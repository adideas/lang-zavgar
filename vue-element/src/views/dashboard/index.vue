<template>
  <div class="app-container" style="background: #e0e0e0; min-height: calc(100vh - 84px);">
    <div v-if="$store.getters.access.root">

      <div
        class="el-col-xs-24 el-col-sm-11 el-col-sm-offset-1 el-col-md-11 el-col-md-offset-1 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12"
        style="margin-top: 10px"
      >
        <el-card header="Статус перевода ключей">
          <div class="el-col-24">
            <div class="el-col-12 my_dashboard_class success" style="font-size: 30px; font-weight: 700;">
              <vue-count-to :start-val="0" :end-val="data.key_status[0]" />
            </div>
            <div class="el-col-12 my_dashboard_class warning" style="font-size: 30px; font-weight: 700;">
              <vue-count-to :start-val="0" :end-val="data.key_status[1]" />
            </div>
            <div class="el-col-12 my_dashboard_class success">
              Всего
            </div>
            <div class="el-col-12 my_dashboard_class warning">
              Без перевода
            </div>
          </div>
        </el-card>
      </div>

      <div
        v-for="(el, index) in data.git"
        :key="index"
        class="el-col-xs-24 el-col-sm-11 el-col-sm-offset-1 el-col-md-11 el-col-md-offset-1 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12"
        style="margin-top: 10px"
      >
        <el-card>
          <template slot="header">
            <span>
              {{ index + ` (${el.branches.join(', ')})` }}
            </span>
            <el-button size="mini" type="info" style="float: right;" @click="uploadToGithubDev(index)">push develop</el-button>
            <el-button size="mini" type="danger" style="float: right;" @click="uploadToGithub(index)">push master</el-button>
          </template>
          <!-- ---------------------------------- -->
          <el-card header="Последняя фиксация">
            <code class="hljs bash" style=" white-space: pre;" v-html="hljs_view(el.last_commit)" />
          </el-card>
          <!-- ---------------------------------- -->
          <el-card header="Статус" style="margin-top: 10px;">
            <code class="hljs bash" style=" white-space: pre;" v-html="hljs_view(el.status)" />
          </el-card>
          <!-- ---------------------------------- -->
        </el-card>
      </div>

    </div>
    <div v-else>
      <el-card header="Перевод">
        <p>Добро пожаловать в систему переводов Zavgar.Online</p>
        <p>Что бы начать переводить ключи (текст) нажмите на кнопку приступить к переводу</p>
        <div class="el-col-24" style="margin: 20px 0;">
          <el-button style="float: right;" type="danger" @click="$router.push({name: 'Translate'})">Приступить к переводу</el-button>
        </div>
      </el-card>
    </div>

    <el-dialog title="Отправка на github" append-to-body :visible.sync="dialog_upload_git" width="170px">
      <el-progress type="dashboard" :percentage="percentage" :color="colors" />
    </el-dialog>

  </div>
</template>

<script>
import { list, store } from '@/api/api-laravel'
import hljs from 'highlight.js'
import 'highlight.js/styles/github.css'
import VueCountTo from 'vue-count-to'

export default {
  name: 'Dashboard',
  components: { VueCountTo },
  data() {
    return {
      data: {},
      dialog_upload_git: false,
      percentage: 10,
      colors: [
        { color: '#f56c6c', percentage: 20 },
        { color: '#e6a23c', percentage: 40 },
        { color: '#5cb87a', percentage: 60 },
        { color: '#1989fa', percentage: 80 },
        { color: '#6f7ad3', percentage: 100 }
      ]
    }
  },
  created() {
    if (this.$store.getters.access.root) {
      this.getDashboard()
    } else {
      setTimeout(() => {
        if (this.$store.getters.access.root) {
          this.getDashboard()
        }
      }, 1000)
    }
  },
  methods: {
    uploadToGithub(index) {
      if (this.$store.getters.access.root) {
        this.$alert('Отправить код на github (master ветку) ?', 'Внимание', {
          confirmButtonText: 'Отправить',
          showCancelButton: true,
          cancelButtonText: 'Отменить'
        }).then(res => {
          store('translate', { repo: index, type: 'master' }).then(res => {
            this.dialog_upload_git = true
            const interval = setInterval(() => {
              this.percentage += 2
              if (this.percentage >= 100) {
                this.percentage = 0
                this.dialog_upload_git = false
                clearInterval(interval)
              }
            }, 200)
          })
        })
      }
    },
    uploadToGithubDev(index) {
      if (this.$store.getters.access.root) {
        this.$alert('Отправить код на github (develop ветку) ?', 'Внимание', {
          confirmButtonText: 'Отправить',
          showCancelButton: true,
          cancelButtonText: 'Отменить'
        }).then(res => {
          this.dialog_upload_git = true
          const interval = setInterval(() => {
            this.percentage += 2
            if (this.percentage >= 100) {
              this.percentage = 0
              this.dialog_upload_git = false
              clearInterval(interval)
            }
          }, 200)
          store('translate', { repo: index, type: 'develop' }).then(res => {})
        })
      }
    },
    getDashboard() {
      list('dashboard').then(res => {
        this.data = res.data.data
      })
    },
    hljs_view(value) {
      if (Array.isArray(value)) {
        return hljs.highlight('bash', value.join('\n')).value
      }
      return hljs.highlight('bash', value).value
    }
  }
}
</script>
<style scoped lang="scss">
.my_dashboard_class {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60px;
    font-size: 19px;
}
</style>
