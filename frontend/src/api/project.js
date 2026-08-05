import apiClient from './client'

export const projectApi = {
  getAll() {
    return apiClient.get('/projects')
  },
  getById(id) {
    return apiClient.get(`/projects/${id}`)
  },
  create(data) {
    return apiClient.post('/projects', data)
  },
  update(id, data) {
    return apiClient.put(`/projects/${id}`, data)
  },
  delete(id) {
    return apiClient.delete(`/projects/${id}`)
  },
}
