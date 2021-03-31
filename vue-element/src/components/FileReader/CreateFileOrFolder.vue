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
      <el-form-item v-if="typeFile === 2" label="Тип файла">
        <el-select v-model="el.file_type" class="el-col-24">
          <el-option label="Php" :value="1" />
          <el-option label="JavaScript" :value="2" />
          <el-option label="Json" :value="3" />
          <el-option label="Env" :value="4" />
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button style="float: right;" @click="saveFile">Сохранить</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { rus_to_latin } from '@/mixins/rusToLatin'

export default {
  name: 'CreateFileOrFolder',
  props: {
    typeFile: {
      type: Number,
      default: () => 1
    },
    hook: {
      type: Number,
      default: () => 1
    },
    callback: {
      type: Function,
      default: () => {}
    }
  },
  data() {
    return {
      el: {
        name: '',
        description: '',
        is_file: 0,
        file_type: 1
      }
    }
  },
  watch: {
    hook(hook) {
      this.el.name = ''
      this.el.description = ''
    },
    typeFile(e) {
      if (this.typeFile === 1) {
        this.el.is_file = 0
      } else {
        this.el.is_file = 1
        this.el.file_type = 1
      }
    }
  },
  created() {
    if (this.typeFile === 1) {
      this.el.is_file = 0
    } else {
      this.el.is_file = 1
    }
  },
  methods: {
    filterS(e) {
      this.el.name = rus_to_latin(this.el.name).withoutSymbol()
    },
    saveFile() {
      this.callback(this.el)
      this.$parent.close()
    }
  }
}
</script>

<style scoped>

</style>
