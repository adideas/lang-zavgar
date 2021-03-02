<template>
  <div v-if="!file.disable" @dblclick="openEntity" @contextmenu.prevent="$emit('contextmenu', $event)">
    <div v-if="typeViewList === 'В клетку'" class="file">
      <svg-icon v-if="file.path && file.is_file === 0" icon-class="folder" style="width: 60px; height: 50px;" />
      <svg-icon v-if="file.path && file.is_file === 1" :icon-class="file.file_type | iconClassFile" style="width: 60px; height: 50px;" />
      <svg-icon v-if="file.indexed" icon-class="language" style="width: 60px; height: 50px;" />
      <div v-if="file.indexed" class="name unselectable">
        <input :value="file.name" onClick="this.select();" style="width: 100%;text-align: center; border: none; box-shadow: none;">
      </div>
      <div v-else class="name unselectable">{{ file.name }}</div>
    </div>
    <div v-if="typeViewList === 'Списком'" class="file-list">
      <svg-icon v-if="file.path && file.is_file === 0" icon-class="folder" style="width: 30px; height: 30px;" />
      <svg-icon v-if="file.path && file.is_file === 1" :icon-class="file.file_type | iconClassFile" style="width: 30px; height: 30px;" />
      <svg-icon v-if="file.indexed" :icon-class="file.translate === null ? 'language' : 'keys'" style="width: 30px; height: 30px;" />
      <div v-if="file.indexed" class="name unselectable">
        <input :value="file.name" onClick="this.select();" style="width: 100%; border: none; box-shadow: none;">
      </div>
      <div v-else class="name unselectable">{{ file.name }}</div>
    </div>
    <el-dialog :visible.sync="dialog.visible">
      {{ dialog.data }}
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'File',
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
  props: {
    file: {
      type: Object,
      default: () => {}
    },
    typeViewList: {
      type: String,
      default: () => 'В клетку'
    }
  },
  data() {
    return {
      dialog: {
        visible: false,
        data: null
      }
    }
  },
  methods: {
    currentFile(data) {
      const _data = JSON.parse(JSON.stringify(data))
      _data.files = null
      _data.keys = null
      _data.translate = null
      this.$emit('current-file', _data)
    },
    openEntity() {
      // keys
      if (this.file.files && this.file.files.filter(x => !x.disable).length) {
        this.$emit('view-files', this.file.files)
        this.currentFile(this.file)
        this.$emit('history', this.file.name)
      } else {
        if (this.file.keys && this.file.keys.filter(x => !x.disable).length) {
          this.$emit('view-files', this.file.keys)
          this.currentFile(this.file)
          this.$emit('history', this.file.name)
        } else {
          if (this.file.translate) {
            this.dialog.visible = true
            this.dialog.data = this.file.translate
          } else {
            this.$emit('view-files', [])
            this.currentFile(this.file)
            this.$emit('history', this.file.name)
            /* this.$alert('Дальше ничего нет', 'Внимание', {
              confirmButtonText: 'Хорошо'
            })
            console.warn('No')
            console.error(this.file)*/
          }
        }
      }
    }
  }
}
</script>

<style scoped lang="scss">
.file-tree {

}
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
input:focus {outline:none!important;}
input {border:0;outline:0;}
</style>
