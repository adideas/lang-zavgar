export default [
  {
    path: 'dashboard',
    component: () => import('@/views/dashboard/index'),
    name: 'Dashboard',
    meta: { title: 'Главная', icon: 'dashboard', affix: true }
  },
  {
    path: '404',
    component: () => import('@/views/error-page/404'),
    hidden: true,
    meta: { title: '404', icon: 'dashboard', affix: true }
  },
  {
    path: '401',
    component: () => import('@/views/error-page/401'),
    hidden: true,
    meta: { title: '401', icon: 'dashboard', affix: true }
  }
]
