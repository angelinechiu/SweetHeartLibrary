<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-4" style="color: #2C2C2C;">Create Account</h2>

              <form @submit.prevent="handleRegister">
                <div class="mb-3">
                  <input
                    v-model="form.name"
                    type="text"
                    class="form-control form-control-lg"
                    placeholder="Full Name"
                    required
                    minlength="3"
                  >
                  <small v-if="errors.name" class="text-danger">{{ errors.name }}</small>
                </div>

                <div class="mb-3">
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-control form-control-lg"
                    placeholder="Email"
                    required
                  >
                  <small v-if="errors.email" class="text-danger">{{ errors.email }}</small>
                </div>

                <div class="mb-4">
                  <input
                    v-model="form.password"
                    type="password"
                    class="form-control form-control-lg"
                    placeholder="Password"
                    required
                    minlength="6"
                  >
                  <small v-if="errors.password" class="text-danger">{{ errors.password }}</small>
                </div>

                <button
                  type="submit"
                  class="btn btn-pink btn-lg w-100"
                  :disabled="loading"
                >
                  <span v-if="loading">Creating account...</span>
                  <span v-else>Create Account</span>
                </button>
              </form>

              <p class="text-center mt-4">
                Already have an account?
                <router-link to="/login" style="color: #E8B4B8; font-weight: 600;">Login here</router-link>
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
import api from '../services/api.js'

const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: ''
})

const errors = ref({})
const loading = ref(false)

const validateForm = () => {
  errors.value = {}
  let valid = true

  if (form.value.name.length < 3) {
    errors.value.name = 'Name must be at least 3 characters'
    valid = false
  }
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

const handleRegister = async () => {
  if (!validateForm()) return

  loading.value = true

  try {
    const res = await api.post('/auth.php?action=register', form.value)

    if (res.data.success) {
      alert('Registration successful! Please login.')
      router.push('/login')
    } else {
      alert(res.data.message || 'Registration failed')
    }
  } catch (error) {
    console.error(error)
    alert('Registration failed. Please check console for errors.')
  } finally {
    loading.value = false
  }
}
</script>
