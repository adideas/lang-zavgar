import router from './router'
import store from './store'
import { Message } from 'element-ui'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import { get_access_token } from '@/utils/auth'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

const whiteList = ['/login', '/auth-redirect']

router.beforeEach(async(to, from, next) => {
  NProgress.start()
  setTimeout(_ => NProgress.done(), 1500)

  if (get_access_token()) {
    var access = store.getters.access && store.getters.access.length > 0

    if (!access) {
      try {
        store.dispatch('user/userInfo').then((data) => {
          store.dispatch('permission/generateRoutes', ['admin', ...store.getters.access]).then(data => {
            router.addRoutes(data)
            next({ ...to, replace: true })
          }).catch(_ => {
            store.commit('user/CLEAR_PASSPORT')
            next(`/login`)
          })
        }).catch(_ => {
          store.commit('user/CLEAR_PASSPORT')
          next(`/login`)
        })
      } catch (e) {
        Message.error(e || 'Has Error')
        next(`/login`)
      }
    } else {
      next()
    }
  } else {
    if (whiteList.indexOf(to.path) !== -1) {
      next()
    } else {
      next(`/login`)
    }
  }

  /* try {
    store.dispatch('user/userInfo').then((data) => {
      store.dispatch('permission/generateRoutes', ['admin']).then(data => {
        router.addRoutes(data)
        next({ ...to, replace: true })
        console.error(3)
      }).catch(_ => {
        store.commit('user/CLEAR_PASSPORT')
        next(`/login`)
        console.error(4)
      })
    }).catch(_ => {
      store.commit('user/CLEAR_PASSPORT')
      next(`/login`)
      console.error(5)
    })
  } catch (error) {
    Message.error(error || 'Has Error')
    next(`/login`)
    console.error(6)
    return
  }*/
})

router.afterEach(_ => NProgress.done())
