<template>
  <div style="background-color: #F8F4F0;" class="py-5 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-4" style="color: #2C2C2C;">Reset Your Password</h2>

              <div v-if="success" class="alert alert-success text-center">
                <h5>Password Reset Successful!</h5>
                <router-link to="/login" class="btn btn-pink mt-3">Go to Login</router-link>
              </div>

              <form v-else @submit.prevent="resetPassword">
                <div class="mb-3">
                  <label class="form-label fw-medium">New Password</label>
                  <input v-model="form.password" type="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-4">
                  <label class="form-label fw-medium">Confirm New Password</label>
                  <input v-model="form.confirm_password" type="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-pink btn-lg w-100" :disabled="loading">
                  {{ loading ? 'Resetting...' : 'Reset Password' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'

const route = useRoute()
const router = useRouter()

const form = ref({ password: '', confirm_password: '' })
const loading = ref(false)
const success = ref(false)
const token = ref('')

onMounted(async () => {
  await nextTick() // Wait for route to be fully ready

  token.value = route.query.token || ''
  console.log("Token captured from URL:", token.value)

  if (!token.value) {
    alert('Invalid reset link - No token found')
    router.push('/forgot-password')
  }
})

const resetPassword = async () => {
  console.log("Sending token to backend:", token.value)

  if (form.value.password !== form.value.confirm_password) {
    alert('Passwords do not match!')
    return
  }

  loading.value = true
  try {
    const res = await api.post('/auth.php?action=reset_password', {
      token: token.value,
      password: form.value.password
    })

    if (res.data.success) {
      success.value = true
    } else {
      alert(res.data.message || 'Invalid or expired token')
    }
  } catch (error) {
    console.error('Failed to reset password', error)
    alert('Something went wrong. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
