<template>
  <div class="tasks">
    <h1>My Tasks</h1>

    <el-table :data="tasks" v-loading="loading" stripe>
      <el-table-column prop="title" label="Title" />
      <el-table-column prop="project.name" label="Project" />
      <el-table-column prop="status" label="Status">
        <template #default="{ row }">
          <el-tag :type="statusType(row.status)">{{ row.status }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="priority" label="Priority">
        <template #default="{ row }">
          <el-tag :type="priorityType(row.priority)" effect="dark">{{ row.priority }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="dueDate" label="Due Date">
        <template #default="{ row }">
          {{ row.dueDate ? formatDate(row.dueDate) : 'No due date' }}
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { taskApi } from '@/api/task'

const tasks = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    // This is a simplified view; in real app you'd have a dedicated endpoint
    const response = await taskApi.getByProject(1)
    tasks.value = response.data
  } finally {
    loading.value = false
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const statusType = (status) => {
  const map = {
    todo: 'info',
    in_progress: 'warning',
    in_review: 'primary',
    done: 'success',
  }
  return map[status] || 'info'
}

const priorityType = (priority) => {
  const map = {
    low: 'info',
    medium: 'success',
    high: 'warning',
    urgent: 'danger',
  }
  return map[priority] || 'info'
}
</script>

<style scoped>
.tasks h1 {
  margin-bottom: 24px;
  color: #303133;
}
</style>
