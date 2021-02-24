<template>
  <div>

    <header-sticky title="Редактирование пользователя">
      <template slot="prepend">
        <el-button type="success" icon="el-icon-check" @click="createUser">Сохранить</el-button>
      </template>
    </header-sticky>

    <el-form>

      <el-form-item label="Имя">
        <el-input v-model="form.name" />
      </el-form-item>

      <el-form-item label="Email">
        <el-input v-model="form.email" />
      </el-form-item>

      <el-form-item label="Пароль">
        <el-input v-model="form.password" :type="show_password ? 'text' : 'password'">
          <template slot="append">
            <el-button icon="el-icon-view" @click="show_password = !show_password" />
          </template>
        </el-input>
      </el-form-item>

    </el-form>

  </div>
</template>

<script>
import { show, update } from '@/api/api-laravel'

export default {
  name: 'UserEdit',
  data() {
    return {
      show_password: false,
      form: {
        email: '',
        password: '',
        name: ''
      }
    }
  },
  created() {
    show('user', this.$route.params.id).then(res => {
      this.form = res.data || {}
    })
  },
  methods: {
    createUser() {
      update('user', this.$route.params.id, this.form).then(res => {
        this.$router.back()
      })
    }
  }
}
</script>

<style scoped>

</style>
