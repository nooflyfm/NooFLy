<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
            <el-row :gutter="24" class="mb-4">
                <el-col :span="24">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            Select currency
                        </div>
                        <div class="card-body">
                            <el-select v-model="budgetCurrency" placeholder="Select" @change="redirectToCurrency()">
                                <el-option
                                    v-for="item in currencies"
                                    :key="item.id"
                                    :label="item.name + ' (' + item.symbol + ')'"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                </el-col>
            </el-row>
            <div>
                <el-row :gutter="24" v-if="cashes.length > 0" class="mb-4">
                    <el-col :span="24">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Add expense category
                            </div>
                            <div class="card-body">
                                <el-form :model="addform" ref="addform" class="demo-ruleForm" label-position="top" label-width="100px" :rules="addformRules">
                                    <el-row :gutter="24">
                                        <el-col :span="12" :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                            <el-form-item label="Category" prop="category">
                                                <el-input v-model="addform.category" required></el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12" :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                            <el-form-item label="Summ" prop="summ">
                                                <el-input type="number" v-model="addform.summ" required></el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-form-item>
                                        <el-button type="success" icon="el-icon-plus" @click="submitAddForm()">Add</el-button>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </div>
                    </el-col>
                </el-row>
                <el-row :gutter="24" v-if="cashes.length > 0" class="mb-4">
                    <el-col :span="12" :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Budget statistics
                            </div>
                            <div class="card-body">
                                <doughnut-chart :height="250" :chart-data="chart_data"></doughnut-chart>
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="12" :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Calculating
                            </div>
                            <div class="card-body">
                                <el-table :data="stats" border stripe style="width: 100%">
                                    <el-table-column label="Incomes" prop="income_summ">
                                        <template slot-scope="scope">
                                            {{ scope.row.curr_symbol }}{{ scope.row.income_summ }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="Expenses" prop="expense_summ">
                                        <template slot-scope="scope">
                                            {{ scope.row.curr_symbol }}{{ scope.row.expense_summ }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="Free Money" prop="free_money">
                                        <template slot-scope="scope">
                                            {{ scope.row.curr_symbol }}{{ scope.row.free_money }}
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </div>
                    </el-col>
                </el-row>
                <el-row :gutter="24" class="mb-4">
                    <el-col :span="12" :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Cashes in budget
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
                    <el-col :span="12" :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Expense categories
                            </div>
                            <div class="card-body">
                                <el-table :data="items" border stripe style="width: 100%" class="mb-3">
                                    <el-table-column label="Name" prop="category"></el-table-column>
                                    <el-table-column label="Summ" prop="summ"></el-table-column>

                                    <el-table-column label="Actions" align="right" width="240">
                                        <template slot-scope="scope">
                                            <el-popconfirm
                                                cancelButtonText='No'
                                                confirmButtonText='Yes'
                                                title="Delete category?"
                                                @confirm="removeItem(scope.row)"
                                                @onConfirm="removeItem(scope.row)"
                                            >
                                                <el-button slot="reference" type="danger" class="ml-2" icon="el-icon-delete"></el-button>
                                            </el-popconfirm>
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
  </div>
</template>

<script>
import DoughnutChart from "./assets/DoughnutChart"

export default {
  name: 'MyBudget',
  props: ['homeurl', 'currency'],
  data() {
    return {
      loading: true,
      page_title: 'Budget',
      currencies: [],
      budgetCurrency: '',
      items: [],
      cashes: [],
      stats: [{
        'income_summ': 0,
        'expense_summ': 0,
        'free_money': 0,
        'curr_symbol': '',
      }],
      income_summ: 0,
      addform: {
        'category': '',
        'summ': '',
      },
      addformRules: {
          category: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          summ: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
      },
      chart_data: {},
    };
  },
  components: {
    DoughnutChart
  },
  methods: {
    async getCurrencies() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/currencies/data/1`)
        this.currencies = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getData() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/budget/${this.currency}/data`)
        this.items = data.items;
        this.stats[0].expense_summ = data.expense_summ;
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
        const {data} = await axios.get(`${this.homeurl}/budget/${this.currency}/cashes`)
        this.cashes = data.items;
        this.stats[0].income_summ = data.income_summ;
        this.stats[0].curr_symbol = data.curr_symbol;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    getCurr(id) {
        id = Math.floor(id);
        const theItem = this.currencies.filter(_=>_.id===id);
        if(typeof theItem[0] === 'undefined') {
            return {
                id: '',
                name: 'Select...',
            };
        }
        return theItem[0].id;
    },
    redirectToCurrency() {
        window.location.href = this.homeurl + '/budget/' + this.budgetCurrency
    },
    submitAddForm() {
        this.$refs['addform'].validate((valid) => {
            if (valid) {
                this.addItem()
            } else {
                return false;
            }
        });
    },
    async addItem() {
        try {
            this.loading = true
            let formData = new FormData();
            formData.append('category', this.addform.category)
            formData.append('summ', this.addform.summ)
            await axios.post(`${this.homeurl}/budget/${this.currency}/add`, formData)
            this.$notify.success({
                title: 'Expense category added!',
            });
            this.addform.category = ''
            this.addform.summ = ''
            this.loading = false
            await this.getAll()
        } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application Error!',
            });
      }
    },
    async removeItem(row) {
      try {
        await axios.delete(`${this.homeurl}/budget/${this.currency}/${row.id}`)
        var rowIndex = this.items.indexOf(row);
        this.items.splice(rowIndex, 1);
        this.$notify.success({
            title: 'Category Deleted!',
        });
        await this.getAll()
      } catch (e) {
          this.loading = true;
          this.$notify.error({
              title: 'Application error!',
          });
      }
    },
    async calculateFn() {
      return this.stats[0].free_money = (this.stats[0].income_summ - this.stats[0].expense_summ)
    },
    async chartCalc() {
      this.chart_data = {
        labels: ['Incomes', 'Expenses', 'Free Money'],
        datasets: [
          {
            label: 'Summ',
            backgroundColor: ['#09e53c', '#ff0000', '#009dff'],
            data: [this.stats[0].income_summ, this.stats[0].expense_summ, this.stats[0].free_money],
          },
        ]
      }
    },
    async getAll() {
      await this.getCurrencies()
      await this.getData()
      await this.getCashes()
      await this.calculateFn()
      await this.chartCalc()
    },
  },
  watch: {
  },
  async mounted() {
    this.loading = false
    await this.getAll()
    this.$nextTick(() => {
        this.budgetCurrency = this.getCurr(this.currency)
    })
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
