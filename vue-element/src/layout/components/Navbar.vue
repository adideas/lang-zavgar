<template>
  <div class="navbar">
    <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

    <breadcrumb id="breadcrumb-container" class="breadcrumb-container" />

    <div class="right-menu">
      <div v-if="$store.getters.access.root" class="avatar-container right-menu-item">
        <div class="avatar-wrapper" @click="uploadToGithub">
          <svg-icon icon-class="upload_github" class="user-avatar" />
        </div>
      </div>
      <el-dropdown class="avatar-container right-menu-item hover-effect" trigger="click">
        <div class="avatar-wrapper">
          <svg-icon icon-class="driver" class="user-avatar" />
          <i class="el-icon-caret-bottom" />
        </div>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item @click.native="logout">
            <span style="display:block;">Выход</span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>

    <el-dialog title="Отправка на github" append-to-body :visible.sync="dialog_upload_git" width="170px">
      <el-progress type="dashboard" :percentage="percentage" :color="colors" />
    </el-dialog>

  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'
import { store } from '@/api/api-laravel'

export default {
  components: {
    Breadcrumb,
    Hamburger
  },
  data() {
    return {
      avatar: process.env.VUE_APP_BASE_HOST + 'storage/icon.png',
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
  computed: {
    ...mapGetters([
      'sidebar',
      // 'avatar',
      'device'
    ])
  },
  methods: {
    uploadToGithub() {
      if (this.$store.getters.access.root) {
        this.$alert('Отправить код на github (master ветку) ?', 'Внимание', {
          confirmButtonText: 'Отправить',
          showCancelButton: true,
          cancelButtonText: 'Отменить'
        }).then(res => {
          store('translate').then(res => {
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
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar')
    },
    async logout() {
      await this.$store.dispatch('user/logout')
      this.$router.push(`/login`)
    }
  }
}
</script>

<style lang="scss" scoped>
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0,21,41,.08);

  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background .3s;
    -webkit-tap-highlight-color:transparent;

    &:hover {
      background: rgba(0, 0, 0, .025)
    }
  }

  .breadcrumb-container {
    float: left;
  }

  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }

  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background .3s;

        &:hover {
          background: rgba(0, 0, 0, .025)
        }
      }
    }

    .avatar-container {
      margin-right: 30px;

      .avatar-wrapper {
        margin-top: 5px;
        position: relative;

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }

        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
