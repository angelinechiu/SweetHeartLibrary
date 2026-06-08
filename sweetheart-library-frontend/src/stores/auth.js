import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const role = ref(localStorage.getItem('role') || null)

  
  const savedUser = localStorage.getItem('user')
  if (savedUser) {
    try {
      user.value = JSON.parse(savedUser)
    } catch (err) {
      console.error('Failed to parse saved user:', err)
    }
  }

  
  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => role.value === 'admin')
  const isUser = computed(() => role.value === 'user')
  const currentUser = computed(() => user.value)

  
  function login(userData, tokenData, userRole) {
    user.value = userData
    token.value = tokenData
    role.value = userRole

    
    localStorage.setItem('token', tokenData)
    localStorage.setItem('role', userRole)
    localStorage.setItem('user', JSON.stringify(userData))
  }

  function logout() {
    user.value = null
    token.value = null
    role.value = null

    
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

  
  function hasRole(requiredRole) {
    return role.value === requiredRole
  }

  return {
    
    user,
    token,
    role,

    
    isLoggedIn,
    isAdmin,
    isUser,
    currentUser,

    
    login,
    logout,
    updateUser,
    hasRole
  }
})
