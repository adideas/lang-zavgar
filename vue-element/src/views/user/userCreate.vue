<template>
  <div class="app-container">

    <header-sticky title="Создание пользователя">
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
import generator from 'generate-password'
import { store } from '@/api/api-laravel'
export default {
  name: 'UserCreate',
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
    this.form.password = generator.generate({
      length: 10,
      numbers: true,
      uppercase: false,
      excludeSimilarCharacters: true
    })
  },
  methods: {
    createUser() {
      store('user', this.form).then(res => {
        this.$router.back()
      })
    }
  }
}
</script>

<style scoped>

</style>
