<template>
  <div>
    <el-form>
      <el-form-item>
        <div style="min-width: 100%; display: flex;">
          ${language} => Заменяется на [ru, en ...]
        </div>
      </el-form-item>
      <el-form-item label="Название">
        <el-input v-model="el.name" />
      </el-form-item>
      <el-form-item label="Описание">
        <el-input v-model="el.description" />
      </el-form-item>
      <el-form-item label="Дата обновления">
        <div style="min-width: 100%; display: flex;">
          {{ new Date(entity.updated_at).toISOString().slice(0,19).replace('T', ' ') }}
        </div>
      </el-form-item>
      <el-form-item>
        <el-button style="float: right;" type="success" @click="closeDialog">Обновить</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  name: 'PropertiesFolderAndFile',
  props: {
    entity: {
      type: Object,
      default: () => {}
    },
    callbackUpdate: {
      type: Function,
      default: () => {}
    }
  },
  data() {
    return {
      el: {
        name: '',
        description: ''
      }
    }
  },
  watch: {
    entity(entity) {
      this.el.name = this.entity.name
      this.el.description = this.entity.description
    }
  },
  beforeDestroy() {
    console.error('beforeDestroy')
  },
  created() {
    this.el.name = this.entity.name
    this.el.description = this.entity.description
  },
  methods: {
    closeDialog() {
      if (this.el.name !== this.entity.name || this.el.description !== this.entity.description) {
        this.callbackUpdate(this.el)
      }
      this.$parent.close()
    }
  }
}

export function current_update_entity(current, data, e) {
  const index = current.findIndex(x => x.id === data.id)
  if (index >= 0) {
    current[index].name = e.name
    current[index].description = e.description
  }
}
export function recursive_update_entity(file, data, e) {
  if (file.name === data.name && file.description === data.description) {
    file.name = e.name; file.description = e.description
  }
  if (file.files) {
    file.files.forEach(el => recursive_update_entity(el, data, e))
  }
}
</script>

<style scoped>

</style>
