<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
          <el-row :gutter="24">
              <el-col :span="8" :xs="24" :sm="24" :md="12" :lg="8" :xl="8" class="mb-4">
                  <div class="card bg-success text-white">
                      <div class="card-header">
                          All Users
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-users"></i>&nbsp;{{ stat_counts.users }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="8" :xs="24" :sm="24" :md="12" :lg="8" :xl="8" class="mb-4">
                  <div class="card bg-primary text-white">
                      <div class="card-header">
                          All Cashes
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-wallet"></i>&nbsp;{{ stat_counts.cashes }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="8" :xs="24" :sm="24" :md="12" :lg="8" :xl="8" class="mb-4">
                  <div class="card bg-white">
                      <div class="card-header">
                          All Cashes Balances
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-balance-scale"></i>&nbsp;{{ stat_counts.cashes_balances }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="8" :xs="24" :sm="24" :md="12" :lg="8" :xl="8" class="mb-4">
                  <div class="card bg-danger text-white">
                      <div class="card-header">
                          All Transactions
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-money-check"></i>&nbsp;{{ stat_counts.transactions }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="8" :xs="24" :sm="24" :md="12" :lg="8" :xl="8" class="mb-4">
                  <div class="card bg-warning">
                      <div class="card-header">
                          All Piggybanks
                      </div>
                      <div class="card-body" style="text-align: center;">
                          <span style="font-size: 50px;"><i class="fas fa-piggy-bank"></i>&nbsp;{{ stat_counts.piggybanks }}</span>
                      </div>
                  </div>
              </el-col>
              <el-col :span="8" :xs="24" :sm="24" :md="12" :lg="8" :xl="8" class="mb-4">
                  <div class="card bg-secondary text-white">
                      <div class="card-header">
                          All Currencies
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
                          Users registrations this year
                      </div>
                      <div class="card-body">
                          <line-chart :height="400" :chart-data="users_regs_year"></line-chart>
                      </div>
                  </div>
              </el-col>
          </el-row>
          <el-row :gutter="24" class="mb-4">
              <el-col :span="24">
                  <div class="card">
                      <div class="card-header bg-success text-white">
                          Users registrations this month
                      </div>
                      <div class="card-body">
                          <line-chart :height="400" :chart-data="users_regs_month"></line-chart>
                      </div>
                  </div>
              </el-col>
          </el-row>
          <el-row :gutter="24" class="mb-4">
              <el-col :span="24">
                  <div class="card">
                      <div class="card-header bg-success text-white">
                          Users List
                      </div>
                      <div class="card-body">
                          <el-row :gutter="24">
                              <el-row :gutter="20">
                                  <el-col :span="4" :xs="24" :sm="12" :md="12" :lg="4" :xl="4">
                                      <span class="demo-input-label">Name: </span><br />
                                      <el-input v-model="filterUserName" />
                                  </el-col>
                                  <el-col :span="4" :xs="24" :sm="12" :md="12" :lg="4" :xl="4">
                                      <span class="demo-input-label">E-mail: </span><br />
                                      <el-input v-model="filterUserEmail" />
                                  </el-col>
                              </el-row>
                              <el-row :gutter="4">
                                  <el-col :span="2" :xs="24" :sm="12" :md="8" :lg="2" :xl="2">
                                      <el-button type="success" @click="getUsers">Search</el-button>
                                  </el-col>
                                  <el-col :span="2" :xs="24" :sm="12" :md="6" :lg="2" :xl="2">
                                      <el-button type="danger" @click="resetFilters">Reset</el-button>
                                  </el-col>
                              </el-row>
                          </el-row>
                          <el-table :data="users" border stripe style="width: 100%" @sort-change="filterChange">
                              <el-table-column label="ID" prop="id" sortable="custom"></el-table-column>
                              <el-table-column label="Name" prop="name" sortable="custom"></el-table-column>
                              <el-table-column label="E-mail" prop="email" sortable="custom"></el-table-column>
                              <el-table-column label="Banned?" prop="is_banned" v-if="!viewMore">
                                  <template slot-scope="scope">
                                      <el-tag v-if="scope.row.is_banned" type="danger" style="cursor: pointer" @click="changeUserBanStatus(scope.row, '0')">YES</el-tag>
                                      <el-tag v-else type="success" style="cursor: pointer" @click="changeUserBanStatus(scope.row, '1')">NO</el-tag>
                                  </template>
                              </el-table-column>
                              <el-table-column label="Created at" prop="created_at" sortable="custom" v-if="!viewMore"></el-table-column>
                              <el-table-column label="Updated at" prop="updated_at" sortable="custom" v-if="!viewMore"></el-table-column>

                              <el-table-column label="Currencies" prop="currencies" v-if="viewMore"></el-table-column>
                              <el-table-column label="Cashes" prop="cashes" v-if="viewMore"></el-table-column>
                              <el-table-column label="Piggybanks" prop="piggybanks" v-if="viewMore"></el-table-column>
                              <el-table-column label="Transactions" prop="transactions" v-if="viewMore"></el-table-column>

                              <el-table-column label="Actions" align="right" width="240">
                                  <template slot-scope="scope">
                                      <el-button v-if="!viewMore" type="primary" icon="el-icon-view" @click="() => {viewMore = true}"></el-button>
                                      <el-button v-else type="secondary" icon="el-icon-view" @click="() => {viewMore = false}"></el-button>
                                      <el-popconfirm
                                          cancelButtonText='No'
                                          confirmButtonText='Yes'
                                          title="Delete user and all his data?"
                                          @confirm="removeUser(scope.row)"
                                          @onConfirm="removeUser(scope.row)"
                                      >
                                          <el-button slot="reference" type="danger" class="ml-2" icon="el-icon-delete"></el-button>
                                      </el-popconfirm>
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
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LineChart from "./assets/LineChart"
import BarChart from "./assets/BarChart"

export default {
  name: 'AdminHome',
  props: ['homeurl'],
  data() {
    return {
      loading: true,
      page_title: 'Administration. Dashboard',
      stat_counts: [],
      users: [],
      limit: 20,
      offset: 0,
      totalElements: 0,
      number: 0,
      search: '&sort=created_at__DESC',
      viewMore: false,
      filterUserName: '',
      filterUserEmail: '',
      users_regs_year: {},
      users_regs_month: {},
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
        const {data} = await axios.get(`${this.homeurl}/admin/counts/data`)
        this.stat_counts = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getUsers() {
      let filterss = ''
      let fName = ''
      let fEmail = ''
      if (this.filterUserName != '') {
        fName = `&name=${this.filterUserName}`
      }
      if (this.filterUserEmail != '') {
        fEmail = `&email=${this.filterUserEmail}`
      }
      filterss = `${fEmail}${fName}`
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/admin/users/data/?limit=${this.limit}&offset=${this.offset}${this.search}${filterss}`)
        this.users = data.items;
        this.totalElements = data.total;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async resetFilters() {
      this.filterUserName = ''
      this.filterUserEmail = ''
      await this.getUsers()
    },
    async changeUserBanStatus(row, statUs) {
        try {
            await axios.put(`${this.homeurl}/admin/user/ban/${row.id}`, {
              status: statUs,
            })
            row.is_banned = !row.is_banned
            this.$notify.success({
                title: 'User ban status changed!',
            })
        } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application Error!',
            });
      }
    },
    async removeUser(row) {
      try {
        await axios.delete(`${this.homeurl}/admin/users/remove/${row.id}`)
        this.$notify.success({
            title: 'The user and all his data was deleted!',
        });
        await this.getCounts()
        await this.getUsers()
        await this.getUsersYearRegs()
        await this.getUsersMonthRegs()
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
      await this.getUsers();
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
    async getUsersYearRegs() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/admin/users/regs/year`)
        this.users_regs_year = {
          labels: data.items.labels,
          datasets: [
            {
              label: data.items.datasets.label,
              backgroundColor: data.items.datasets.backgroundColor,
              borderColor: data.items.datasets.borderColor,
              pointBackgroundColor: data.items.datasets.pointBackgroundColor,
              pointBorderColor: data.items.datasets.pointBorderColor,
              data: data.items.datasets.data
            }
          ]
        }
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getUsersMonthRegs() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/admin/users/regs/month`)
        this.users_regs_month = {
          labels: data.items.labels,
          datasets: [
            {
              label: data.items.datasets.label,
              backgroundColor: data.items.datasets.backgroundColor,
              borderColor: data.items.datasets.borderColor,
              pointBackgroundColor: data.items.datasets.pointBackgroundColor,
              pointBorderColor: data.items.datasets.pointBorderColor,
              data: data.items.datasets.data
            }
          ]
        }
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
      await this.getUsers()
    },
  },
  async mounted() {
    this.loading = false
    await this.getCounts()
    await this.getUsers()
    await this.getUsersYearRegs()
    await this.getUsersMonthRegs()
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
