import apiClient from './client'

export const authApi = {
  login(credentials) {
    return apiClient.post('/login', credentials)
  },
  me() {
    return apiClient.get('/me')
  },
  register(data) {
    return apiClient.post('/register', data)
  },
}
