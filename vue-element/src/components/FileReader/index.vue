<template>
  <div v-loading="loading" :class="{'FileReader': true}" style="height: calc(100vh - 84px); background-color: #dfdfdf;">

    <table style="width: 100%; height: 100%;">
      <tr style="height: 50px;">
        <td>
          <table style="width: 100%;">
            <tr>
              <td style="width: 116px;">
                <div>
                  <el-button size="mini" icon="el-icon-back" style="border-radius: 100px; height: 42px;" round @click="backCapture" />
                  <el-button size="mini" icon="el-icon-refresh-right" style="border-radius: 100px; height: 42px;" round @click="updateCapture" />
                </div>
              </td>
              <td>
                <el-input :value="names.join('.')" placeholder="Файловый менеджер" onClick="this.select();" />
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <div
            v-if="!languageCapture"
            class="unselectable"
            style="height: 100%; background-color: white; overflow-y: auto;"
            @contextmenu.prevent="contextMenuBoard"
            @click.stop="selectFile(); context_menu.visible = false"
          >
            <div
              v-for="(file, index) in currentCapture"
              :key="index"
              :class="{mini_preview: true, select: select_file === file.id}"
              @dblclick="getCapture({id: file.id, type: file.is_file === 0 ? 'folder' : (file.is_file === 1 ? 'file' : 'key' )}, file)"
              @click.stop="selectFile(file); context_menu.visible = false"
              @contextmenu.prevent="contextMenuFile($event, file)"
              @contextmenu.stop=""
            >
              <div class="icon">
                <svg-icon v-if="file.path && file.is_file === 0" icon-class="folder" style="width: 60px; height: 60px;" />
                <svg-icon v-if="file.path && file.is_file === 1" :icon-class="file.file_type | iconClassFile" style="width: 60px; height: 60px;" />
                <svg-icon v-if="file.indexed" :icon-class="file.translates_count > 0 ? 'language' : 'keys'" style="width: 60px; height: 60px;" />
              </div>
              <div class="name">
                <el-popover
                  placement="left"
                  title="Инфо"
                  width="200"
                  trigger="hover"
                  :content="file.name"
                >
                  <span slot="reference">{{ file.name }}</span>
                </el-popover>
              </div>
            </div>
          </div>
          <div v-else>
            <el-table :data="languageCapture.language">
              <el-table-column prop="name" width="50" />
              <el-table-column prop="description" width="200" />
              <el-table-column>
                <template slot-scope="row">
                  <el-input v-model="row.row.translate" :disabled="row.$index > 0" clearable @change="updateTranslate(row.row)" />
                </template>
              </el-table-column>
            </el-table>
          </div>
        </td>
      </tr>
      <tr style="height: 100px;">
        <td>
          <table v-if="select_file_object" style="width: 100%;">
            <tr>
              <td>Название:</td>
              <td><input :value="select_file_object.name" class="input_data" onClick="this.select();"></td>
            </tr>
            <tr>
              <td>Путь:</td>
              <td><input :value="select_file_object.path" class="input_data" onClick="this.select();"></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <ul v-show="context_menu.visible" :style="{left:context_menu.left+'px',top:context_menu.top+'px', minWidth: '200px'}" class="contextmenu">
      <li v-for="(el, index) in context_menu.$el" :key="index" @click="(el.click ? el.click() : false); context_menu.visible = !el.click">
        {{ el.name }}
      </li>
    </ul>

  </div>
</template>

<script>
import { store, update } from '@/api/api-laravel'
import CreateFileOrFolder from '@/components/FileReader/CreateFileOrFolder'
import PropertiesFolderAndFile from '@/components/FileReader/PropertiesFolderAndFile'
import CreateKeys from '@/components/FileReader/CreateKeys'

export default {
  name: 'FileReader',
  filters: {
    iconClassFile(value) {
      const data = [
        'php',
        'js',
        'json',
        'env'
      ]
      return data[value - 1]
    }
  },
  data() {
    return {
      loading: true, // Загрузка
      currentCapture: null, // то что сейчас на экране
      languageCapture: null, // тут снимок перевода
      select_file: null, // Выделенный файл id
      select_file_object: null, // Выделенный файл object
      history: [], // История перемещений
      names: [], // Имена по очереди
      context_menu: {
        visible: false,
        top: 0,
        left: 0,
        $el: []
      }
    }
  },
  created() {
    this.getCapture()
  },
  methods: {
    updateProperty(file) {
      this.$msgbox({
        title: 'Свойства',
        message: this.$createElement(PropertiesFolderAndFile, {
          props: {
            entity: file,
            callbackUpdate: (e) => {
              update('file', file.id, {
                method: file.path ? 'property' : 'propertyKey',
                next: e
              }).then(res => {
              }).finally(() => {
              })
            }
          }
        }),
        showConfirmButton: false
      })
    },
    backCapture() {
      if (this.history.length >= 2) {
        this.history = this.history.slice(0, this.history.length - 1)
        this.getCapture(this.history[this.history.length - 1])
      }
    },
    updateCapture() {
      this.loading = true
      this.select_file = null
      this.select_file_object = null
      this.$ws('FileReader', this.history[this.history.length - 1]).then(res => {
        if (Array.isArray(res.data)) {
          this.languageCapture = null
          this.currentCapture = res.data
        } else {
          this.languageCapture = res.data || {}
        }
      }).finally(() => {
        this.loading = false
      })
    },
    getCapture(data = {}, file = null) {
      if (file && (file.is_file === 1 || file.indexed) && file.name.indexOf('}') < 1) {
        this.names.push(file.name)
      } else {
        if (this.names.length > 0) {
          this.names = this.names.slice(0, this.names.length - 1)
        }
      }
      this.loading = true
      this.select_file = null
      this.select_file_object = null
      this.$ws('FileReader', data).then(res => {
        if (this.history.length < 1) {
          this.history.push(data)
        }
        if (this.history.length > 0 && this.history[this.history.length - 1] !== data) {
          this.history.push(data)
        }
        if (Array.isArray(res.data)) {
          this.languageCapture = null
          this.currentCapture = res.data
        } else {
          this.languageCapture = res.data || {}
        }
      }).finally(() => {
        this.loading = false
      })
    },
    updateTranslate(translate) {
      this.$alert('Обновить ключ?', 'Внимание', {
        showCancelButton: true,
        confirmButtonText: 'Обновить',
        cancelButtonText: 'Отменить'
      }).then(_ => {
        this.loading = true
        update('translate', translate.translate_id, {
          id: translate.translate_id,
          language_id: translate.id,
          value: translate.translate
        }).then(_ => {
          this.updateCapture()
          this.$message.success('Сохранено')
        }).catch(_ => {
          this.updateCapture()
        })
      })
    },
    contextMenuBoard(event) {
      this.select_file = null
      this.select_file_object = null
      this.context_menu.top = event.clientY
      this.context_menu.left = event.clientX
      this.context_menu.$el = []
      this.context_menu.visible = true

      const board = this.history[this.history.length - 1]

      if (board.type === 'folder') {
        this.context_menu.$el.push({ name: 'Создать файл', click: () => {
          this.$msgbox({
            title: `Создать файл`,
            showConfirmButton: false,
            closeOnClickModal: false,
            message: this.$createElement(CreateFileOrFolder, { props: { typeFile: 2, hook: Math.random(), callback: (e) => {
              this.loading = true
              store('file', { method: 'makeFileDirectory', data: e, parent: board.id }).then(res => { this.updateCapture() }).finally(() => { this.loading = false })
            } }})
          })
        } })
        this.context_menu.$el.push({ name: 'Создать папку', click: () => {
          this.$msgbox({
            title: `Создать папку`,
            showConfirmButton: false,
            closeOnClickModal: false,
            message: this.$createElement(CreateFileOrFolder, { props: { typeFile: 1, hook: Math.random(), callback: (e) => {
              this.loading = true
              store('file', { method: 'makeFileDirectory', data: e, parent: board.id }).then(res => { this.updateCapture() }).finally(() => { this.loading = false })
            } }})
          })
        } })
      } else {
        if (this.history.length > 1) {
          this.context_menu.$el.push({ name: 'Создать ключ', click: () => {
            const file_id = this.history.find(x => x.type === 'file').id
            const keys = this.history.filter(x => x.type === 'key')
            const parent = keys.length > 0 ? keys[keys.length - 1].id : null
            this.$msgbox({
              title: `Создать ключ`,
              showConfirmButton: false,
              closeOnClickModal: false,
              message: this.$createElement(CreateKeys, { props: { typeAdd: 3, hook: Math.random(), callback: (e) => {
                this.loading = true
                store('file', { method: 'makeKey', data: e, parent, file_id }).then(res => { this.updateCapture() }).finally(() => { this.loading = false })
              } }})
            })
          } })
          this.context_menu.$el.push({ name: 'Создать под.ключ', click: () => {
            const file_id = this.history.find(x => x.type === 'file').id
            const keys = this.history.filter(x => x.type === 'key')
            const parent = keys.length > 0 ? keys[keys.length - 1].id : null

            this.$msgbox({
              title: `Создать под.ключ`,
              showConfirmButton: false,
              closeOnClickModal: false,
              message: this.$createElement(CreateKeys, { props: { typeAdd: 4, hook: Math.random(), callback: (e) => {
                this.loading = true
                store('file', { method: 'makeKey', data: e, parent, file_id }).then(res => { this.updateCapture() }).finally(() => { this.loading = false })
              } }})
            })
          } })
        }
      }
    },
    contextMenuFile(event, file) {
      this.selectFile(file)
      this.context_menu.top = event.clientY
      this.context_menu.left = event.clientX
      this.context_menu.$el = []
      this.context_menu.visible = true

      this.context_menu.$el.push({ name: 'Свойства', click: () => { this.updateProperty(file) } })
    },
    selectFile(file = {}) {
      if (file.id) {
        this.select_file = file.id
        this.select_file_object = file
      } else {
        this.select_file = null
        this.select_file_object = null
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.FileReader {
  .mini_preview {
    width: 100px;
    height: 100px;
    margin: 4px;
    display: block;
    cursor: pointer;
    float: left;

    &.select {
      background-color: #dfdfdf;
      border-radius: 7px;
    }

    &:hover {
      background-color: #dfdfdf;
      border-radius: 7px;
    }

    .icon {
      width: 60px;
      height: 60px;
      margin: 0 20px;
    }
    .name {
      width: 100px;
      height: 40px;
      text-align: center;
      /*word-wrap: break-word;*/
      overflow: hidden;
      margin: 1px;
      font-size: 13px;
      line-height: 22px;
      text-overflow: ellipsis;
    }
  }
}

.contextmenu {
    margin: 0;
    background: #fdfdfd;
    z-index: 3000;
    position: fixed;
    list-style-type: none;
    padding: 5px 0;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 400;
    color: #333;
    box-shadow: 2px 2px 3px 0 rgba(0, 0, 0, .3);
    li {
      margin: 0;
      padding: 7px 16px;
      cursor: pointer;
      &:hover {
        background: #eee;
      }
    }
}
.input_data:focus {outline:none!important; width: 100%; border: none; box-shadow: none;}
.input_data {border:0;outline:0; width: 100%; box-shadow: none;}
</style>
