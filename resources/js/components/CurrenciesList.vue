<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
            <el-table :data="items" border stripe style="width: 100%">
                <el-table-column label="Name" prop="name"></el-table-column>
                <el-table-column label="Symbol" prop="symbol"></el-table-column>
                <el-table-column label="Active?">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.active == '1'" type="success" style="cursor: pointer;" @click="toggleActive(scope.row, '0')"><i class="el-icon-circle-check"></i></el-tag>
                        <el-tag v-else type="danger" style="cursor: pointer;" @click="toggleActive(scope.row, '1')"><i class="el-icon-circle-close"></i></el-tag>
                    </template>
                </el-table-column>

                <el-table-column label="Actions" align="right" width="240">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="gotoEdit(scope.row)" icon="el-icon-edit"></el-button>
                        <el-popconfirm
                            v-if="scope.row.in_use == '0'"
                            cancelButtonText='No'
                            confirmButtonText='Yes'
                            title="Delete currency?"
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
    </div>
  </div>
</template>

<script>
export default {
  name: 'CurrenciesList',
  props: ['homeurl'],
  data() {
    return {
      loading: true,
      page_title: 'Currencies',
      items: [],
    };
  },
  components: {
  },
  methods: {
    async getData() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/currencies/data/0`)
        this.items = data.items;
        this.loading = false;
      } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application error!',
            });
      }
    },
    async toggleActive(row, activ) {
        try {
            await axios.put(`${this.homeurl}/edit/currency/active/${row.id}`, {
              active: activ,
            })
            row.active = activ
            this.$notify.success({
                title: 'Activity changed!',
            })
        } catch (e) {
            this.loading = true;
            this.$notify.error({
                title: 'Application Error!',
            });
      }
    },
    async removeItem(row) {
      try {
        if (row.in_use == '1') {
          this.$notify.error({
              title: 'The currency is in use!',
          });
          return;
        }
        await axios.delete(`${this.homeurl}/remove/currency/${row.id}`)
        var rowIndex = this.items.indexOf(row);
        this.items.splice(rowIndex, 1);
        this.$notify.success({
            title: 'Currency Deleted!',
        });
      } catch (e) {
          this.loading = true;
          this.$notify.error({
              title: 'Application error!',
          });
      }
    },
    gotoEdit(row) {
      window.location.href = this.homeurl + '/edit/currency/' + row.id
    },
  },
  watch: {
  },
  async mounted() {
    this.loading = false;
    await this.getData()
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
