export default {
  path: 'language',
  component: () => import('@/views/language/index'),
  name: 'Language',
  meta: { title: 'Языки', icon: 'earth', roles: ['show.App\\Models\\Language'] }
}
