<template>
  <div class="dashboard">
    <h1>Dashboard</h1>

    <el-row :gutter="20" class="stats-row">
      <el-col :span="6">
        <el-card shadow="hover">
          <template #header>Total Projects</template>
          <div class="stat-value">{{ stats.projects.total }}</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover">
          <template #header>Active Projects</template>
          <div class="stat-value">{{ stats.projects.byStatus.active || 0 }}</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover">
          <template #header>Total Tasks</template>
          <div class="stat-value">{{ stats.tasks.total }}</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover">
          <template #header>Overdue Tasks</template>
          <div class="stat-value text-danger">{{ stats.tasks.overdue }}</div>
        </el-card>
      </el-col>
    </el-row>

    <el-card class="welcome-card" shadow="hover">
      <template #header>Welcome back! 👋</template>
      <p>
        This is a Symfony + Vue project management system demonstrating modern
        full-stack development with JWT auth, Redis caching, RabbitMQ queues,
        Docker, and automated testing.
      </p>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { dashboardApi } from '@/api/dashboard'

const stats = ref({
  projects: { total: 0, byStatus: {} },
  tasks: { total: 0, overdue: 0 },
})

onMounted(async () => {
  try {
    const response = await dashboardApi.getStats()
    stats.value = response.data.stats
  } catch (error) {
    console.error('Failed to load dashboard stats', error)
  }
})
</script>

<style scoped>
.dashboard h1 {
  margin-bottom: 24px;
  color: #303133;
}

.stats-row {
  margin-bottom: 24px;
}

.stat-value {
  font-size: 32px;
  font-weight: bold;
  color: #409eff;
  text-align: center;
}

.stat-value.text-danger {
  color: #f56c6c;
}

.welcome-card {
  margin-top: 20px;
}

.welcome-card p {
  color: #606266;
  line-height: 1.8;
}
</style>
