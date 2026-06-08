<template>
  <div style="background-color: #F8F4F0;" class="py-5 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h2 class="text-center fw-bold mb-2" style="color: #2C2C2C;">Forgot Password?</h2>
              <p class="text-center text-muted mb-4">Enter your email to receive a password reset link.</p>

              
              <div v-if="message" :class="['alert', isSuccess ? 'alert-success' : 'alert-danger']">
                {{ message }}
              </div>

              
              <div v-if="resetLink" class="alert alert-info text-center">
                <p class="mb-2"><strong>Development Mode</strong></p>
                <a :href="resetLink" target="_blank" class="btn btn-success">
                  Click here to Reset Password
                </a>
                <p class="small mt-2 mb-0">Link expires in 1 hour</p>
              </div>

              
              <form v-if="!message || !isSuccess" @submit.prevent="handleForgotPassword">
                <div class="mb-4">
                  <input
                    v-model="email"
                    type="email"
                    class="form-control form-control-lg"
                    placeholder="Enter your registered email"
                    required
                  >
                </div>

                <button
                  type="submit"
                  class="btn btn-pink btn-lg w-100"
                  :disabled="loading"
                >
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
const message = ref('')
const isSuccess = ref(false)
const resetLink = ref('')

const handleForgotPassword = async () => {
  message.value = ''
  resetLink.value = ''
  loading.value = true

  try {
    const res = await api.post('/auth.php?action=forgot_password', {
      email: email.value
    })

    console.log('Backend response:', res.data) 

    if (res.data.success) {
      isSuccess.value = true
      message.value = res.data.message || 'Request processed successfully.'

      if (res.data.reset_link) {
        resetLink.value = res.data.reset_link
      }
    } else {
      isSuccess.value = false
      message.value = res.data.message || 'Something went wrong.'
    }
  } catch (error) {
    console.error('Full error:', error)

    isSuccess.value = false

    if (error.response) {
      
      message.value = error.response.data?.message || 'Server error occurred.'
    } else if (error.request) {
      message.value = 'Cannot connect to server. Is the backend running?'
    } else {
      message.value = 'Failed to send request. Check console for details.'
    }
  } finally {
    loading.value = false
  }
}
</script>
