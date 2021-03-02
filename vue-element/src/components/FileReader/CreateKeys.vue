<template>
  <div>
    <el-form>
      <el-form-item>
        <el-input v-model="el.name" />
      </el-form-item>
      <el-form-item>
        <el-input v-model="el.description" />
      </el-form-item>
      <el-form-item>
        <el-checkbox v-model="is_end">Это конечный ключ (Не под ключ)</el-checkbox>
      </el-form-item>
      <el-form-item>
        <el-button style="float: right;" @click="saveKey">Сохранить</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  name: 'CreateKeys',
  props: {
    hook: {
      type: Number,
      default: () => 0
    },
    callback: {
      type: Function,
      default: () => {}
    }
  },
  data() {
    return {
      is_end: false,
      el: {
        name: '',
        description: ''
      }
    }
  },
  methods: {
    saveKey() {
      if (this.is_end) {
        this.callback({ ...this.el, translate: {}})
      } else {
        this.callback({ ...this.el, translate: null })
      }

      this.$parent.close()
    }
  }
}

export function recursive_add_key(files = [], copy_buffer) {
  return new Promise(resolve => {
    files.forEach(file => {
      if (file.name === copy_buffer.name && file.description === copy_buffer.description) {
        resolve(file)
      } else {
        if (file.files) {
          recursive_add_key(file.files, copy_buffer).then(res => {
            resolve(res)
          })
        }
        if (file.keys) {
          recursive_add_key(file.keys, copy_buffer).then(res => {
            resolve(res)
          })
        }
      }
    })
  })
}
</script>

<style scoped>

</style>
