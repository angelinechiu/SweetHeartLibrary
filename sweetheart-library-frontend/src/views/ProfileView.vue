<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-7">
          <div class="card shadow-lg border-0">
            <div class="card-header text-center py-4" style="background-color: #2C2C2C; color: #F8F4F0;">
              <h2>My Profile</h2>
            </div>
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <LoadingSpinner :loading="loading" />

              <div v-if="!loading">
                <div class="text-center mb-4">
                  <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                       style="width: 120px; height: 120px; background-color: #D9CFC2; font-size: 3rem;">
                    ❤️
                  </div>
                  <h3>{{ user.name }}</h3>
                  <p class="text-muted">{{ user.email }}</p>
                </div>

                <form @submit.prevent="updateProfile">
                  <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input v-model="form.name" type="text" class="form-control form-control-lg" required>
                  </div>
                  <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input v-model="form.email" type="email" class="form-control form-control-lg" required>
                  </div>
                  <button type="submit" class="btn btn-pink btn-lg w-100" :disabled="loading">
                    Update Profile
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const authStore = useAuthStore()
const user = ref({})
const form = ref({ name: '', email: '' })
const loading = ref(true)

const loadProfile = () => {
  if (authStore.user) {
    user.value = { ...authStore.user }
    form.value.name = user.value.name
    form.value.email = user.value.email
  }
  loading.value = false
}

const updateProfile = async () => {
  loading.value = true
  try {
    authStore.user.name = form.value.name
    authStore.user.email = form.value.email
    localStorage.setItem('user', JSON.stringify(authStore.user))
    user.value = { ...authStore.user }
    alert('Profile updated successfully!')
  } catch (error) {
    console.error('Profile update failed:', error)
    alert('Failed to update profile')
  } finally {
    loading.value = false
  }
}

onMounted(loadProfile)
</script>
