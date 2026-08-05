<template>
  <el-header class="header">
    <div class="header-left">
      <h2>Project Management</h2>
    </div>
    <div class="header-right">
      <span v-if="authStore.user" class="user-name">
        {{ authStore.user.fullName }}
      </span>
      <el-button type="danger" size="small" @click="handleLogout">Logout</el-button>
    </div>
  </el-header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #fff;
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
}

.header-left h2 {
  margin: 0;
  font-size: 20px;
  color: #303133;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-name {
  color: #606266;
  font-size: 14px;
}
</style>
