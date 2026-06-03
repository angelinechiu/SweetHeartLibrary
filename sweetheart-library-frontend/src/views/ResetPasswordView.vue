<template>
  <div style="background-color: #F8F4F0;" class="py-5 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-4" style="color: #2C2C2C;">Reset Your Password</h2>

              <div v-if="success" class="alert alert-success text-center">
                <h5 class="mb-3">Password Reset Successful!</h5>
                <p>You can now login with your new password.</p>
                <router-link to="/login" class="btn btn-pink mt-2 px-4">
                  Go to Login
                </router-link>
              </div>

              <form v-else @submit.prevent="resetPassword">
                <div class="mb-3">
                  <label class="form-label fw-medium">New Password</label>
                  <input v-model="form.password" type="password" class="form-control" required minlength="6">
                  <small class="text-muted">Minimum 6 characters</small>
                </div>
                <div class="mb-4">
                  <label class="form-label fw-medium">Confirm New Password</label>
                  <input v-model="form.confirm_password" type="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-pink btn-lg w-100" :disabled="loading">
                  {{ loading ? 'Resetting Password...' : 'Reset Password' }}
                </button>
              </form>

              <p class="text-center mt-4">
                <router-link to="/login" style="color: #E8B4B8;">Back to Login</router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'

const route = useRoute()
const router = useRouter()

const form = ref({
  password: '',
  confirm_password: ''
})
const loading = ref(false)
const success = ref(false)
const token = ref('')

onMounted(() => {
  token.value = route.query.token
  if (!token.value) {
    alert('Invalid or missing reset token')
    router.push('/forgot-password')
  }
})

const resetPassword = async () => {
  if (form.value.password.length < 6) {
    alert('Password must be at least 6 characters')
    return
  }
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
      // Auto redirect to login after 3 seconds
      setTimeout(() => {
        router.push('/login')
      }, 3000)
    } else {
      alert(res.data.message || 'Failed to reset password. The link may have expired.')
    }
  } catch (error) {
    console.error('Reset password error:', error)
    alert('Something went wrong. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
