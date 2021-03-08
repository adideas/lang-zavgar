import router from '@/router'
import Vue from 'vue'

class QueryHelper {
  constructor() {
    this.href = ''
    this.query_json = {}
    this.json = {}
  }
  get() {
    try {
      if (router.currentRoute.query) {
        const query = {}
        Object.keys(router.currentRoute.query).forEach(key => {
          try {
            if (router.currentRoute.query[key].slice(0, 3) === '...') {
              query[key] = JSON.parse(this.convertString(router.currentRoute.query[key]).back())
            } else {
              query[key] = router.currentRoute.query[key]
            }
          } catch (e) {
            //
            console.error(e)
          }
        })
        return query
      }
    } catch (e) {
      return {}
    }
  }
  set(query_json) {
    query_json = this.convertData(query_json)
    const href = router.resolve({ query: query_json }).href
    window.history.replaceState({}, '', href)
    // НЕОБХОДИМО ВЫЗВАТЬ СОБЫТИЕ ИЛИ ИСТОРИЯ ВКЛАДОК БУДЕТ РАБОТАТЬ НЕ ПРАВИЛЬНО
    window.dispatchEvent(new Event('popstate'))
    this.href = href

    return this
  }
  toString() {
    return this.href
  }
  toJSON() {
    return this.query_json
  }
  convertString(string) {
    // ~ - " , . < >
    return {
      to() {
        return '...' + btoa(string).replace(/=/gm, '...')
      },
      back() {
        return atob(
          String(string)
            .match(/...(.+)/)[1]
            .replace(/\.\.\./gm, '=')
        )
      }
    }
  }
  convertData(query_json) {
    const query_val = {}
    this.json = query_json
    try {
      Object.keys(query_json).forEach(key => {
        if (typeof query_json[key] === 'object' || Array.isArray(query_json[key])) {
          const json_data = this.convertString(JSON.stringify(query_json[key])).to()
          if (json_data.length) {
            query_val[key] = json_data
          }
        } else {
          if (query_json[key]) {
            query_val[key] = query_json[key]
          }
        }
      })
    } catch (e) {
      console.error(e)
    }
    this.query_json = query_val
    return query_val
  }
}

const _QueryHelper = new QueryHelper()

Vue.prototype.$query = function($data) {
  _QueryHelper.set($data)
  window.query = $data
}

export const watcherQuery = {
  handler: function(e) {
    this.$query(e)
  },
  deep: true
}

export default {
  install(Vue) {
    Vue.mixin({
      data() {
        return {
          query: Vue.observable(window.query)
        }
      },
      watch: {
        '$route.query': function(query) {
          window.query = Object.assign(_QueryHelper.get())
          this.query = window.query
        }
      },
      beforeCreate() {
        if (this.$options['query']) {
          setTimeout(() => {
            this.$options['query'].bind(this)(window.query)
          }, 1)
        }
      }
    })
  }
}
