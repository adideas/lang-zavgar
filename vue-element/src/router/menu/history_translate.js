export default {
  path: 'history_translate',
  component: () => import('@/views/history_translate/index'),
  name: 'HistoryTranslate',
  meta: { title: 'История переводов', icon: 'history_translate', roles: ['translate'] }
}
