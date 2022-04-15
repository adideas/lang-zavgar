<template>
  <div class="app-container">
    <div style="text-align: center; padding: 3px 3px 30px 3px;">
      <p>LCS - Сравнение двух ревизий перевода на основе их самой длинной общей подпоследовательности.</p>
      <p>Применен для глубокого анализа изменений в переводе.</p>
      <a style="color: blue;" target="_blank" href="https://en.wikipedia.org/wiki/Longest_common_subsequence_problem">(см. Wikipedia)</a>
    </div>
    <div>
      <el-select v-model="search.user_id" size="mini" placeholder="Пользователь" clearable>
        <el-option v-for="(user, index) in users" :key="index" :value="user.id" :label="user.name + ' ' + user.email" />
      </el-select>
      <el-date-picker
        v-model="search.dateBetween"
        :picker-options="pickerOptions"
        size="mini"
        clearable
        range-separator="-"
        type="daterange"
        start-placeholder="Start date"
        end-placeholder="End date"
        value-format="timestamp"
      />
      <el-button size="mini" type="success" @click="getHistory">
        Показать
      </el-button>
    </div>
    <div>
      <el-table v-if="history_translate.data" :data="history_translate.data" :summary-method="getSummaries" show-summary>
        <el-table-column prop="user_name" label="Пользователь" />
        <el-table-column prop="date" label="Период" />
        <el-table-column prop="count_with_space" label="Кол-во" />
        <el-table-column prop="count_without_space" label="Кол-во без пробелов" />
        <el-table-column align="right" label="Информация" width="150">
          <template slot-scope="{row}">
            <el-button size="mini" type="primary" icon="el-icon-info" @click="openInfo(row)" />
          </template>
        </el-table-column>
      </el-table>
    </div>
    <el-dialog :visible.sync="open_dialog" class="select-scroll" title="Информация" fullscreen modal-append-to-body :lock-scroll="true" :close-on-click-modal="false">
      <div>
        <back-to-top show style="z-index: 9999999999;" class-to-top="select-scroll" />
      </div>
      <div style="text-align: center;">
        <p>LCS - Сравнение двух ревизий перевода на основе их самой длинной общей подпоследовательности.</p>
        <p> Применен для глубокого анализа изменений в переводе.</p>
        <a style="color: blue;" target="_blank" href="https://en.wikipedia.org/wiki/Longest_common_subsequence_problem">(см. Wikipedia)</a>
        <p>( Кол-во с пробелами ) - ( Кол-во без пробелов )</p>
      </div>
      <el-table v-if="info" :data="info">
        <el-table-column>
          <template slot-scope="{row}">
            <diff :diff="row" />
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
    <back-to-top :visibility-height="300" :back-position="50" transition-name="fade" />
    <div class="el-pagination-container">
      <el-pagination
        v-if="history_translate.meta"
        :page-sizes="[10, 25, 50]"
        :page-size="search.to"
        :total="history_translate.meta.total"
        background
        style="text-align: center; position: fixed; width: 100%; bottom: 15px;"
        layout="total, sizes, prev, pager, next"
        :current-page="history_translate.meta.current_page"
        @size-change="(e) => { search.to = e; getHistory() }"
        @current-change="(e) => { search.page = e; getHistory() }"
      />
    </div>
  </div>
</template>

<script>
import { list } from '@/api/api-laravel'
import Diff from '@/views/history_translate/diff'
import BackToTop from '@/components/BackToTop/index'

export default {
  name: 'HistoryTranslate',
  components: { BackToTop, Diff },
  data() {
    return {
      search: {
        user_id: '',
        dateBetween: null,
        to: 10,
        page: 1,
        timezone: (-1 * (new Date()).getTimezoneOffset()) * 60000
      },
      users: [],
      history_translate: {},
      info: null,
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      }
    }
  },
  computed: {
    open_dialog: { get() { return !!this.info }, set(v) {
      this.info = v ? this.info : null
    } }
  },
  created() {
    this.getHistory()
    this.getUsers()
  },
  methods: {
    getSummaries({ columns, data }) {
      const title = 'Итого на текущей вкладке'
      const cols = columns.map(x => '')
      cols[0] = title

      let count_with_space = 0
      let count_without_space = 0

      this.history_translate.data.forEach((res) => {
        count_with_space += parseInt(res.count_with_space)
        count_without_space += parseInt(res.count_without_space)
      })

      cols[2] = count_with_space
      cols[3] = count_without_space
      return cols
    },
    getHistory() {
      list('history_translate', this.search).then(res => {
        this.history_translate = res.data
      })
    },
    getUsers() {
      list('user').then(res => {
        this.users = res.data
      })
    },
    openInfo(row) {
      list('history_translate', { date: row.date, user_id: row.user_id }).then(res => {
        this.info = res.data
      })
    }
  }
}
</script>

<style scoped>

</style>
