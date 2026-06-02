import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const role = ref(localStorage.getItem('role') || null)

  // Load user from localStorage on init
  const savedUser = localStorage.getItem('user')
  if (savedUser) {
    try {
      user.value = JSON.parse(savedUser)
    } catch (err) {
      console.error('Failed to parse saved user:', err)
    }
  }

  // Getters (Computed)
  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => role.value === 'admin')
  const currentUser = computed(() => user.value)

  // Actions
  function login(userData, tokenData, userRole) {
    user.value = userData
    token.value = tokenData
    role.value = userRole

    // Save to localStorage
    localStorage.setItem('token', tokenData)
    localStorage.setItem('role', userRole)
    localStorage.setItem('user', JSON.stringify(userData))
  }

  function logout() {
    user.value = null
    token.value = null
    role.value = null

    // Clear localStorage
    localStorage.removeItem('token')
    localStorage.removeItem('role')
    localStorage.removeItem('user')
  }

  function updateUser(updatedData) {
    if (user.value) {
      user.value = { ...user.value, ...updatedData }
      localStorage.setItem('user', JSON.stringify(user.value))
    }
  }

  // Check if user has specific role
  function hasRole(requiredRole) {
    return role.value === requiredRole
  }

  return {
    // State
    user,
    token,
    role,

    // Getters
    isLoggedIn,
    isAdmin,
    currentUser,

    // Actions
    login,
    logout,
    updateUser,
    hasRole
  }
})
