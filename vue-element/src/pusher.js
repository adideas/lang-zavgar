import { raw } from '@/api/api-laravel'
// import store from '@/store'

var ws = new WebSocket('ws://lang.zavgar.online:6001/app/c6006da2763f3cc10a5f?protocol=7&client=js&version=4.3.1&flash=false')
var channel = 'private.User'

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
      setTimeout(() => {
        if (ws) {
          ws.send(JSON.stringify({ event: 'pusher:ping', data: {}}))
        }
      }, 4000)
      break
    case 'pusher:pong':
      setTimeout(() => {
        if (ws) {
          ws.send(JSON.stringify({ event: 'pusher:ping', data: {}}))
        }
      }, 4000)
      setTimeout(() => {
        if (ws) {
          ws.send(JSON.stringify({ event: 'testConnectBla', data: { a: 'b' }}))
        }
      }, 4000)
      break
  }
}

ws.onclose = (res) => {
  console.error('onclose', res)
}
