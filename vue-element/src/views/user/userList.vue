<template>
  <div v-loading="loading">

    <header-sticky title="Пользователи">
      <template slot="prepend">
        <el-button type="success" icon="el-icon-plus" @click="$router.push({ name: 'UserCreate' })">Добавить</el-button>
      </template>
    </header-sticky>

    <el-table :data="users.data">

      <el-table-column label="id">
        <template slot-scope="row">
          <el-col># {{ row.row.id }}</el-col>
        </template>
      </el-table-column>

      <el-table-column prop="email" label="email" />
      <el-table-column prop="name" label="name" />
      <el-table-column label="last time">
        <template slot-scope="row">
          <el-col>{{ row.row.last_login_at | humanDate }}</el-col>
        </template>
      </el-table-column>

      <el-table-column align="right">
        <template slot-scope="row">
          <el-button-group>
            <el-button icon="el-icon-edit" type="primary" size="mini" @click="$router.push({name: 'UserEdit', params: { id: row.row.id }})" />
            <el-button icon="el-icon-delete" type="danger" size="mini" @click="removeUser" />
          </el-button-group>
        </template>
      </el-table-column>

    </el-table>

    <div class="mt-10">
      <el-pagination
        :page-sizes="[10, 25, 50]"
        :page-size="query.to"
        :total="users.total"
        background
        style="text-align: center; position: fixed; width: 100%; bottom: 15px;"
        layout="total, sizes, prev, pager, next"
        :current-page="users.current_page"
        @size-change="(e) => { query.to = e; getList() }"
        @current-change="(e) => { query.page = e; getList() }"
      />
    </div>

  </div>
</template>

<script>
import { list } from '@/api/api-laravel'
import humanDate from '@/filters/human-date'

export default {
  name: 'UserList',
  filters: { humanDate },
  data() {
    return {
      loading: true,
      users: {},
      query: {
        page: 1,
        to: 10
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.loading = true
      list('user', this.query).then(res => {
        this.users = res.data || []
      }).finally(_ => {
        this.loading = false
      })
    },
    removeUser() {

    }
  }
}
</script>

<style scoped>

</style>
