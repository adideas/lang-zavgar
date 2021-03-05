export default {
  path: 'user',
  component: () => import('@/views/user/index'),
  name: 'User',
  redirect: 'list',
  meta: { title: 'Пользователи', icon: 'driver', roles: ['show.App\\User', 'store.App\\User', 'update.App\\User', 'destroy.App\\User'] },
  children: [
    {
      path: 'list',
      component: () => import('@/views/user/userList'),
      name: 'UserList',
      meta: { title: 'Пользователи', icon: 'driver', roles: ['show.App\\User'] }
    },
    {
      path: 'create',
      component: () => import('@/views/user/userCreate'),
      name: 'UserCreate',
      hidden: true,
      meta: { title: 'Создание пользователя', icon: 'driver', roles: ['store.App\\User'] }
    },
    {
      path: ':id/edit',
      component: () => import('@/views/user/userEdit'),
      name: 'UserEdit',
      hidden: true,
      meta: { title: 'Редактирование пользователя', icon: 'driver', roles: ['update.App\\User'] }
    }
  ]
}
