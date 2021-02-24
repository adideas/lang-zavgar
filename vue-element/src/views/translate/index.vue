<template>
  <div v-loading="loading">
    <el-table :data="translations">
      <el-table-column
        v-for="(language, index) in [{id:0}, ...languages]"
        :key="index"
        :label="language.id ? language.description + ` [${language.name}]` : ''"
      >
        <template slot-scope="row">
          <div v-if="language.id">
            <el-col style="cursor: pointer;">

              <el-popover
                :ref="index + '-' + language.id + '-' + row.$index"
                placement="bottom"
                width="300"
                trigger="click"
                @show="value = row.row['0' + language.id]"
              >
                <div class="container">
                  <div class="el-col-18 my_custom_input">
                    <el-input
                      v-model="value"
                      type="textarea"
                      placeholder="Введите перевод"
                      autofocus
                      @keyup.enter.native="keyPress($event, index + '-' + language.id + '-' + row.$index, row.$index, language.id, row.row.id, index)"
                    />
                  </div>
                  <div class="el-col-5">
                    <el-tooltip class="item" effect="dark" content="Применить" placement="right">
                      <el-button
                        size="mini"
                        type="success"
                        icon="el-icon-check"
                        style="width: 100%; margin: 2px 9px 9px 9px;"
                        @click="saveTranslate($event, index + '-' + language.id + '-' + row.$index, row.$index, language.id, row.row.id, index)"
                      />
                    </el-tooltip>

                    <el-tooltip class="item" effect="dark" content="Отменить" placement="right">
                      <el-button
                        size="mini"
                        type="warning"
                        icon="el-icon-close"
                        plain
                        style="width: 100%; margin: 2px 9px 15px 9px;"
                        @click="nosaveTranslate($event, index + '-' + language.id + '-' + row.$index)"
                      />
                    </el-tooltip>

                    <el-tooltip class="item" effect="dark" content="Очистить ключ" placement="right">
                      <el-button
                        size="mini"
                        type="danger"
                        icon="el-icon-delete"
                        style="width: 100%; margin: 9px; margin-top: 20px;"
                        @click="clearTranslate($event, index + '-' + language.id + '-' + row.$index, row.$index, language.id, row.row.id)"
                      />
                    </el-tooltip>
                  </div>
                </div>
                <span slot="reference" style="border-bottom-style: dotted;">
                  <span v-if="row.row['0' + language.id]">
                    {{ row.row['0' + language.id] }}
                  </span>
                  <div v-else>
                    __________________
                  </div>
                </span>

              </el-popover>

            </el-col>
          </div>
          <div v-else>
            <el-col>
              {{ row.row.name }}
            </el-col>
            <el-col style="font-size: 12px;">
              {{ row.row.description }}
            </el-col>
          </div>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import { list, update } from '@/api/api-laravel'

export default {
  name: 'Translate',
  data() {
    return {
      translations: [],
      languages: [],
      keys: [],
      value: '',
      loading: true
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.loading = true
      list('translate').then(res => {
        this.translations = res.data.data || []
      }).finally(_ => {
        this.loading = false
      })
      list('language').then(res => {
        this.languages = res.data || []
      })
      list('key').then(res => {
        this.keys = res.data || []
      })
    },
    keyPress(ev, refs, index, lang, entity_id, index2) {
      ev.target.blur()
      this.$alert('Перенос строки запрещен. Вы хотели сохранить?', 'Внимание', {
        confirmButtonText: 'OK',
        type: 'error'
      }).then(_ => {
        const value = this.clearValue(this.value)
        this.translations[index]['0' + lang] = value
        this.translations = Object.assign(this.translations)
        this.sendServer(entity_id, lang, value)
        this.closePopover(refs)
        setTimeout(() => {
          this.nextTranslate(index2, lang, index)
        }, 400)
      })
    },
    saveTranslate(ev, refs, index, lang, entity_id, index2) {
      const value = this.clearValue(this.value)
      this.translations[index]['0' + lang] = value
      this.translations = Object.assign(this.translations)
      this.sendServer(entity_id, lang, value)
      this.closePopover(refs)
      setTimeout(() => {
        this.nextTranslate(index2, lang, index)
      }, 400)
    },
    nosaveTranslate(ev, refs) {
      this.value = ''
      this.closePopover(refs)
    },
    clearTranslate(ev, refs, index, lang, entity_id) {
      this.translations[index]['0' + lang] = null
      this.translations = Object.assign(this.translations)
      this.sendServer(entity_id, lang, null)
      this.closePopover(refs)
    },
    closePopover(refs) {
      if (this.$refs[refs] && this.$refs[refs].length === 1) {
        this.$refs[refs][0].doClose()
      }
    },
    clearValue(value) {
      return value.replace(/[~,`,@,\",\',$,%,^,\r,\\,\/,\t,&,\},\{,\[,\]]*/g, '').replace(/[\n]/g, ' ')
    },
    sendServer(id, lang, value) {
      update('translate', id, {
        id,
        language_id: lang,
        value
      }).then(_ => {
        this.$message.success('Сохранено')
      }).catch(_ => {
        this.getList()
      })
    },
    nextTranslate(a, b, c) {
      const refs = a + '-' + b + '-' + (c + 1)
      if (this.$refs[refs] && this.$refs[refs].length === 1) {
        this.$refs[refs][0].doShow()
        try {
          this.$nextTick(() => {
            this.$refs[refs][0].$children[0].$el.getElementsByTagName('textarea')[0].focus()
          })
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
}
</script>

<style lang="scss">
.my_custom_input {
  textarea {
    min-height: 140px!important;
    background-color: #f9f8f8;
    resize: none;
  }
}
</style>
