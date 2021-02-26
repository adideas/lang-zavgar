<template>
  <div style="width: 100%; float: left;">
    <div
      style="width: 100%; float: left; padding: 2px 2px;"
      :class="{'hover-row': true, 'is-click': is_click}"
      @click="click(null)"
      @contextmenu.prevent="contextmenu({$event, data})"
    >
      <svg-icon v-if="data.is_file === 0" icon-class="folder" :style="iconStyle" />
      <svg-icon v-else :icon-class="data.file_type | iconClassFile" :style="iconStyle" />
      <span style="color: #e4e4e4;">{{ data.name }} <span v-if="path"> - [{{ data.path }}]</span></span>
    </div>
    <div style="width: 100%; float: left;">
      <div :style="{ width: '100%', float: 'left', paddingLeft: '15px' }">
        <tree-view v-for="(file, index) in data.children" :key="index" :index="index" :data="file" :clear-click="clearClick" :path="path" @click="click" @contextmenu="(e) => contextmenu(e, true)" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TreeView',
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
    data: {
      type: Object,
      default: () => {}
    },
    clearClick: {
      type: Boolean,
      default: () => false
    },
    path: {
      type: Boolean,
      default: () => false
    }
  },
  data() {
    return {
      iconStyle: {
        width: '20px',
        height: '20px'
      },
      is_click: false
    }
  },
  watch: {
    clearClick() {
      this.is_click = false
    }
  },
  methods: {
    click(data) {
      if (data) {
        this.$emit('click', data)
      } else {
        setTimeout(() => {
          this.is_click = true
        }, 100)
        this.$emit('click', this.data)
      }
    },
    contextmenu(event, is_no_root = false) {
      if (event) {
        if (!is_no_root) {
          setTimeout(() => {
            this.is_click = true
          }, 100)
        }
        this.$emit('contextmenu', event)
      }
    }
  }
}
</script>

<style scoped lang="scss">
.hover-row:hover {
  cursor: pointer;
  background-color: #4f4e4e;
}
.is-click {
  background-color: #4f4e4e;
}
</style>
