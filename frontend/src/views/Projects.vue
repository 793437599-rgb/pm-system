<template>
  <div class="projects">
    <div class="page-header">
      <h1>Projects</h1>
      <el-button type="primary" @click="openDialog()">New Project</el-button>
    </div>

    <el-table :data="projectStore.projects" v-loading="projectStore.loading" stripe>
      <el-table-column prop="name" label="Name" />
      <el-table-column prop="description" label="Description" show-overflow-tooltip />
      <el-table-column prop="status" label="Status">
        <template #default="{ row }">
          <el-tag :type="statusType(row.status)">{{ row.status }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="owner.fullName" label="Owner" />
      <el-table-column label="Actions" width="180">
        <template #default="{ row }">
          <el-button size="small" @click="$router.push(`/projects/${row.id}`)">View</el-button>
          <el-button size="small" type="danger" @click="handleDelete(row)">Delete</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog v-model="dialogVisible" title="New Project" width="500px">
      <el-form :model="form" label-position="top">
        <el-form-item label="Name">
          <el-input v-model="form.name" placeholder="Project name" />
        </el-form-item>
        <el-form-item label="Description">
          <el-input
            v-model="form.description"
            type="textarea"
            rows="3"
            placeholder="Project description"
          />
        </el-form-item>
        <el-form-item label="Status">
          <el-select v-model="form.status" placeholder="Select status">
            <el-option label="Planning" value="planning" />
            <el-option label="Active" value="active" />
            <el-option label="On Hold" value="on_hold" />
            <el-option label="Completed" value="completed" />
          </el-select>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="dialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="handleCreate">Create</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useProjectStore } from '@/stores/project'

const projectStore = useProjectStore()

const dialogVisible = ref(false)
const form = reactive({
  name: '',
  description: '',
  status: 'planning',
})

onMounted(() => {
  projectStore.fetchProjects()
})

const openDialog = () => {
  form.name = ''
  form.description = ''
  form.status = 'planning'
  dialogVisible.value = true
}

const handleCreate = async () => {
  if (!form.name) {
    ElMessage.warning('Please enter project name')
    return
  }

  try {
    await projectStore.createProject({ ...form })
    dialogVisible.value = false
    ElMessage.success('Project created successfully')
  } catch (error) {
    ElMessage.error('Failed to create project')
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete "${row.name}"?`,
      'Confirm Delete',
      { type: 'warning' }
    )
    await projectStore.deleteProject(row.id)
    ElMessage.success('Project deleted')
  } catch {
    // cancelled
  }
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
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-header h1 {
  margin: 0;
  color: #303133;
}
</style>
