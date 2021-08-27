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
  name: 'AddPiggybank',
  props: ['homeurl'],
  data() {
    return {
      loading: true,
      page_title: 'Add Piggy Bank',
      action_button_text: 'Add',
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
            formData.append('balance', this.addform.balance)
            formData.append('target_summ', this.addform.target_summ)
            formData.append('target_date', this.addform.target_date)
            formData.append('currency', Number(this.addform.currency))
            formData.append('active', Number(this.addform.active))
            await axios.post(`${this.homeurl}/add/createpiggybank`, formData)
            this.$notify.success({
                title: 'Piggy Bank Created!',
            });
            this.addform.name = ''
            this.addform.balance = ''
            this.addform.target_summ = ''
            this.addform.target_date = ''
            this.addform.currency = ''
            this.addform.active = false
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
