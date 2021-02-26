<template>
  <el-dialog :visible.sync="visibleDialog" :title="data.data.path || ''">
    <el-form>
      <el-form-item label="Название">
        <el-input v-model="el.name" />
      </el-form-item>
      <el-form-item label="Описание">
        <el-input v-model="el.description" type="textarea" />
      </el-form-item>
      <el-form-item v-if="el.file_type" label="Тип файла">
        <el-select v-model="el.file_type" class="el-col-24">
          <el-option :value="1" label="PHP" />
          <el-option :value="2" label="JavaScript" />
          <el-option :value="3" label="JSON" />
          <el-option :value="4" label="ENV" />
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" style="float: right;" @click="save">Обновить</el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
</template>

<script>
import { update } from '@/api/api-laravel'

export default {
  name: 'SettingsFile',
  props: {
    visible: {
      type: Boolean,
      default: () => false
    },
    data: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      el: {
        file_id: 0,
        file_type: null,
        name: '',
        description: ''
      }
    }
  },
  computed: {
    visibleDialog: {
      get() {
        return this.visible
      },
      set(v) {
        this.$emit('update:visible', v)
      }
    }
  },
  created() {
    this.el.file_id = this.data.data.id
    this.el.file_type = this.data.data.file_type
    this.el.name = this.data.data.name
    this.el.description = this.data.data.description
  },
  methods: {
    save() {
      update('file', this.el.file_id, this.el).then(res => {
        this.$message.success('Обновлено')
        this.visibleDialog = false
        this.$emit('update')
      })
    }
  }
}
</script>

<style scoped>

</style>
