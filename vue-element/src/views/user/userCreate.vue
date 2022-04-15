<template>
  <div v-loading="loading" class="app-container">

    <header-sticky title="Создание пользователя">
      <template slot="prepend">
        <el-button type="success" icon="el-icon-check" @click="createUser">Сохранить</el-button>
      </template>
    </header-sticky>

    <div class="el-col-24">
      <div class="el-col-xs-24 el-col-sm-24 el-col-md-24 el-col-lg-12 el-col-xl-12 el-col-12" style="margin-top: 10px">
        <el-card header="Основные">
          <el-form>

            <el-form-item label="Имя">
              <el-input v-model="form.name" />
            </el-form-item>

            <el-form-item label="Почта">
              <div style="overflow: hidden; width: 0;">
                <input id="email" type="email" name="email" style="width: 100px; background: #fff; border: none;">
              </div>
              <el-input v-model="form.email" />
            </el-form-item>

            <el-form-item label="Пароль">
              <div style="overflow: hidden; width: 0;">
                <input id="password" type="password" name="password" style="width: 100px; background: #fff; border: none;">
              </div>
              <el-input v-model="form.password" :type="show_password ? 'text' : 'password'">
                <template slot="append">
                  <el-button icon="el-icon-view" @click="show_password = !show_password" />
                </template>
              </el-input>
            </el-form-item>

          </el-form>
        </el-card>
      </div>
      <div class="el-col-xs-24 el-col-sm-24 el-col-md-24 el-col-lg-11 el-col-lg-offset-1 el-col-xl-11 el-col-xl-offset-1 el-col-12" style="margin-top: 10px">
        <el-card header="Доступы">
          <el-transfer v-model="form.access_id" class="my-transfer-user" filterable :data="access_list" :titles="['Все доступы', 'Предоставленные']" :props="{key: 'id', label: 'name'}" />
        </el-card>
      </div>
    </div>

  </div>
</template>

<script>
import generator from 'generate-password'
import { list, store } from '@/api/api-laravel'
export default {
  name: 'UserCreate',
  data() {
    return {
      loading: true,
      show_password: false,
      access_list: [],
      form: {
        email: '',
        password: '',
        name: '',
        access_id: []
      }
    }
  },
  created() {
    list('user-access').then(res => {
      this.access_list = res.data || []
    }).finally(_ => {
      this.loading = false
    })
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

<style scoped lang="scss">

</style>
<style lang="scss">
.my-transfer-user {
  .el-transfer-panel {
    width: calc((100% - 122px)/ 2);
  }
  .el-checkbox.el-transfer-panel__item {
    min-height: 50px;
    .el-checkbox__label {
      white-space: break-spaces;
      line-height: initial;
    }
  }
  .el-transfer__buttons {
    width: 122px;
    button {
      margin: 0;
      margin-top: 10px;
    }
  }
}
</style>
