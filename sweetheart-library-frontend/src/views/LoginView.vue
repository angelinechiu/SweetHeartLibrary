<template>
  <div style="background-color: #F8F4F0;" class="py-5 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-4" style="color: #2C2C2C;">Welcome Back</h2>

              <form @submit.prevent="handleLogin">
                <!-- Email & Password fields (same as before) -->
                <div class="mb-3">
                  <input v-model="form.email" type="email" class="form-control form-control-lg" placeholder="Email" required>
                </div>
                <div class="mb-4">
                  <input v-model="form.password" type="password" class="form-control form-control-lg" placeholder="Password" required>
                </div>
                <!-- Add this inside the form in RegisterView.vue -->
                <div class="mb-3 text-end">
                  <router-link to="/forgot-password" style="color: #E8B4B8; font-size: 0.9rem; text-decoration: none;">
                    Forgot your password?
                  </router-link>
                </div>

                <button type="submit" class="btn btn-pink btn-lg w-100" :disabled="loading">
                  Login
                </button>
              </form>

              <!-- Link to Register -->
              <p class="text-center mt-4">
                Don't have an account?
                <router-link to="/register" style="color: #E8B4B8; font-weight: 600;">Create one here</router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({ email: '', password: '' })
const errors = ref({})
const loading = ref(false)

const validateForm = () => {
  errors.value = {}
  let valid = true

  if (!form.value.email.includes('@')) {
    errors.value.email = 'Please enter a valid email'
    valid = false
  }
  if (form.value.password.length < 6) {
    errors.value.password = 'Password must be at least 6 characters'
    valid = false
  }
  return valid
}

const handleLogin = async () => {
  if (!validateForm()) return

  loading.value = true
  try {
    const res = await api.post('/auth.php?action=login', form.value)

    if (res.data.success) {
      authStore.login(res.data.user, res.data.token, res.data.user.role)
      router.push('/dashboard')
    } else {
      alert(res.data.message || 'Login failed')
    }
  } catch (error) {
    console.error('Login error:', error)
    alert('Login failed. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
