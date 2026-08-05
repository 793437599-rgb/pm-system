<template>
  <div v-if="projectStore.currentProject" class="project-detail">
    <el-page-header @back="$router.push('/projects')" :title="projectStore.currentProject.name" />

    <el-descriptions :column="2" border class="project-info">
      <el-descriptions-item label="Description">
        {{ projectStore.currentProject.description || 'No description' }}
      </el-descriptions-item>
      <el-descriptions-item label="Status">
        <el-tag :type="statusType(projectStore.currentProject.status)">
          {{ projectStore.currentProject.status }}
        </el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="Owner">
        {{ projectStore.currentProject.owner.fullName }}
      </el-descriptions-item>
      <el-descriptions-item label="Created">
        {{ formatDate(projectStore.currentProject.createdAt) }}
      </el-descriptions-item>
    </el-descriptions>

    <h3 style="margin-top: 24px">Tasks</h3>

    <el-button type="primary" @click="taskDialogVisible = true" style="margin-bottom: 16px">
      Add Task
    </el-button>

    <el-table :data="tasks" v-loading="taskLoading" stripe>
      <el-table-column prop="title" label="Title" />
      <el-table-column prop="status" label="Status">
        <template #default="{ row }">
          <el-tag :type="taskStatusType(row.status)">{{ row.status }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="priority" label="Priority">
        <template #default="{ row }">
          <el-tag :type="priorityType(row.priority)" effect="dark">{{ row.priority }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="assignee.fullName" label="Assignee" />
      <el-table-column label="Actions" width="120">
        <template #default="{ row }">
          <el-button size="small" type="danger" @click="deleteTask(row)">Delete</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- Add Task Dialog -->
    <el-dialog v-model="taskDialogVisible" title="Add Task" width="500px">
      <el-form :model="taskForm" label-position="top">
        <el-form-item label="Title">
          <el-input v-model="taskForm.title" placeholder="Task title" />
        </el-form-item>
        <el-form-item label="Description">
          <el-input
            v-model="taskForm.description"
            type="textarea"
            rows="3"
            placeholder="Task description"
          />
        </el-form-item>
        <el-form-item label="Priority">
          <el-select v-model="taskForm.priority" placeholder="Select priority">
            <el-option label="Low" value="low" />
            <el-option label="Medium" value="medium" />
            <el-option label="High" value="high" />
            <el-option label="Urgent" value="urgent" />
          </el-select>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="taskDialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="createTask">Create</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useProjectStore } from '@/stores/project'
import { taskApi } from '@/api/task'

const route = useRoute()
const projectStore = useProjectStore()

const tasks = ref([])
const taskLoading = ref(false)
const taskDialogVisible = ref(false)
const taskForm = reactive({
  title: '',
  description: '',
  priority: 'medium',
})

onMounted(async () => {
  const projectId = route.params.id
  await projectStore.fetchProject(projectId)
  await loadTasks(projectId)
})

const loadTasks = async (projectId) => {
  taskLoading.value = true
  try {
    const response = await taskApi.getByProject(projectId)
    tasks.value = response.data
  } finally {
    taskLoading.value = false
  }
}

const createTask = async () => {
  if (!taskForm.title) {
    ElMessage.warning('Please enter task title')
    return
  }

  try {
    await taskApi.create({
      ...taskForm,
      projectId: route.params.id,
    })
    taskDialogVisible.value = false
    taskForm.title = ''
    taskForm.description = ''
    taskForm.priority = 'medium'
    await loadTasks(route.params.id)
    ElMessage.success('Task created')
  } catch {
    ElMessage.error('Failed to create task')
  }
}

const deleteTask = async (row) => {
  try {
    await ElMessageBox.confirm(`Delete task "${row.title}"?`, 'Confirm', {
      type: 'warning',
    })
    await taskApi.delete(row.id)
    await loadTasks(route.params.id)
    ElMessage.success('Task deleted')
  } catch {
    // cancelled
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const statusType = (status) => {
  const map = {
    planning: 'info',
    active: 'success',
    on_hold: 'warning',
    completed: 'primary',
  }
  return map[status] || 'info'
}

const taskStatusType = (status) => {
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
.project-detail h1 {
  margin-bottom: 20px;
}

.project-info {
  margin-top: 24px;
}
</style>
