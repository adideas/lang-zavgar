<template>
  <div>
    <el-drawer :visible.sync="search.visible" size="50%" :wrapper-closable="false">
      <template slot="title">
        <div style="width: 100%; text-align: center;">
          Поиск
        </div>
      </template>
      <div class="app-container">
        <el-input v-model="search.text" clearable size="mini" placeholder="Начните вводить для поиска">
          <template slot="append">
            <el-button icon="el-icon-search" @click="startSearch(search.text)" />
          </template>
        </el-input>
        <div style="overflow-y: scroll; height: calc(100vh - 200px);">
          <el-table :data="search.data" empty-text="Ничего не найдено" @cell-click="clickSearch">
            <el-table-column width="50">
              <template slot-scope="{ row }">
                <svg-icon :icon-class="icon(row)" />
              </template>
            </el-table-column>
            <el-table-column>
              <template slot-scope="{ row }">
                <el-col style="cursor: pointer;">
                  {{ text(row) }}
                </el-col>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </el-drawer>
  </div>
</template>

<script>
import { store } from '@/api/api-laravel'

export default {
  name: 'Search',
  props: {
    visible: {
      type: Boolean,
      default: () => false
    },
    router: {
      type: Boolean,
      default: () => false
    },
    filter: {
      type: Array,
      default: () => []
    },
    language: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      search: {
        text: '',
        visible: false,
        data: []
      }
    }
  },
  watch: {
    'search.visible': function(v) {
      this.$emit('update:visible', v)
    },
    visible(visible) {
      this.search.visible = this.visible
    }
  },
  created() {
    this.search.visible = this.visible
  },
  methods: {
    clickSearch({ id }) {
      this.search.visible = false
      this.$emit('model', id)
    },
    icon(entity) {
      if (entity.model && entity.model.file_type) {
        if (entity.model.is_file === 0) {
          return 'folder'
        } else {
          return ['php', 'js', 'json', 'env'][entity.model.file_type - 1]
        }
      }
      const icons = {
        'App\\Models\\Translate': 'language',
        'App\\Models\\File': 'folder',
        'App\\Models\\Key': 'keys',
        'App\\User': 'driver'
      }

      return icons[entity.entity]
    },
    text(entity) {
      if (entity.model && entity.model.name) {
        if (entity.model.description) {
          return `[${entity.model.name}] ${entity.model.description}`
        } else {
          if (entity.model.email) {
            return `${entity.model.name} [${entity.model.email}] `
          } else {
            return `${entity.model.name}`
          }
        }
      }

      if (entity.entity === 'App\\Models\\Translate') {
        const string = Object.keys(entity.model).filter(x => Number(x) && x).map(x => entity.model[x]).join(' ')
        if (string.replace(/\s/gm, '').length) {
          return string
        }
      }

      return '~ ' + entity.searchable
    },
    startSearch() {
      store('search', {
        search: this.search.text,
        filter: this.filter
      }).then(res => {
        this.search.data = res.data || []
      })
    }
  }
}
</script>

<style scoped>

</style>
