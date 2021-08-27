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
                                <el-form-item label="Symbol" prop="symbol">
                                    <el-input v-model="addform.symbol" required></el-input>
                                </el-form-item>
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="8" :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                        <div class="card mb-4">
                            <div class="card-body">
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
  name: 'EditCurrency',
  props: ['homeurl', 'id'],
  data() {
    return {
      loading: true,
      page_title: 'Edit Currency',
      action_button_text: 'Save',
      item: {},
      addform: {
        'name': '',
        'symbol': '',
        'active': false,
      },
      addformRules: {
          name: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          symbol: [
            { required: true, message: 'Required field', trigger: 'blur' },
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
        const {data} = await axios.get(`${this.homeurl}/edit/currency/data/${this.id}`)
        this.item = data.item;
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
            await axios.put(`${this.homeurl}/edit/savecurrency/${this.id}`, {
              name: this.addform.name,
              symbol: this.addform.symbol,
              active: Number(this.addform.active),
            })
            this.$notify.success({
                title: 'Currency saved!',
            });
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
    await this.getData()
    this.$nextTick(() => {
        this.addform.name = this.item[0].name
        this.addform.symbol = this.item[0].symbol
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
