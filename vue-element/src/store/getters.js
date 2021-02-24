const getters = {
  sidebar: state => state.app.sidebar,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  permission_routes: state => state.permission.routes,
  //
  access_token: state => state.user.access_token,
  token_type: state => state.user.token_type,
  access: state => state.user.access
}
export default getters
