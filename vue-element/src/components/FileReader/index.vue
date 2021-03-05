<template>
  <div v-loading="count_loading > 0" style="background-color: #d4d4d4;display: flow-root; height: calc(100vh - 84px);" class="unselectable file-reader" @contextmenu.prevent="contextmenu('plane', $event)">
    <div style="min-width: 100%; padding: 5px;">
      <div class="el-col-24">
        <table style="width: 100%;">
          <tr>
            <td style="width: 116px;">
              <div>
                <el-button size="mini" icon="el-icon-back" style="border-radius: 100px; height: 42px;" round :disabled="back_current.length < 1" @click="back()" />
                <el-button size="mini" icon="el-icon-refresh-right" style="border-radius: 100px; height: 42px;" round @click="hardReplayAllFiles" />
              </div>
            </td>
            <td>
              <el-input :value="history.join(' / ').replace('/ /', '/')" placeholder="Файловый менеджер" />
            </td>
            <td style="width: 206px;">
              <el-radio-group v-model="type_view" style="float: right;">
                <el-radio-button label="В клетку" />
                <el-radio-button label="Списком" />
              </el-radio-group>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div v-if="back_current.length && type_view === 'В клетку'" class="file" @dblclick="back()">
      <svg-icon icon-class="folder" style="width: 60px; height: 50px;" />
      <div class="name unselectable">...</div>
    </div>
    <div v-if="back_current.length && type_view === 'Списком'" class="file-list" @dblclick="back()">
      <svg-icon icon-class="folder" style="width: 30px; height: 30px;" />
      <div class="name unselectable">...</div>
    </div>

    <span v-for="(file, index) in current" :key="index">
      <file
        v-if="file && file.id"
        :file="file"
        :type-view-list="type_view"
        @view-files="setCurrent"
        @current-file="setCurrentFile"
        @history="setHistory"
        @contextmenu.prevent="contextmenu('entity', $event, file)"
      />
    </span>

    <ul v-show="context_menu.visible" :style="{left:context_menu.left+'px',top:context_menu.top+'px', minWidth: '200px'}" class="contextmenu">
      <li
        v-for="(el, index) in context_menu.$el.filter(x => x)"
        :key="index"
        :class="el.name === 'separator' ? 'separator' : ''"
        data-no-event="true"
        @click="(el.click ? el.click() : false); context_menu.visible = !el.click"
      >
        {{ el.name }}
      </li>
    </ul>

  </div>
</template>

<script>
import { destroy, list, update, store } from '@/api/api-laravel'
import File from '@/components/FileReader/File'
import CreateFileOrFolder from '@/components/FileReader/CreateFileOrFolder'
// import { recursive_add_file, recursive_remove_file } from '@/components/FileReader/CreateFileOrFolder'
import { recursive_add_file } from '@/components/FileReader/CreateFileOrFolder'
import PropertiesFolderAndFile from '@/components/FileReader/PropertiesFolderAndFile'
// import { recursive_update_entity, current_update_entity } from '@/components/FileReader/PropertiesFolderAndFile'
import CreateKeys from '@/components/FileReader/CreateKeys'
import { recursive_add_key } from '@/components/FileReader/CreateKeys'

export default {
  name: 'FileReader',
  components: { File },
  provide() {
    return {
      languages: () => this.languages
    }
  },
  data() {
    return {
      languages: [],
      count_loading: 0,
      type_view: 'В клетку',
      files: [],
      current: [],
      current_file: null,
      back_current: [],
      history: [],
      block_context: false,
      copy_buffer: null,
      context_menu: {
        visible: false,
        top: 0,
        left: 0,
        $el: []
      }
    }
  },
  watch: {
    type_view() {
      this.current = Object.assign(this.current)
    }
  },
  created() {
    this.getFiles()
    this.getLanguage()
  },
  mounted() {
    document.querySelector('div.file-reader').addEventListener('click', this.globalEventClick)
  },
  beforeDestroy() {
    try {
      document.querySelector('div.file-reader').removeEventListener('click', this.globalEventClick)
    } catch (ignore) { /**/ }
  },
  methods: {
    getLanguage() {
      list('language').then(res => {
        this.languages = res.data || []
      })
    },
    hardReplayAllFiles() {
      const old_history = JSON.parse(JSON.stringify(this.history))
      this.count_loading++
      list('file').then(res => {
        this.back_current = []
        this.history = []
        this.files = res.data || []
        this.current = res.data || []
        const recursive_update = (files, history) => {
          files.forEach(file => {
            if (file.name === history[0]) {
              // переходы
              if (file.files && file.files.length) {
                this.setCurrent(file.files)
                this.currentFile(file)
                this.setHistory(file.name, 1)
                if (history.slice(1).length > 0) {
                  recursive_update(file.files, history.slice(1))
                }
              } else {
                if (file.keys && file.keys.length) {
                  this.setCurrent(file.keys)
                  this.currentFile(file)
                  this.setHistory(file.name, 2)
                  if (history.slice(1).length > 0) {
                    recursive_update(file.keys, history.slice(1))
                  }
                }
              }
            }
          })
        }
        recursive_update(this.files, old_history)
      }).finally(_ => {
        this.count_loading--
      })
    },
    async copyFile() {
      const recursive_fun = (files = [], copy_buffer) => {
        return new Promise(resolve => {
          files.forEach(file => {
            if (file.name === copy_buffer.name && file.description === copy_buffer.description) {
              resolve(file)
            } else {
              if (file.files) {
                recursive_fun(file.files, copy_buffer).then(res => {
                  resolve(res)
                })
              }
              if (file.keys) {
                recursive_fun(file.keys, copy_buffer).then(res => {
                  resolve(res)
                })
              }
            }
          })
        })
      }

      const file_paste = await recursive_fun(this.files, this.current_file)

      const data = {
        method: 'copyMove',
        // Это ключ или файл
        entity_type: this.copy_buffer.indexed ? 'Key' : 'File',
        // Ид родителя
        parent: this.current_file.id,
        // вставить в ключ или файл
        parent_type: this.current_file.indexed ? 'Key' : 'File',
        // вырезать или копировать
        move: !!this.copy_buffer.is_cut
      }

      if ((file_paste.files && file_paste.files.findIndex(x => x.name === this.copy_buffer.name) >= 0) ||
          (file_paste.keys && file_paste.keys.findIndex(x => x.name === this.copy_buffer.name) >= 0)) {
        data.entity_new_name = this.copy_buffer.name + '_' + (Math.random() * 100000).toFixed(0)
      }

      this.count_loading++
      update('file', this.copy_buffer.id, data).then(async res => {
        this.hardReplayAllFiles()
        /* const file_copy = await recursive_fun(this.files, this.copy_buffer)

        if (this.copy_buffer.indexed && (this.current_file.is_file || this.copy_buffer.indexed)) {
          this.current.push(JSON.parse(JSON.stringify(file_copy)))
          file_paste.keys.push(JSON.parse(JSON.stringify(file_copy)))
        } else {
          this.current.push(JSON.parse(JSON.stringify(file_copy)))
          file_paste.files.push(JSON.parse(JSON.stringify(file_copy)))
        }

        if (this.copy_buffer.is_cut) {
          file_copy.disable = true
        }*/
        this.copy_buffer = null
      }).finally(_ => {
        this.count_loading--
      })
    },
    deleteFile(data) {
      this.$alert('Удалить файл?', 'Внимание', {
        confirmButtonText: 'Да удалить!',
        cancelButtonText: 'Отменить',
        showCancelButton: true
      }).then(res => {
        this.$alert('Точно удалить файл? его можно переместить или переименовать!', 'Внимание', {
          confirmButtonText: 'Да удалить!',
          cancelButtonText: 'Отменить',
          showCancelButton: true
        }).then(res => {
          this.count_loading++
          destroy('file', data.id, {
            method: data.indexed ? 'deleteKey' : 'deleteFile'
          }).then(res => {
            this.hardReplayAllFiles()
            /*            this.current = this.current.filter(x => x.id !== data.id)
            recursive_remove_file(this.files, data.id, !data.indexed)*/
          }).finally(_ => {
            this.count_loading--
          })
        })
      })
    },
    makeFolderKeyFile(type = 1) {
      return () => {
        if (type === 1 || type === 2) {
          this.$msgbox({
            title: `Создать ${type === 1 ? 'папку' : 'файл'}`,
            showConfirmButton: false,
            message: this.$createElement(CreateFileOrFolder, {
              props: {
                typeFile: type,
                hook: Math.random(),
                callback: async(e) => {
                  const file_paste = await recursive_add_file(this.files, this.current_file)
                  if (file_paste.files.findIndex(x => x.name === e.name) >= 0) {
                    this.$alert('В этой папке уже есть файл с таким именем', '', { confirmButtonText: 'Хорошо' })
                    return false
                  }
                  this.count_loading++
                  store('file', {
                    method: 'makeFileDirectory',
                    data: e,
                    parent: this.current_file.id
                  }).then(res => {
                    this.hardReplayAllFiles()
                    // file_paste.files.push(res.data)
                    // this.current.push(res.data)
                  }).finally(_ => {
                    this.count_loading--
                  })
                }
              }
            })
          })
        } else {
          this.$msgbox({
            title: `Создать ключ`,
            showConfirmButton: false,
            message: this.$createElement(CreateKeys, {
              props: {
                hook: Math.random(),
                typeAdd: type,
                callback: async(e) => {
                  const file_paste = await recursive_add_key(this.files, this.current_file)
                  if (file_paste.keys.findIndex(x => x.name === e.name) >= 0) {
                    this.$alert('В этой папке уже есть ключ с таким именем', '', { confirmButtonText: 'Хорошо' })
                    return false
                  }

                  const parent = this.current_file.path ? null : this.current_file.id
                  const file_id = this.current_file.path ? this.current_file.id : this.current_file.file_id
                  this.count_loading++
                  store('file', {
                    method: 'makeKey',
                    data: e,
                    parent,
                    file_id
                  }).then(res => {
                    this.hardReplayAllFiles()
                    // file_paste.keys.push(res.data)
                    // this.current.push(res.data)
                  }).finally(_ => {
                    this.count_loading--
                  })
                }
              }
            })
          })
        }
      }
    },
    plane(type = 'plane', event, data = null) {
      if (this.history.length && this.current_file) {
        const type_entity = this.current_file.is_file ? 'Текущий файл' : (this.current_file.indexed ? 'Текущий ключ' : 'Текущая папка')
        this.context_menu.$el = [
          { name: `${type_entity}: ${this.history[this.history.length - 1]}` }
        ]
        // Копирование всего чего угодно
        if (this.copy_buffer && (this.copy_buffer.is_file === 0 || this.copy_buffer.is_file === 1) && this.current_file.is_file === 0) {
          this.context_menu.$el.push({ name: `Вставить ${this.copy_buffer.name} сюда`, click: this.copyFile })
        }
        if (this.copy_buffer && this.copy_buffer.indexed && (this.current_file.is_file === 1 || this.current_file.indexed)) {
          this.context_menu.$el.push({ name: `Вставить ключ ${this.copy_buffer.name} сюда`, click: this.copyFile })
        }

        if (type_entity === 'Текущая папка') {
          this.context_menu.$el.push({
            name: 'Создать папку', click: this.makeFolderKeyFile(1)
          })
          this.context_menu.$el.push({
            name: 'Создать файл', click: this.makeFolderKeyFile(2)
          })
          if (this.current_file.name !== '/') {
            this.context_menu.$el.push({
              name: 'Свойства', click: () => {
                this.$msgbox({
                  title: 'Свойства',
                  message: this.$createElement(PropertiesFolderAndFile, { props: { entity: this.current_file }}),
                  showConfirmButton: false
                })
              }
            })
          }
        } else {
          this.context_menu.$el.push({
            name: 'Создать ключ', click: this.makeFolderKeyFile(3)
          })
          this.context_menu.$el.push({
            name: 'Создать под ключ', click: this.makeFolderKeyFile(4)
          })
        }
      } else {
        this.context_menu.$el = [
          {
            name: `Тут нельзя ничего изменять`,
            click: () => {
              this.$alert('К сожалению в корневой папке нельзя ничего менять поэтому перейдите в любую дочернюю', 'Внимание', { confirmButtonText: 'Хорошо' })
            }
          }
        ]
      }
    },
    entity(type = 'entity', event, data = null) {
      const type_file = data.is_file ? 'Файл' : (data.indexed ? 'Ключ' : 'Папка')

      if (data.name === '/') {
        this.context_menu.$el = [
          { name: `${type_file}: ${data.name}` }
        ]
      } else {
        this.context_menu.$el = [
          { name: `${type_file}: ${data.name}` },
          { name: 'Копировать', click: () => { this.copy_buffer = data } },
          { name: 'Вырезать', click: () => { this.copy_buffer = { ...data, is_cut: true } } },
          { name: 'separator' },
          { name: 'Удалить', click: () => { this.deleteFile(data) } }
        ]
      }

      if (type_file === 'Ключ' && data.translate) {
        this.context_menu.$el.splice(1, 0, {
          name: 'Сделать под ключём', click: () => {
            this.$alert('Это удалит все вложенные ключи если они есть! Для создания подключей', 'Внимание', {
              confirmButtonText: 'Продолжить',
              showCancelButton: true,
              cancelButtonText: 'Отменить'
            }).then(res => {
              this.current_file = data
              this.makeFolderKeyFile(3)()
            })
          }
        })
      }

      if (type_file === 'Ключ') {
        this.context_menu.$el = this.context_menu.$el.filter(x => x.name !== 'Копировать')
      }

      if (data.name !== '/') {
        this.context_menu.$el.push({
          name: 'Свойства', click: () => {
            this.$msgbox({
              title: 'Свойства',
              message: this.$createElement(PropertiesFolderAndFile, {
                props: {
                  entity: data,
                  callbackUpdate: (e) => {
                    this.count_loading++
                    update('file', data.id, {
                      method: data.path ? 'property' : 'propertyKey',
                      next: e
                    }).then(res => {
                      this.hardReplayAllFiles()
                      // current_update_entity(this.current, data, e)
                      // recursive_update_entity(this.files[0], data, e)
                    }).finally(_ => {
                      this.count_loading--
                    })
                  }
                }
              }),
              showConfirmButton: false
            })
          }
        })
      }
    },
    contextmenu(type = 'plane', event, data = null) {
      if (!this.block_context) {
        this.block_context = setTimeout(() => {
          clearTimeout(this.block_context)
          this.block_context = null
        }, 700)

        this.context_menu.top = event.clientY + 30
        this.context_menu.left = event.clientX - 100

        if (type === 'plane') {
          this.plane(type, event, data)
          this.context_menu.visible = true
        }
        if (type === 'entity') {
          this.entity(type, event, data)
          this.context_menu.visible = true
        }
      }
    },
    getFiles() {
      this.count_loading++
      list('file').then(res => {
        this.files = res.data || []
        this.current = res.data || []
      }).finally(_ => {
        this.count_loading--
      })
    },
    setCurrentFile(entity) {
      this.current_file = entity
    },
    setCurrent(entity) {
      this.back_current.push(this.current)
      this.current = entity
    },
    setHistory(history, other = null) {
      this.history.push(history)
    },
    currentFile(data) {
      const _data = JSON.parse(JSON.stringify(data))
      _data.files = null
      _data.keys = null
      _data.translate = null
      this.current_file = _data
    },
    back() {
      this.current = this.back_current[this.back_current.length - 1]
      this.back_current = this.back_current.slice(0, this.back_current.length - 1)
      this.history = this.history.slice(0, this.history.length - 1)

      if (this.back_current.length >= 2) {
        this.currentFile(this.back_current[this.back_current.length - 1].find(x => x.name === this.history[this.history.length - 1]))
      } else {
        this.currentFile(this.files[0])
      }
    },
    globalEventClick(ev) {
      if (ev.target.getAttribute('data-no-event') !== 'true') {
        this.context_menu.visible = false
      }
    }
  }
}
</script>

<style scoped lang="scss">
.file-list {
  cursor: pointer;
  height: 50px;
  background-color: #ffffff;
  border-bottom: 1px solid #cccccc;
  padding-left: 20px;
  display: flex;
  align-items: center;
  &:hover {
    background-color: rgba(0,0,0,.1);
  }
  &:focus {
    background-color: rgba(0,0,0,.1);
  }
  svg {
    float: left;
  }
  .name {
    padding-left: 20px;
    float: left;
    height: 100%;
    display: flex;
    align-items: center;
  }
}
.file {
  cursor: pointer;
  float: left;
  margin: 20px;
  padding: 5px;
  width: 70px;
  height: 100px;
  &:hover {
    background-color: rgba(0,0,0,.2);
  }
  &:focus {
    background-color: rgba(0,0,0,.2);
  }
  .name {
    width: 100%;
    text-align: center;
    overflow: hidden;
    font-size: 13px;
    word-break: break-all;
  }
}
.unselectable {
  -webkit-touch-callout: none; /* iOS Safari */
  -webkit-user-select: none;   /* Chrome/Safari/Opera */
  -khtml-user-select: none;    /* Konqueror */
  -moz-user-select: none;      /* Firefox */
  -ms-user-select: none;       /* Internet Explorer/Edge */
  user-select: none;           /* Non-prefixed version, currently
                                  not supported by any browser */
}
.contextmenu {
  margin: 0;
  background: #fff;
  z-index: 3000;
  position: absolute;
  list-style-type: none;
  padding: 5px 0;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 400;
  color: #333;
  box-shadow: 2px 2px 3px 0 rgba(0, 0, 0, .3);
  li.separator {
    background-color: #ccc;
    height: 2px;
    padding: 0px;
    overflow: hidden;
  }
  li:nth-child(1) {
    background: #eee;
    margin: 4px;
    border-radius: 4px;
  }
  li {
    margin: 0;
    padding: 7px 16px;
    cursor: pointer;
    &:hover {
      background: #eee;
    }
  }
}
</style>
