import { defineStore } from 'pinia'
import { ref } from 'vue'
import { projectApi } from '@/api/project'

export const useProjectStore = defineStore('project', () => {
  const projects = ref([])
  const currentProject = ref(null)
  const loading = ref(false)

  async function fetchProjects() {
    loading.value = true
    try {
      const response = await projectApi.getAll()
      projects.value = response.data
    } finally {
      loading.value = false
    }
  }

  async function fetchProject(id) {
    loading.value = true
    try {
      const response = await projectApi.getById(id)
      currentProject.value = response.data
    } finally {
      loading.value = false
    }
  }

  async function createProject(data) {
    const response = await projectApi.create(data)
    projects.value.unshift(response.data)
    return response.data
  }

  async function updateProject(id, data) {
    const response = await projectApi.update(id, data)
    const index = projects.value.findIndex((p) => p.id === id)
    if (index !== -1) {
      projects.value[index] = response.data
    }
    return response.data
  }

  async function deleteProject(id) {
    await projectApi.delete(id)
    projects.value = projects.value.filter((p) => p.id !== id)
  }

  return {
    projects,
    currentProject,
    loading,
    fetchProjects,
    fetchProject,
    createProject,
    updateProject,
    deleteProject,
  }
})
