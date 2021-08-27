<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
            <el-form :model="addform" ref="addform" class="demo-ruleForm" label-position="top" label-width="100px" :rules="addformRules">
                <el-row :gutter="24">
                    <el-col :span="16" :xs="24" :sm="24" :md="24" :lg="16" :xl="16">
                        <div class="card mb-4">
                            <div class="card-body">
                                <el-form-item label="Name" prop="name">
                                    <el-input v-model="addform.name" required></el-input>
                                </el-form-item>
                                <el-form-item label="Type" prop="type">
                                    <el-select v-model="addform.type" placeholder="Select" required>
                                        <el-option
                                            label="Income"
                                            value="income">
                                        </el-option>
                                        <el-option
                                            label="Expense"
                                            value="expense">
                                        </el-option>
                                        <el-option
                                            label="Transfer"
                                            value="transfer">
                                        </el-option>
                                        <el-option
                                            label="Piggybank"
                                            value="piggybank">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="Amount" prop="summ">
                                    <el-input type="number" v-model="addform.summ" required></el-input>
                                </el-form-item>
                                <el-form-item label="Date" prop="date">
                                    <el-date-picker
                                        v-model="addform.date"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        placeholder="Pick a day"
                                        required>
                                    </el-date-picker>
                                </el-form-item>
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="8" :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <el-form-item label="Add new category?">
                                    <el-switch v-model="addform.add_category" @change="() => this.addform.category = ''"></el-switch>
                                </el-form-item>
                                <el-form-item v-if="addform.add_category" label="New Category" prop="category">
                                    <el-input v-model="addform.category" required></el-input>
                                </el-form-item>
                                <el-form-item v-else label="Category" prop="category">
                                    <el-select v-model="addform.category" placeholder="Select" required>
                                        <el-option
                                            v-for="item in categories"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.name">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="Currency" prop="currency">
                                    <el-select v-model="addform.currency" placeholder="Select" required @change="getByCurrency()">
                                        <el-option
                                            v-for="item in currencies"
                                            :key="item.id"
                                            :label="item.name + ' (' + item.symbol + ')'"
                                            :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <div v-if="addform.currency != ''">
                                    <el-form-item label="Cash" prop="cash_from">
                                        <el-select v-model="addform.cash_from" placeholder="Select" required @change="() => this.addform.cash_to = ''">
                                            <el-option
                                                v-for="item in cashes"
                                                :key="item.id"
                                                :label="item.name + ' (' + item.curr_symbol + ')'"
                                                :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item v-if="addform.type == 'transfer'" label="To cash" prop="cash_to">
                                        <el-select v-model="addform.cash_to" placeholder="Select" required>
                                            <el-option
                                                v-show="item.id != addform.cash_from"
                                                v-for="item in cashes"
                                                :key="item.id"
                                                :label="item.name + ' (' + item.curr_symbol + ')'"
                                                :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item v-if="addform.type == 'piggybank'" label="To piggybank" prop="piggybank_to">
                                        <el-select v-model="addform.piggybank_to" placeholder="Select" required>
                                            <el-option
                                                v-for="item in piggybanks"
                                                :key="item.id"
                                                :label="item.name + ' (' + item.curr_symbol + ')'"
                                                :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </div>
                                <el-form-item>
                                    <el-button type="success" icon="el-icon-plus" @click="submitAddForm()">{{ action_button_text }}</el-button>
                                </el-form-item>
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </el-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddTransaction',
  props: ['homeurl'],
  data() {
    return {
      loading: true,
      page_title: 'Add Transaction',
      action_button_text: 'Add',
      categories: [],
      currencies: [],
      cashes: [],
      piggybanks: [],
      addform: {
        'name': '',
        'type': '',
        'summ': '',
        'date': '',
        'add_category': false,
        'category': '',
        'currency': '',
        'cash_from': '',
        'cash_to': '',
        'piggybank_to': '',
      },
      addformRules: {
          name: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          type: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
          summ: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          date: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          category: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
          currency: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
          cash_from: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
          cash_to: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
          piggybank_to: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
      },
    };
  },
  components: {
  },
  methods: {
    async getCategories() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/transactions/categories/data`)
        this.categories = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getCurrencies() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/currencies/data/1/?active=1`)
        this.currencies = data.items;
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
        const {data} = await axios.get(`${this.homeurl}/cashes/data/?limit=5000&offset=0&active=1&currency=${this.addform.currency}`)
        this.cashes = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getPiggybanks() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/piggybanks/data/?limit=5000&offset=0&active=1&currency=${this.addform.currency}`)
        this.piggybanks = data.items;
        this.totalElements = data.total;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async getByCurrency() {
      await this.getCashes()
      await this.getPiggybanks()
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
            formData.append('name', this.addform.name)
            formData.append('type', this.addform.type)
            formData.append('summ', this.addform.summ)
            formData.append('date', this.addform.date)
            formData.append('category', this.addform.category)
            formData.append('currency', Number(this.addform.currency))
            formData.append('from_cash', Number(this.addform.cash_from))
            formData.append('to_cash', Number(this.addform.cash_to))
            formData.append('to_piggybank', Number(this.addform.piggybank_to))
            await axios.post(`${this.homeurl}/add/createtransaction`, formData)
            this.$notify.success({
                title: 'Transaction created!',
            });
            this.addform.name = ''
            this.addform.type = ''
            this.addform.summ = ''
            this.addform.date = ''
            this.addform.add_category = false
            this.addform.category = ''
            this.addform.currency = ''
            this.addform.cash_from = ''
            this.addform.cash_to = ''
            this.addform.piggybank_to = ''
            this.loading = false
        } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application Error!',
            });
      }
    },
  },
  watch: {
  },
  async mounted() {
    this.loading = false
    await this.getCategories()
    await this.getCurrencies()
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
