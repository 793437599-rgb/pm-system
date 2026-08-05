<template>
  <el-container class="layout-container">
    <Sidebar />
    <el-container direction="vertical">
      <Header />
      <el-main class="main-content">
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import Sidebar from './Sidebar.vue'
import Header from './Header.vue'
</script>

<style scoped>
.layout-container {
  min-height: 100vh;
}

.main-content {
  padding: 20px;
  background-color: #f5f7fa;
}
</style>
