import request from '@/utils/request'

export function getTokenFromPassport(username, password) {
  const data = { username, password, grant_type: 'password', client_id: process.env.VUE_APP_PASSPORT_CLIENT_ID, client_secret: process.env.VUE_APP_PASSPORT_SECRET, scope: '' }
  return request({ url: process.env.VUE_APP_BASE_HOST + 'oauth/token', method: 'post', data })
}

export function getUserInfo() {
  return request({ url: 'current-user', method: 'get' })
}

export function logout() {
  return request({ url: 'logout', method: 'post' })
}
