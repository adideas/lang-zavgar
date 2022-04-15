import { logout, getUserInfo, getTokenFromPassport } from '@/api/auth'
import { get_access_token, set_access_token, remove_access_token } from '@/utils/auth'
import { resetRouter } from '@/router'
import store from '@/store'
import Vue from 'vue'

export default {
  namespaced: true,
  state: {
    access_token: get_access_token(),
    expires_in: null,
    refresh_token: '',
    token_type: 'Bearer',
    id: '',
    name: '',
    email: '',
    access: {}
  },
  mutations: {
    SET_PASSPORT: (state, data) => {
      const { access_token, expires_in, refresh_token, token_type } = data
      state.access_token = access_token
      set_access_token(access_token)
      state.expires_in = expires_in
      state.refresh_token = refresh_token
      state.token_type = token_type
    },
    CLEAR_PASSPORT: (state) => {
      state.access_token = ''
      remove_access_token()
      state.expires_in = null
      state.refresh_token = ''
      state.token_type = 'Bearer'
    },
    SET_USER_DATA: (state, data) => {
      state.id = data.id
      state.name = data.name
      state.email = data.email
      state.access = data.access || {}
    }
  },
  actions: {
    login({ commit }, user_data) {
      return new Promise((resolve, reject) => {
        getTokenFromPassport(user_data.username.trim(), user_data.password).then(response => {
          commit('SET_PASSPORT', response.data)
          resolve(response)
        }).catch(err => {
          reject(err)
        })
      })
    },
    logout({ commit, state, dispatch }) {
      return new Promise((resolve) => {
        logout(state.access_token).finally(_ => {
          commit('CLEAR_PASSPORT')
          resetRouter()
          dispatch('tagsView/delAllViews', null, { root: true })
          resolve()
        })
      })
    },
    userInfo({ commit }) {
      return new Promise((resolve, reject) => {
        getUserInfo().then(res => {
          commit('SET_USER_DATA', res.data.data)
          resolve(res)
        }).catch(err => {
          reject(err)
        })
      })
    }
  }
}

Vue.prototype.$isRootUser = function() {
  if (store.state.user) {
    if (store.state.user.access) {
      return store.state.user.access.root || false
    }
  }
  return false
}
