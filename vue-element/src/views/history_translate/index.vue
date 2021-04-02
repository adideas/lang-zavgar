<template>
  <div class="app-container">
    <div style="text-align: center;">
      <p>LCS - Сравнение двух ревизий перевода на основе их самой длинной общей подпоследовательности.</p>
      <p> Применен для глубокого анализа изменений в переводе.</p>
      <a style="color: blue;" target="_blank" href="https://en.wikipedia.org/wiki/Longest_common_subsequence_problem">(см. Wikipedia)</a>
    </div>
    <div>
      <el-table v-if="history_translate.data" :data="history_translate.data">
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
        user_id: ''
      },
      history_translate: [],
      info: null
    }
  },
  computed: {
    open_dialog: { get() { return !!this.info }, set(v) {
      this.info = v ? this.info : null
    } }
  },
  created() {
    this.getHistory()
  },
  methods: {
    getHistory() {
      list('history_translate').then(res => {
        this.history_translate = res.data
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
