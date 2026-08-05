<template>
  <el-aside width="220px" class="sidebar">
    <div class="logo">
      <el-icon><Management /></el-icon>
      <span>PM System</span>
    </div>
    <el-menu
      :default-active="$route.path"
      router
      background-color="#304156"
      text-color="#bfcbd9"
      active-text-color="#409EFF"
    >
      <el-menu-item index="/">
        <el-icon><DataLine /></el-icon>
        <span>Dashboard</span>
      </el-menu-item>
      <el-menu-item index="/projects">
        <el-icon><Folder /></el-icon>
        <span>Projects</span>
      </el-menu-item>
      <el-menu-item index="/tasks">
        <el-icon><List /></el-icon>
        <span>Tasks</span>
      </el-menu-item>
    </el-menu>
  </el-aside>
</template>

<script setup>
import { useRoute } from 'vue-router'

const $route = useRoute()
</script>

<style scoped>
.sidebar {
  background-color: #304156;
  color: #bfcbd9;
}

.logo {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: bold;
  color: #fff;
  border-bottom: 1px solid #1f2d3d;
}

.logo .el-icon {
  margin-right: 10px;
  font-size: 24px;
}

.logo span {
  margin-left: 8px;
}
</style>
