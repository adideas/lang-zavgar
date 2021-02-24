import Cookies from 'js-cookie'

export function get_access_token() {
  return Cookies.get('access_token')
}

export function set_access_token(token) {
  return Cookies.set('access_token', token)
}

export function remove_access_token() {
  return Cookies.remove('access_token')
}
