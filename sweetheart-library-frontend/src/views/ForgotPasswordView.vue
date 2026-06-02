<template>
  <div style="background-color: #F8F4F0;" class="py-5 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-2" style="color: #2C2C2C;">Forgot Password?</h2>
              <p class="text-center text-muted mb-4">Enter your email to receive a password reset link.</p>

              <!-- Success Message with Reset Link -->
              <div v-if="resetLink" class="alert alert-success">
                <p><strong>Password reset link generated!</strong></p>
                <p class="small">For testing, copy and open this link:</p>
                <a :href="resetLink" target="_blank" class="btn btn-sm btn-outline-success w-100">
                  Click here to Reset Password
                </a>
                <p class="mt-2 small text-muted">This link will expire in 1 hour.</p>
              </div>

              <form v-else @submit.prevent="handleForgotPassword">
                <div class="mb-4">
                  <input
                    v-model="email"
                    type="email"
                    class="form-control form-control-lg"
                    placeholder="Enter your registered email"
                    required
                  >
                </div>

                <button type="submit" class="btn btn-pink btn-lg w-100" :disabled="loading">
                  {{ loading ? 'Processing...' : 'Send Reset Link' }}
                </button>
              </form>

              <p class="text-center mt-4">
                Remember your password?
                <router-link to="/login" style="color: #E8B4B8; font-weight: 600;">Login</router-link>
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
import api from '../services/api.js'

const email = ref('')
const loading = ref(false)
const resetLink = ref('')

const handleForgotPassword = async () => {
  loading.value = true
  try {
    const res = await api.post('/auth.php?action=forgot_password', { email: email.value })

    if (res.data.success && res.data.reset_link) {
      // Show the reset link (for localhost testing)
      resetLink.value = res.data.reset_link
    } else {
      alert(res.data.message || 'Something went wrong')
    }
  } catch (error) {
    console.error('Forgot password error:', error)
    alert('Failed to process request. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
