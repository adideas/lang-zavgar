<template>
  <div style="background-color: #2b2b2b; display: flow-root;">

    <div v-if="files.length" style="width: calc(100vw / 2 - 50px); background-color: #2b2b2b; display: block; padding: 5px; height: calc(100vh - 84px); overflow: auto; float: left;">
      <div style="color: #e4e4e4;">
        // Файлы
      </div>
      <tree-view v-for="(file, index) in files" :key="index" :index="index" :data="file" :clear-click="clearClick" @click="click" @contextmenu="contextmenu" />
    </div>

    <div v-if="files_delete.length" style="width: calc(100vw / 2 - 50px); background-color: #2b2b2b; display: block; padding: 5px; height: calc(100vh - 84px); overflow: auto; float: left;">
      <div style="color: #e4e4e4;">
        // Удалённые файлы
      </div>
      <tree-view v-for="(file, index) in files_delete" :key="index" path :index="index" :data="file" :clear-click="clearClick" @contextmenu="contextmenu_delete" />
    </div>

    <settings-file
      v-if="files_context_menu_config.dialog_settings"
      :visible.sync="files_context_menu_config.dialog_settings"
      :data="files_context_menu_config.el"
      @update="getFiles"
    />
    <!-- files context menu -->
    <!-- files context menu -->
    <!-- files context menu -->
    <ul v-show="files_context_menu_config.visible" :style="{left:files_context_menu_config.left+'px',top:files_context_menu_config.top+'px'}" class="contextmenu">
      <li data-no-event="true">{{ files_context_menu_config.name }}</li>
      <li v-if="!files_context_menu_config.deleted && check_file(files_context_menu_config.el)" data-no-event="true">Приступить к переводу</li>
      <li data-no-event="true" @click="settings">Свойства</li>
      <li v-if="!check_file(files_context_menu_config.el)" data-no-event="true" @click="createFinder(files_context_menu_config.el.data.id)">Создать</li>

      <li v-if="!files_context_menu_config.deleted" data-no-event="true" class="separator" />
      <li v-if="!files_context_menu_config.deleted" data-no-event="true" @click="rePath(files_context_menu_config.el.data)">Переместить</li>
      <li v-if="!files_context_menu_config.deleted" data-no-event="true" @click="delete_file(files_context_menu_config.el.data.id)">Удалить</li>
      <li v-if="files_context_menu_config.deleted" data-no-event="true" @click="restore_file(files_context_menu_config.el.data.id)">Востановить</li>
    </ul>
    <!-- dialog path -->
    <!-- dialog path -->
    <!-- dialog path -->
    <el-dialog :visible.sync="files_context_menu_config.dialog_path" title="Переместить">
      <el-form>
        <el-form-item label="Куда">
          <el-cascader v-model="files_context_menu_config.path_parent_id" class="el-col-24" :options="folder" :props="files_context_menu_config.dialog_path_props" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" style="float: right;" @click="rePathApply">Переместить</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>

    <el-dialog :visible.sync="createFile.visible" title="Создание файла">
      <el-form>
        <el-form-item label="Имя">
          <el-input v-model="createFile.name" />
        </el-form-item>
        <el-form-item label="Описание">
          <el-input v-model="createFile.description" />
        </el-form-item>
        <el-form-item label="Папка / файл">
          <el-select v-model="createFile.is_file" class="el-col-24">
            <el-option :value="0" label="Папка" />
            <el-option :value="1" label="Файл" />
          </el-select>
        </el-form-item>
        <el-form-item v-if="createFile.is_file" label="Тип файла">
          <el-select v-model="createFile.file_type" class="el-col-24">
            <el-option :value="1" label="PHP" />
            <el-option :value="2" label="JavaScript" />
            <el-option :value="3" label="JSON" />
            <el-option :value="4" label="ENV" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="createFinder(createFile, true)">Сохранить</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import { destroy, list, update, store } from '@/api/api-laravel'
import TreeView from '@/views/files/components/TreeView'
import SettingsFile from '@/views/files/components/SettingsFile'

export default {
  name: 'Files',
  components: { SettingsFile, TreeView },
  data() {
    return {
      files: [],
      folder: [],
      files_delete: [],
      clearClick: false,
      languages: null,
      files_context_menu_config: {
        deleted: false,
        visible: false,
        name: '',
        left: 0,
        top: 0,
        el: null,
        dialog_settings: false,
        dialog_path: false,
        dialog_path_props: {
          value: 'id',
          label: 'name',
          checkStrictly: true
        },
        path_parent_id: ''
      },
      createFile: {
        visible: false,
        name: '',
        description: '',
        is_file: 0,
        file_type: null
      }
    }
  },
  created() {
    this.getFiles()
  },
  mounted() {
    document.querySelector('section.app-main').addEventListener('click', this.globalEventClick)
  },
  beforeDestroy() {
    document.querySelector('section.app-main').removeEventListener('click', this.globalEventClick)
  },
  methods: {
    globalEventClick(ev) {
      if (ev.target.getAttribute('data-no-event') !== 'true') {
        this.files_context_menu_config.visible = false
      }
    },
    getFiles() {
      list('file').then(res => {
        this.files = res.data || []

        const folder = [...JSON.parse(JSON.stringify(this.files))]

        function folder_check(folder) {
          if (folder.is_file) {
            return null
          }
          if (folder.children) {
            folder.children = folder.children.map(e => folder_check(e))
            if (!folder.children[0]) {
              folder.children = null
            }
          }
          return folder
        }

        this.folder = folder.map(e => folder_check(e))
      })
      list('file', { only: 'delete' }).then(res => {
        this.files_delete = res.data || []
      })
    },
    click(event) {
      this.clearClick = !this.clearClick
    },
    contextmenu(event) {
      this.clearClick = !this.clearClick
      this.files_context_menu_config.deleted = false
      this.files_context_menu_config.top = event.$event.clientY
      this.files_context_menu_config.left = event.$event.clientX
      this.files_context_menu_config.name = 'file_name: ' + event.data.name
      this.files_context_menu_config.el = event
      this.files_context_menu_config.visible = true
    },
    contextmenu_delete(event) {
      this.clearClick = !this.clearClick
      this.files_context_menu_config.deleted = true
      this.files_context_menu_config.top = event.$event.clientY
      this.files_context_menu_config.left = event.$event.clientX
      this.files_context_menu_config.name = 'file_name: ' + event.data.name
      this.files_context_menu_config.el = event
      this.files_context_menu_config.visible = true
    },
    settings() {
      this.files_context_menu_config.visible = false
      this.files_context_menu_config.dialog_settings = true
    },
    delete_file(id) {
      this.files_context_menu_config.visible = false
      this.$alert('Удалить файл?', 'Внимание', {
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена',
        showCancelButton: true,
        type: 'error'
      }).then(res => {
        destroy('file', id).then(res => {
          this.$message.success('Удалено')
          this.getFiles()
        })
      })
    },
    check_file(file) {
      return file && file.data && file.data.is_file
    },
    rePath($data) {
      this.files_context_menu_config.visible = false
      this.files_context_menu_config.dialog_path = true
      this.files_context_menu_config.path_parent_id = null
    },
    createFinder(data, create = false) {
      if (create) {
        store('file', data).then(res => {
          this.createFile.visible = false
          this.getFiles()
        })
      } else {
        this.createFile = {
          visible: false,
          name: '',
          description: '',
          is_file: 0,
          file_type: null
        }
        this.files_context_menu_config.visible = false
        this.createFile.parent = data
        this.createFile.visible = true
      }
    },
    rePathApply() {
      this.files_context_menu_config.dialog_path = false
      update('file', this.files_context_menu_config.el.data.id, {
        path_parents: this.files_context_menu_config.path_parent_id
      }).then(res => {
        this.getFiles()
      })
    },
    restore_file(id) {
      this.files_context_menu_config.visible = false
      this.$alert('Востановить файл?', 'Внимание', {
        confirmButtonText: 'Востановить',
        cancelButtonText: 'Отмена',
        showCancelButton: true,
        type: 'error'
      }).then(res => {
        destroy('file', id).then(res => {
          this.$message.success('Востановлено')
          this.getFiles()
        })
      })
    }
  }
}
</script>

<style scoped lang="scss">
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
