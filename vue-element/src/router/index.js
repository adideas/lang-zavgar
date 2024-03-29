import Vue from 'vue'
import Router from 'vue-router'
import Layout from '@/layout'
import constant_routes from '@/router/constant/constantRoutes'
import dashboard from '@/router/menu/dashboard'
import user from '@/router/menu/user'
import translate from '@/router/menu/translate'
import history_translate from '@/router/menu/history_translate'
import files from '@/router/menu/files'
import language from '@/router/menu/language'

Vue.use(Router)

export const constantRoutes = [...constant_routes, {
  path: '/',
  component: Layout,
  redirect: '/dashboard',
  children: [
    ...dashboard,
    user,
    translate,
    history_translate,
    files,
    language,
    { name: '*', path: '*', redirect: '/404', hidden: true }
  ]
}]

const createRouter = () => new Router({
  mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
