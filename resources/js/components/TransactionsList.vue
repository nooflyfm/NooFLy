<template>
  <div v-loading="loading">
    <div class="main">
      <h3 class="page-title">{{ page_title }}</h3>
      <div class="main-content">
        <div v-if="!loading">
            <el-table :data="items" border stripe style="width: 100%" @sort-change="filterChange">
                <el-table-column label="Name" prop="name" sortable="custom"></el-table-column>
                <el-table-column label="Type" prop="type" sortable="custom"></el-table-column>
                <el-table-column label="Amount" prop="summ" sortable="custom">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.type == 'Income'" type="success">
                            <i class="fas fa-plus"></i> {{ scope.row.curr_symbol }}{{ scope.row.summ }}
                        </el-tag>
                        <el-tag v-if="scope.row.type == 'Expense'" type="danger">
                            <i class="fas fa-minus"></i> {{ scope.row.curr_symbol }}{{ scope.row.summ }}
                        </el-tag>
                        <el-tag v-if="scope.row.type == 'Transfer'" type="primary">
                            <i class="fas fa-exchange-alt"></i> {{ scope.row.curr_symbol }}{{ scope.row.summ }}
                        </el-tag>
                        <el-tag v-if="scope.row.type == 'Piggybank'" type="warning">
                            <i class="fas fa-minus"></i> <i class="fas fa-plus"></i> {{ scope.row.curr_symbol }}{{ scope.row.summ }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="Category" prop="category" sortable="custom"></el-table-column>
                <el-table-column label="From cash" prop="from_cash" sortable="custom">
                    <template slot-scope="scope">
                        {{ scope.row.from_cash_name }}
                    </template>
                </el-table-column>
                <el-table-column label="To cash" prop="to_cash" sortable="custom">
                    <template slot-scope="scope">
                        {{ scope.row.to_cash_name }}
                    </template>
                </el-table-column>
                <el-table-column label="To piggybank" prop="to_piggybank" sortable="custom">
                    <template slot-scope="scope">
                        {{ scope.row.to_piggybank_name }}
                    </template>
                </el-table-column>
                <el-table-column label="Date" prop="date" sortable="custom"></el-table-column>
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
    </div>
  </div>
</template>

<script>
export default {
  name: 'TransactionsList',
  props: ['homeurl'],
  data() {
    return {
      loading: true,
      page_title: 'Transactions',
      items: [],
      limit: 50,
      offset: 0,
      totalElements: 0,
      number: 0,
      search: '&sort=id__ASC',
    };
  },
  components: {
  },
  methods: {
    async getData() {
      try {
        this.loading = true;
        const {data} = await axios.get(`${this.homeurl}/transactions/data/?limit=${this.limit}&offset=${this.offset}${this.search}`)
        this.items = data.items;
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
      await this.getData();
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
  },
  watch: {
    async search() {
      await this.getData()
    },
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
