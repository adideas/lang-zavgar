<template>
  <div v-loading="loading" class="app-container">

    <header-sticky title="Языки">
      <template slot="prepend">
        <el-button type="success" icon="el-icon-plus" @click="createLanguage">Добавить</el-button>
      </template>
    </header-sticky>

    <el-table :data="languages">
      <el-table-column prop="id" label="#" width="50" />
      <el-table-column prop="name" label="Короткое название" />
      <el-table-column prop="description" label="Описание" />
      <el-table-column align="right">
        <template slot-scope="row">
          <el-button-group>
            <el-button size="mini" type="primary" icon="el-icon-edit" @click="updateLanguage(row.row)" />
            <el-button size="mini" type="danger" icon="el-icon-delete" @click="deleteLanguage(row.row)" />
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :visible.sync="dialog.visible" :title="dialog.title">
      <el-form v-if="dialog.visible">
        <el-form-item label="Короткое название">
          <el-input v-model="dialog.data.name" placeholder="Название папки" />
        </el-form-item>
        <el-form-item label="Описание">
          <el-input v-model="dialog.data.description" placeholder="Описание" />
        </el-form-item>
        <el-form-item>
          <el-button type="danger" @click="dialog.visible = false">Отменить</el-button>
          <el-button type="success" style="float:right;" @click="sendServer(dialog.data)">Сохранить</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>

  </div>
</template>

<script>
import { destroy, list, update, store } from '@/api/api-laravel'

export default {
  name: 'Language',
  data() {
    return {
      loading: true,
      languages: [],
      dialog: {
        visible: false,
        title: 'Создать язык',
        data: null
      }
    }
  },
  created() {
    this.getLanguage()
  },
  methods: {
    getLanguage() {
      this.loading = true
      list('language').then(res => {
        this.languages = res.data || []
      }).finally(_ => {
        this.loading = false
      })
    },
    createLanguage() {
      this.dialog.title = 'Создать язык'
      this.dialog.visible = true
      this.dialog.data = {
        name: '',
        description: ''
      }
    },
    updateLanguage(data) {
      this.dialog.title = 'Редактировать язык'
      this.dialog.data = data
      this.dialog.visible = true
    },
    deleteLanguage(data) {
      this.$alert('Удалить язык?', 'Внимание', {
        showCancelButton: true,
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена'
      }).then(res => {
        destroy('language', data.id).then(res => {
          this.$message.success('Удалено')
          this.getLanguage()
        })
      })
    },
    sendServer(data) {
      if (data.id) {
        update('language', data.id, data).then(res => {
          this.getLanguage()
          this.$message.success('Сохранено')
          this.dialog.visible = false
        })
      } else {
        store('language', data).then(res => {
          this.getLanguage()
          this.$message.success('Сохранено')
          this.dialog.visible = false
        })
      }
    }
  }
}
</script>

<style scoped>

</style>
