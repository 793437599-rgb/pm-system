import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || '')
  const user = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials) {
    const response = await authApi.login(credentials)
    token.value = response.data.token
    localStorage.setItem('token', response.data.token)
    await fetchUser()
    return response.data
  }

  async function fetchUser() {
    const response = await authApi.me()
    user.value = response.data
    return response.data
  }

  function logout() {
    token.value = ''
    user.value = null
    localStorage.removeItem('token')
  }

  return {
    token,
    user,
    isAuthenticated,
    login,
    fetchUser,
    logout,
  }
})
