<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
            <el-form :model="addform" ref="addform" class="demo-ruleForm" label-position="top" label-width="100px" :rules="addformRules">
                <el-row :gutter="24">
                    <el-col :span="24">
                        <div class="card mb-4">
                            <div class="card-body">
                                <el-form-item label="Name" prop="name">
                                    <el-input v-model="addform.name" required></el-input>
                                </el-form-item>
                                <el-form-item label="E-mail" prop="email">
                                    <el-input v-model="addform.email" required></el-input>
                                </el-form-item>
                                <el-form-item label="New password" prop="new_password">
                                    <el-input type="password" v-model="addform.new_password" placeholder="From 8 symbols"></el-input>
                                </el-form-item>
                                <el-form-item label="Confirm new password" prop="new_password2">
                                    <el-input type="password" v-model="addform.new_password2"></el-input>
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
  name: 'UserProfile',
  props: {
    homeurl: {
      type: String,
      required: true,
    },
    user: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      loading: true,
      page_title: 'My Profile',
      action_button_text: 'Save',
      item: JSON.parse(this.user),
      addform: {
        'name': '',
        'email': '',
        'new_password': '',
        'new_password2': '',
      },
      addformRules: {
          name: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
          email: [
            { required: true, message: 'Required field', trigger: 'blur' },
          ],
      },
    };
  },
  components: {
  },
  methods: {
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
            if (this.addform.new_password.trim() != '') {
              if (this.addform.new_password.length < 8) {
                this.$notify.error({
                  title: 'Password is short!',
                });
                return
              }
              if (this.addform.new_password2.trim() == '') {
                this.$notify.error({
                  title: 'Confirm new password!',
                });
                return
              }
              if (this.addform.new_password2.trim() !== this.addform.new_password.trim()) {
                this.$notify.error({
                  title: 'Passwords does not matches!',
                });
                return
              }
            } else {
              this.addform.new_password = ''
            }
            this.loading = true
            await axios.put(`${this.homeurl}/profile/save`, {
                name: this.addform.name,
                email: this.addform.email,
                password: this.addform.new_password,
            })
            if (this.addform.new_password.trim() != '') {
              await axios.post(`${this.homeurl}/logout`)
              window.location.reload()
            }
            this.addform.new_password = ''
            this.addform.new_password2 = ''
            this.$notify.success({
                title: 'Profile updated!',
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
    this.$nextTick(() => {
        this.addform.name = this.item.name
        this.addform.email = this.item.email
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
