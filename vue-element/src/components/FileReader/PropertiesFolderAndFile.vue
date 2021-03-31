<template>
  <div>
    <el-form>
      <el-form-item>
        <div style="min-width: 100%; display: flex;">
          ${language} ${language_name} => Заменяется на [ru, en ...]
        </div>
      </el-form-item>
      <el-form-item label="Название">
        <el-input v-model="el.name" @input="filterS" @change="filterS" />
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
import { rus_to_latin } from '@/mixins/rusToLatin'

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
    filterS(e) {
      this.el.name = rus_to_latin(this.el.name).withoutSymbol()
    },
    closeDialog() {
      if (this.el.name !== this.entity.name || this.el.description !== this.entity.description) {
        this.callbackUpdate(this.el)
      }
      this.$parent.close()
    }
  }
}
</script>

<style scoped>

</style>
