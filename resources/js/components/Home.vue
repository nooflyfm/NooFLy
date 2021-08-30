<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
          <el-row :gutter="24">
              <el-col :span="6" :xs="24" :sm="24" :md="12" :lg="6" :xl="6" class="mb-4">
                  <div class="card bg-primary text-white">
                      <div class="card-header">
                          Active Cashes
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-wallet"></i>&nbsp;{{ stat_counts.cashes }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="6" :xs="24" :sm="24" :md="12" :lg="6" :xl="6" class="mb-4">
                  <div class="card bg-danger text-white">
                      <div class="card-header">
                          All Transactions
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-money-check"></i>&nbsp;{{ stat_counts.transactions }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="6" :xs="24" :sm="24" :md="12" :lg="6" :xl="6" class="mb-4">
                  <div class="card bg-warning">
                      <div class="card-header">
                          Active Piggybanks
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-piggy-bank"></i>&nbsp;{{ stat_counts.piggybanks }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="6" :xs="24" :sm="24" :md="12" :lg="6" :xl="6" class="mb-4">
                  <div class="card bg-secondary text-white">
                      <div class="card-header">
                          Active Currencies
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fab fa-btc"></i>&nbsp;{{ stat_counts.currencies }}</span>
                      </div>
                  </div>
              </el-col>
          </el-row>
          <el-row :gutter="24" class="mb-4">
              <el-col :span="24">
                  <div class="card">
                      <div class="card-header bg-success text-white">
                          Active cashes balances history
                      </div>
                      <div class="card-body">
                          <el-table :data="cashes_balances" border stripe style="width: 100%" @sort-change="filterChange">
                              <el-table-column label="Currency" prop="currency" sortable="custom">
                                  <template slot-scope="scope">
                                      {{ scope.row.currency }}
                                  </template>
                              </el-table-column>
                              <el-table-column label="Cash" prop="name" sortable="custom"></el-table-column>
                              <el-table-column label="Date" prop="date"></el-table-column>
                              <el-table-column label="Balance" prop="balance">
                                  <template slot-scope="scope">
                                      {{ scope.row.symbol }}{{ scope.row.balance }}
                                  </template>
                              </el-table-column>
                          </el-table>
                          <el-pagination
                              v-if="!loading"
                              class="pagination mt-3"
                              background
                              layout="prev, pager, next"
                              :page-size="limit"
                              :total="totalElements"
                              :current-page.sync="number"
                              @current-change="setCurrentPage"
                          >
                          </el-pagination>
                      </div>
                  </div>
              </el-col>
          </el-row>
          <el-row :gutter="24" class="mb-4">
              <el-col :span="16" :xs="24" :sm="24" :md="16" :lg="16" :xl="16" class="mb-4">
                  <div class="card mb-4" v-if="piggybanks.length > 0">
                      <div class="card-header bg-success text-white">
                          Active piggybanks statuses
                      </div>
                      <div class="card-body">
                          <el-row :gutter="24" v-for="item in piggybanks" :key="item.id">
                              <span>{{ item.name }}</span>
                              <div class="progress">
                                  <div class="progress-bar" role="progressbar" :style="'width: ' + item.percent + '%;'" :aria-valuenow="item.percent" aria-valuemin="0" aria-valuemax="100">{{ item.balance }}</div>
                              </div>
                          </el-row>
                      </div>
                  </div>
                  <div class="card mb-4">
                      <div class="card-header bg-success text-white">
                          Amounts of popular transactions categories
                      </div>
                      <div class="card-body">
                          <bar-chart :height="400" :chart-data="categories_data"></bar-chart>
                      </div>
                  </div>
              </el-col>
              <el-col :span="8" :xs="24" :sm="24" :md="8" :lg="8" :xl="8" class="mb-4 float-right">
                  <div class="card mb-4">
                      <div class="card-header bg-success text-white">
                          Money amounts in active currencies
                      </div>
                      <div class="card-body">
                          <el-table :data="cashes_amounts" border stripe style="width: 100%">
                              <el-table-column label="Currency" prop="currency"></el-table-column>
                              <el-table-column label="Amount" prop="summ"></el-table-column>
                          </el-table>
                      </div>
                  </div>
                  <div class="card">
                      <div class="card-header bg-success text-white">
                          Active cashes balances
                      </div>
                      <div class="card-body">
                          <el-table :data="cashes" border stripe style="width: 100%">
                              <el-table-column label="Name" prop="name"></el-table-column>
                              <el-table-column label="Balance" prop="current_balance">
                                  <template slot-scope="scope">
                                      {{ scope.row.curr_symbol }}{{ scope.row.current_balance }}
                                  </template>
                              </el-table-column>
                          </el-table>
                      </div>
                  </div>
              </el-col>
          </el-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LineChart from "./assets/LineChart"
import BarChart from "./assets/BarChart"

export default {
  name: 'Home',
  props: ['homeurl'],
  data() {
    return {
      loading: true,
      page_title: 'Dashboard',
      stat_counts: [],
      balances: [],
      cashes_balances: [],
      limit: 5,
      offset: 0,
      totalElements: 0,
      number: 0,
      search: '&sort=id__ASC',
      piggybanks: [],
      cashes_amounts: [],
      categories_data: {},
      cashes: [],
    };
  },
  components: {
    LineChart,
    BarChart
  },
  methods: {
    async getCounts() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/stat/counts`)
        this.stat_counts = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getCashesBalances() {
      try {
        this.loading = false;
        const {data} = await axios.get(`${this.homeurl}/stat/balances/data/?limit=${this.limit}&offset=${this.offset}&sort=${this.search}`)
        this.cashes_balances = data.items;
        this.totalElements = data.total;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async setCurrentPage(page) {
      this.number = page
      this.offset = ((this.number - 1) * this.limit);
      await this.getCashesBalances();
    },
    filterChange (row) {
      if (row.order === 'ascending') {
        this.search = `&sort=${row.prop}__DESC`
      } else if (row.order === 'descending') {
        this.search = `&sort=${row.prop}__ASC`
      } else {
        this.search = ''
      }
    },
    async getPiggybanks() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/piggybanks/data/?limit=5000&offset=0&active=1`)
        this.piggybanks = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getAmounts() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/stat/amounts/data`)
        this.cashes_amounts = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getTransactionsCategories() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/stat/categories/data`)
        this.categories_data = {
          labels: data.items.labels,
          datasets: data.items.datasets
        }
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getCashes() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/cashes/data/?limit=5000&offset=0&active=1`)
        this.cashes = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
  },
  watch: {
    async search() {
      await this.getCashesBalances()
    },
  },
  async mounted() {
    this.loading = false
    await this.getCashesBalances()
    await this.getCounts()
    await this.getPiggybanks()
    await this.getAmounts()
    await this.getTransactionsCategories()
    await this.getCashes()
  },
}
</script>

<style lang="scss" scoped>
  .el-row {
    margin-bottom: 10px;
    &:last-child {
      margin-bottom: 0;
    }
  }
</style>
