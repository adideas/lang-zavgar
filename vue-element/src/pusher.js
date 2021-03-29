import Vue from 'vue'
import { raw } from '@/api/api-laravel'
import store from '@/store'

var ws = null
var buffer = {}
var ws_auth = false

function pusher() {
  ws = new WebSocket('ws://lang.zavgar.online:6001/app/c6006da2763f3cc10a5f?protocol=7&client=js&version=4.3.1&flash=false')
  var channel = 'private.User.' + store.state.user.id

  ws.onmessage = (res) => {
    let data = {}
    if (res.data) {
      data = JSON.parse(res.data)
    }
    if (data.data) {
      try {
        data.data = JSON.parse(data.data)
      } catch (e) { /**/ }
    }

    switch (data.event) {
      case 'pusher:connection_established':

        raw({
          url: 'http://lang.zavgar.online/api/websocket/auth',
          method: 'post',
          data: { socket_id: (data.data.socket_id), channel_name: channel },
          headers: {
            'X-App-ID': '829353'
          }
        }).then(res => {
          const auth = {
            event: 'pusher:subscribe',
            data: {
              auth: res.data.auth,
              channel: channel
            }
          }
          if (ws) {
            ws.send(JSON.stringify(auth))
          }
        })
        break
      case 'pusher_internal:subscription_succeeded':
        ws_auth = true
        setTimeout(() => {
          if (ws) {
            ws.send(JSON.stringify({ event: 'pusher:ping', data: {}}))
          }
        }, 4000)
        break
      case 'pusher:pong':
        if (channel !== 'private.User.' + store.state.user.id) {
          ws.close()
          ws = undefined
          var i = setInterval(() => {
            if (store.state.user.id) {
              pusher()
              clearInterval(i)
            }
          }, 1000)
        }
        setTimeout(() => {
          if (ws) {
            ws.send(JSON.stringify({ event: 'pusher:ping', data: {}}))
          }
        }, 4000)
        break
      case 'pusher:error':
        ws_auth = false
        ws.close()
        ws = undefined
        break
      default:
        if (data.id) {
          buffer[data.id] = data
        }
        break
    }
  }

  ws.onclose = (res) => {
    ws_auth = false
    ws = undefined
    setTimeout(() => {
      pusher()
    }, 1000)
  }
}

var i = setInterval(() => {
  if (store.state.user.id) {
    pusher()
    clearInterval(i)
  }
}, 500)

Vue.prototype.$ws = function(event = 'pusher:ping', data = {}) {
  return new Promise((resolve, reject) => {
    function connect() {
      const id = Math.random()
      ws.send(JSON.stringify({ event, data, id }))

      let interval = setInterval(() => {
        if (buffer[id]) {
          clearInterval(interval)
          resolve(buffer[id])
          interval = null
        }
      }, 100)

      setTimeout(() => {
        if (interval) {
          clearInterval(interval)
          reject()
          interval = null
        }
      }, 5000)
    }

    const interval_connect = setInterval(() => {
      if (ws_auth) {
        clearInterval(interval_connect)
        return connect()
      }
    }, 100)
  })
}
