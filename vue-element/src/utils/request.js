import axios from 'axios'
import { Message } from 'element-ui'
import store from '@/store'

// create an axios instance
const service = axios.create({ baseURL: process.env.VUE_APP_BASE_HOST + 'api', withCredentials: true, timeout: 30000 })

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

service.interceptors.request.use(
  config => {
    if (store.getters.access_token) {
      config.headers['Authorization'] = store.getters.token_type + ' ' + store.getters.access_token
    }
    return config
  },
  error => {
    // do something with request error
    console.log(error) // for debug
    return Promise.reject(error)
  }
)
service.interceptors.response.use(
  response => {
    return Promise.resolve(response)
  },
  error => {
    if (process.env.NODE_ENV !== 'production') {
      Message({ message: error.message, type: 'error', duration: 5 * 1000 })
      console.error(error.response.data)
    }
    return Promise.reject(error.response.data)
  }
)

export default service
