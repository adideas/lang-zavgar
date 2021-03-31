<template>
  <div>
    <el-form>
      <el-form-item label="Название">
        <el-input v-model="el.name" @input="filterS" @change="filterS" />
      </el-form-item>
      <el-form-item v-if="is_end" label="Русский перевод">
        <el-input v-model="el.value" @input="filterS" @change="filterS" />
      </el-form-item>
      <el-form-item label="Описание">
        <el-input v-model="el.description" />
      </el-form-item>
      <el-form-item>
        <el-button style="float: right;" @click="saveKey">Сохранить</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { rus_to_latin } from '@/mixins/rusToLatin'

export default {
  name: 'CreateKeys',
  props: {
    hook: {
      type: Number,
      default: () => 0
    },
    typeAdd: {
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
        description: '',
        value: ''
      }
    }
  },
  watch: {
    hook() {
      this.el.name = ''
      this.el.description = ''
      this.el.is_end = false
    },
    typeAdd() {
      this.is_end = this.typeAdd === 3
    }
  },
  created() {
    this.is_end = this.typeAdd === 3
  },
  methods: {
    filterS(e) {
      this.el.name = rus_to_latin(this.el.name).withoutSymbol()
      this.el.value = this.el.value.replace(/[~,`,@,\",\',$,%,^,\r,\\,\/,\t,&,\},\{,\[,\]]*/g, '').replace(/[\n]/g, ' ')
    },
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
</script>

<style scoped>

</style>
