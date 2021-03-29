import request from '@/utils/request'

export function list(url, query) {
  return request({ url: url, method: 'get', params: query })
}

export function store(url, data) {
  return request({ url: url, method: 'post', data })
}

export function show(url, id, query = {}) {
  return request({ url: url + '/' + id, method: 'get', params: query })
}

export function update(url, id, data = {}) {
  return request({ url: url + '/' + id, method: 'put', data })
}

export function destroy(url, id, data = {}) {
  return request({ url: url + '/' + id, method: 'delete', data })
}

export function raw(...p) {
  return request(...p)
}
