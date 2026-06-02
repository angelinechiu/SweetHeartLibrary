<template>
  <div style="background-color: #F8F4F0;" class="py-5 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-4" style="color: #2C2C2C;">Reset Your Password</h2>

              <div v-if="success" class="alert alert-success text-center">
                <p>Password has been reset successfully!</p>
                <router-link to="/login" class="btn btn-pink mt-2">Login Now</router-link>
              </div>

              <form v-else @submit.prevent="resetPassword">
                <div class="mb-3">
                  <label class="form-label">New Password</label>
                  <input v-model="form.password" type="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-4">
                  <label class="form-label">Confirm New Password</label>
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
    alert('Invalid reset link')
    router.push('/forgot-password')
  }
})

const resetPassword = async () => {
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
      alert(res.data.message || 'Failed to reset password')
    }
  } catch (error) {
    console.error('Reset password error:', error)
    alert('Something went wrong. The link may have expired.')
  } finally {
    loading.value = false
  }
}
</script>
