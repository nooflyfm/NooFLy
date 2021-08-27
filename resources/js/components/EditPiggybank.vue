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
                                <el-form-item label="Balance" prop="balance">
                                    <el-input type="number" v-model="addform.balance" required></el-input>
                                </el-form-item>
                                <el-form-item label="Target Summ" prop="target_summ">
                                    <el-input type="number" v-model="addform.target_summ" required></el-input>
                                </el-form-item>
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="8" :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <el-form-item label="Target Date" prop="target_date">
                                    <el-date-picker
                                        v-model="addform.target_date"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        placeholder="Pick a day"
                                        required>
                                    </el-date-picker>
                                </el-form-item>
                                <el-form-item label="Currency" prop="currency">
                                    <el-select v-model="addform.currency" placeholder="Select" required>
                                        <el-option
                                            v-for="item in currencies"
                                            :key="item.id"
                                            :label="item.name + ' (' + item.symbol + ')'"
                                            :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="Active?" prop="active">
                                    <el-switch v-model="addform.active" required></el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="success" icon="el-icon-check" @click="submitAddForm()">{{ action_button_text }}</el-button>
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
  name: 'EditPiggybank',
  props: ['homeurl', 'id'],
  data() {
    return {
      loading: true,
      page_title: 'Edit Piggy Bank',
      action_button_text: 'Save',
      item: {},
      currencies: [],
      addform: {
        'name': '',
        'balance': '',
        'target_summ': '',
        'target_date': '',
        'currency': '',
        'active': false,
      },
      addformRules: {
          name: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          balance: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          target_summ: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          target_date: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          currency: [
            { required: true, message: 'Required field', trigger: 'change' },
          ],
      },
    };
  },
  components: {
  },
  methods: {
    async getData() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/edit/piggybank/data/${this.id}`)
        this.item = data.item;
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
    submitAddForm() {
        this.$refs['addform'].validate((valid) => {
            if (valid) {
                this.saveItem()
            } else {
                return false;
            }
        });
    },
    async saveItem() {
        try {
            this.loading = true
            await axios.put(`${this.homeurl}/edit/savepiggybank/${this.id}`, {
                name: this.addform.name,
                balance: this.addform.balance,
                target_summ: this.addform.target_summ,
                target_date: this.addform.target_date,
                currency: this.addform.currency,
                active: Number(this.addform.active),
            })
            this.$notify.success({
                title: 'Piggy Bank Saved!',
            });
            this.loading = false
        } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application Error!',
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
  },
  watch: {
  },
  async mounted() {
    this.loading = false
    await this.getData()
    await this.getCurrencies()
    this.$nextTick(() => {
        this.addform.name = this.item[0].name
        this.addform.balance = this.item[0].balance
        this.addform.target_summ = this.item[0].target_summ
        this.addform.target_date= this.item[0].target_date
        this.addform.currency = this.getCurr(this.item[0].currency)
        this.addform.active = Boolean(this.item[0].active)
    });
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
