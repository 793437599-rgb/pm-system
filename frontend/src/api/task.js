import apiClient from './client'

export const taskApi = {
  getByProject(projectId) {
    return apiClient.get(`/projects/${projectId}/tasks`)
  },
  create(data) {
    return apiClient.post('/tasks', data)
  },
  update(id, data) {
    return apiClient.put(`/tasks/${id}`, data)
  },
  delete(id) {
    return apiClient.delete(`/tasks/${id}`)
  },
}
