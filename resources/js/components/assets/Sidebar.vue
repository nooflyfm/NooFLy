<template>
  <el-menu
      :router="false"
      :mode="menu_mode"
      :collapse="isCollapse"
      @open="handleOpen"
      @close="handleClose"
      active-text-color="#fff"
      background-color="#444"
      class="main-menu"
      default-active="/"
      text-color="#fff">

    <el-menu-item v-if="isCollapse && isMobile" index="01" @click="() => this.isCollapse = false">
        <i class="el-icon-s-unfold"></i>
        <span slot="title">Expand</span>
    </el-menu-item>
    <el-menu-item v-else-if="!isCollapse && isMobile" index="01" @click="() => this.isCollapse = true">
        <i class="el-icon-s-fold"></i>
        <span slot="title">Collapse</span>
    </el-menu-item>
    
    <el-menu-item index="/" @click="goto('/')">
        <i class="fas fa-tachometer-alt"></i>
        <span slot="title">Dashboard</span>
    </el-menu-item>

    <el-submenu index="1" class="bg-success">
      <template slot="title">
        <i class="fas fa-plus"></i>
        <span slot="title">Add</span>
      </template>
        <el-menu-item index="10" @click="goto('/add/transaction')">
            <i class="fas fa-money-check"></i>
            <span slot="title">Transaction</span>    
        </el-menu-item>
        <el-menu-item index="11" @click="goto('/add/cash')">
            <i class="fas fa-wallet"></i>
            <span slot="title">Cash</span>
        </el-menu-item>
        <el-menu-item index="12" @click="goto('/add/piggybank')">
            <i class="fas fa-piggy-bank"></i>
            <span slot="title">Piggybank</span>
        </el-menu-item>
        <el-menu-item index="13" @click="goto('/add/currency')">
            <i class="fab fa-btc"></i>
            <span slot="title">Currency</span>
        </el-menu-item>
    </el-submenu>

    <el-menu-item index="2" @click="goto('/transactions')">
        <i class="fas fa-money-check"></i>
        <span slot="title">Transactions</span>
    </el-menu-item>

    <el-menu-item index="3" @click="goto('/cashes')">
        <i class="fas fa-wallet"></i>
        <span slot="title">Cashes</span>
    </el-menu-item>

    <el-menu-item index="4" @click="goto('/piggybanks')">
        <i class="fas fa-piggy-bank"></i>
        <span slot="title">Piggy Banks</span>
    </el-menu-item>

    <el-menu-item index="5" @click="goto('/currencies')">
        <i class="fab fa-btc"></i>
        <span slot="title">Currencies</span>
    </el-menu-item>

    <el-menu-item index="6" @click="goto('/budget')">
        <i class="fas fa-coins"></i>
        <span slot="title">Budget</span>
    </el-menu-item>

  </el-menu>
</template>

<script>
export default {
  name: "Sidebar",
  props: ['homeurl'],
  data() {
    return {
      menu_mode: 'vertical',
      isCollapse: true,
      isMobile: false,
    };
  },
  methods: {
    handleOpen(key, keyPath) {
      console.log(key, keyPath);
    },
    handleClose(key, keyPath) {
      console.log(key, keyPath);
    },
    goto(loctn) {
        window.location.href = this.homeurl + loctn
    },
  },
  mounted() {
    if (window.innerWidth < 800) {
      this.isCollapse = true
      this.isMobile = true
    } else {
      this.isCollapse = false
      this.isMobile = false
    }
  }
}
</script>

<style lang="scss" scoped>
.main-menu:not(.el-menu--collapse) {
    width: 17%;
}
.main-menu {
  margin-top: 54px;
  height: 100vh;
  padding: 0 0 0 0 !important;
  position: fixed;
  overflow: scroll;
  width: 17%;
  z-index: 1001;
  
  .el-menu-item {
    border-top: 1px solid #fff;
    text-align: left;

    i {
      color: #fff;
    }
  }

  .el-submenu {
    border-top: 1px solid #fff;
    padding-left: 0 !important;
    text-align: left;

    .el-menu-item {

      i {
        color: #fff;
      }
    }
  }
}
@media only screen and (max-width: 800px) {
    .main-menu {
        width: 10%;
    }
    .main-menu:not(.el-menu--collapse) {
        width: 100%;
    }
}
</style>
