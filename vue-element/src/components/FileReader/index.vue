<template>
  <div v-loading="loading" class="FileReader" style="height: calc(100vh - 84px); background-color: #dfdfdf;">
    <div class="unselectable" style="height: calc(100vh - 200px); background-color: white; overflow-y: auto;" @contextmenu.prevent="contextMenuBoard" @click.stop="selectFile()">
      <div
        v-for="(file, index) in currentCapture"
        :key="index"
        :class="{mini_preview: true, select: select_file === file.id}"
        @dblclick="getCapture({id: file.id, type: file.is_file === 0 ? 'folder' : (file.is_file === 1 ? 'file' : 'key' )})"
        @click.stop="selectFile(file)"
        @contextmenu.prevent="contextMenuFile"
      >
        <div class="icon">
          <svg-icon v-if="file.path && file.is_file === 0" icon-class="folder" style="width: 60px; height: 60px;" />
          <svg-icon v-if="file.path && file.is_file === 1" :icon-class="file.file_type | iconClassFile" style="width: 60px; height: 60px;" />
          <svg-icon v-if="file.indexed" :icon-class="file.translates_count > 0 ? 'language' : 'keys'" style="width: 60px; height: 60px;" />
        </div>
        <div class="name">
          <el-popover
            placement="left"
            title="Инфо"
            width="200"
            trigger="hover"
            :content="file.name"
          >
            <span slot="reference">{{ file.name }}</span>
          </el-popover>
        </div>
      </div>
    </div>
    <div>
      <table v-if="select_file_object" style="width: 100%;">
        <tr>
          <td>Название:</td>
          <td><input :value="select_file_object.name" class="input_data" onClick="this.select();"></td>
        </tr>
        <tr>
          <td>Путь:</td>
          <td><input :value="select_file_object.path" class="input_data" onClick="this.select();"></td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FileReader',
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
  data() {
    return {
      loading: true,
      currentCapture: null,
      linkCapture: {},
      select_file: null,
      select_file_object: null
    }
  },
  created() {
    this.getCapture()
  },
  methods: {
    getCapture(data = {}) {
      this.loading = true
      this.linkCapture = data
      this.select_file = null
      this.select_file_object = null
      this.$ws('FileReader', data).then(res => {
        this.currentCapture = res.data
      }).finally(_ => {
        this.loading = false
      })
    },
    contextMenuBoard() {

    },
    contextMenuFile() {

    },
    selectFile(file = {}) {
      if (file.id) {
        this.select_file = file.id
        this.select_file_object = file
      } else {
        this.select_file = null
        this.select_file_object = null
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.FileReader {
  .mini_preview {
    width: 100px;
    height: 100px;
    margin: 4px;
    display: block;
    cursor: pointer;
    float: left;

    &.select {
      background-color: #dfdfdf;
      border-radius: 7px;
    }

    &:hover {
      background-color: #dfdfdf;
      border-radius: 7px;
    }

    .icon {
      width: 60px;
      height: 60px;
      margin: 0 20px;
    }
    .name {
      width: 100px;
      height: 40px;
      text-align: center;
      /*word-wrap: break-word;*/
      overflow: hidden;
      margin: 1px;
      font-size: 13px;
      line-height: 22px;
      text-overflow: ellipsis;
    }
  }
}

.contextmenu {
  .file .board {
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
}
.input_data:focus {outline:none!important; width: 100%; border: none; box-shadow: none;}
.input_data {border:0;outline:0; width: 100%; box-shadow: none;}
</style>
