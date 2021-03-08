<template>
  <div v-loading="loading" class="app-container">

    <header-sticky title="Перевод">
      <template slot="prepend">
        <!-- -->
        <el-button icon="el-icon-search" @click="search_visible = true">Поиск</el-button>
      </template>
    </header-sticky>

    <!-- ----------------------------------------------------------- -->

    <div style="min-width: 100%;">
      <el-select v-model="filter.select" size="mini" placeholder="Какие ключи показать" clearable style="float: left;">
        <el-option value="all" label="Все переводы" />
        <el-option value="is_not_translate" label="Без перевода" />
        <el-option value="is_translate" label="Только переведенные" />
      </el-select>
      <!-- ----------------------------------------------------------- -->
      <el-select v-model="filter.language" size="mini" multiple collapse-tags clearable style="float: left;">
        <el-option v-for="(language, index) in languages" :key="index" :label="`${language.description} [${language.name}]`" :value="language.id" />
      </el-select>
      <!-- ----------------------------------------------------------- -->
      <el-button type="success" size="mini" @click="getTranslate">Показать</el-button>
    </div>

    <!-- ----------------------------------------------------------- -->

    <el-table :data="translations.data">
      <el-table-column v-for="(language, index) in languages_c" :key="index" :label="language.id ? `${language.description} [${language.name}]` : ''">

        <!-- ----------------------------------------------------------- -->

        <template slot-scope="row">
          <div v-if="language.id">
            <el-col style="cursor: pointer;">

              <el-popover
                v-if="canUserUpdate('0' + language.id)"
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
                        style="width: 100%; margin: 2px 9px 15px 9px;"
                        @click="no_saveTranslate($event, index + '-' + language.id + '-' + row.$index)"
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
                <span slot="reference">
                  <div v-if="row.row['0' + language.id]" class="is_translate">
                    {{ row.row['0' + language.id] }}
                  </div>
                  <div v-else class="is_not_translate" />
                </span>

              </el-popover>

              <div v-else>
                <div v-if="row.row['0' + language.id]" class="is_translate_no_update">
                  {{ row.row['0' + language.id] }}
                </div>
                <div v-else class="is_not_translate_no_update" />
              </div>

            </el-col>
          </div>

          <!-- ----------------------------------------------------------- -->

          <div v-else>
            <el-col>
              {{ row.row.name }}
            </el-col>
            <el-col style="font-size: 12px;">
              {{ row.row.description }}
            </el-col>
          </div>
        </template>

        <!-- ----------------------------------------------------------- -->

      </el-table-column>
    </el-table>

    <search :filter="['App\\Models\\Translate','App\\Models\\Key', 'App\\Models\\File']" :visible.sync="search_visible" @model="(e) => filter.model_id = e" />

    <div class="el-pagination-container">
      <el-pagination
        background
        layout="total, sizes, prev, pager, next"
        :current-page.sync="translations.meta.current_page"
        :total="translations.meta.total"
        :page-sizes="[10, 20, 40]"
        @size-change="(e) => {query_back.to = e;getTranslate()}"
        @current-change="(e) => {query_back.page = e; getTranslate()}"
      />
    </div>
  </div>
</template>

<script>
import Search from '@/components/Search/search'
import { watcherQuery } from '@/query/index'
import { list, update } from '@/api/api-laravel'

export default {
  name: 'Translate',
  components: { Search },
  data() {
    return {
      query_back: {
        to: 10,
        page: 1
      },
      filter: { select: '', language: [], model_id: 0 },
      show_name_keys: false,
      search_visible: false,
      translations: {
        data: [],
        meta: {
          current_page: 1,
          total: 0
        }
      },
      languages: [],
      value: '',
      loading: true
    }
  },
  computed: {
    languages_c() {
      if (this.filter.language.length && this.filter.language.length !== this.languages.length) {
        return [{ id: 0 }, ...this.languages.filter(x => this.filter.language.indexOf(x.id) >= 0)]
      }
      return [{ id: 0 }, ...this.languages]
    }
  },
  watch: {
    'filter': watcherQuery
  },
  query(query) {
    console.error(query)
  },
  created() {
    list('language').then(res => {
      this.languages = res.data || []
      if (this.filter.language.length < 1) {
        this.filter.language = this.languages.map(x => x.id)
      }
      this.getTranslate()
    })
  },
  methods: {
    canUserUpdate(lang_id) {
      if (this.$store.getters.access.root) {
        return true
      }
      if (this.$store.getters.access && this.$store.getters.access.translate && this.$store.getters.access.translate.update) {
        return this.$store.getters.access.translate.update.indexOf(lang_id) >= 0
      }
      return false
    },
    getTranslate() {
      this.loading = true
      const query = {
        to: this.query_back.to,
        page: this.query_back.page,
        filter: this.filter
      }

      list('translate', query).then(res => {
        this.translations = res.data || {}
        document.querySelector('.el-pagination__total').innerHTML = 'Кол-во: ' + this.translations.meta.total
      }).finally(_ => {
        this.loading = false
      })
    },
    keyPress(ev, refs, index, lang, entity_id, index2) {
      ev.target.blur()
      this.$alert('Перенос строки запрещен. Вы хотели сохранить?', 'Внимание', {
        confirmButtonText: 'OK',
        type: 'error'
      }).then(_ => {
        const value = this.clearValue(this.value)
        this.translations.data[index]['0' + lang] = value
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
      this.translations.data[index]['0' + lang] = value
      this.translations = Object.assign(this.translations)
      this.sendServer(entity_id, lang, value)
      this.closePopover(refs)
      setTimeout(() => {
        this.nextTranslate(index2, lang, index)
      }, 400)
    },
    no_saveTranslate(ev, refs) {
      this.value = ''
      this.closePopover(refs)
    },
    clearTranslate(ev, refs, index, lang, entity_id) {
      this.translations.data[index]['0' + lang] = null
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
        this.getTranslate()
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
    /*background-color: #f9f8f8;*/
    resize: none;
    &::-moz-placeholder { color: #f57171; }
    &::-webkit-input-placeholder { color: #f57171; }
  }
}
.is_translate {
  font-style: italic;
  color: #4b4b99;
  &:hover {
    color: #d00;
    cursor: pointer;
  }
}
.is_translate_no_update {
  font-style: italic;
  color: #4b4b99;
  &:hover {
    cursor: default;
  }
}
.is_not_translate {
  background: #ff9c9c;
  padding: 23px;
  border: 2px dashed #000000;
  border-radius: 7px;
}
.is_not_translate_no_update {
  border: 2px dashed #c1c1c1;
  border-radius: 7px;
  background: #e6e6e6;
  padding: 23px;
  cursor: default;
}
</style>
