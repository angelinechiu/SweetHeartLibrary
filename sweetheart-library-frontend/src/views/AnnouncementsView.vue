<template>
  <div class="announcements-page py-5" style="background-color: #F8F4F0; min-height: 80vh;">
    <div class="container">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
          <h1 class="fw-bold mb-1" style="color: #2C2C2C;">Announcements</h1>
          <p class="text-muted mb-0">Stay updated with the latest library news and reminders</p>
        </div>
        <router-link v-if="isAdmin" to="/admin" class="btn btn-pink">Manage Announcements</router-link>
      </div>

      <!-- Loading -->
      <LoadingSpinner :loading="loading" message="Loading announcements..." />

      <!-- Announcements List -->
      <div v-if="!loading && announcements.length > 0" class="row g-4">
        <div class="col-md-6" v-for="announcement in announcements" :key="announcement.id">
          <div class="card border-0 shadow-sm h-100" style="background-color: #D9CFC2; border-radius: 16px;">
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-3">
                <span 
                  class="badge px-3 py-1 me-2" 
                  :class="announcement.type === 'Overdue Reminder' ? 'bg-danger text-white' : 'bg-pink text-dark'"
                >
                  {{ announcement.type }}
                </span>
                <small class="text-muted">{{ announcement.published_at }}</small>
              </div>
              
              <h4 class="fw-semibold mb-3" style="color: #2C2C2C;">{{ announcement.title }}</h4>
              
              <p class="mb-3" style="color: #444;">{{ announcement.message }}</p>
              
              <div v-if="announcement.due_date" class="alert alert-warning py-2 px-3 small mb-0">
                <strong>Action Required:</strong> Please return books by {{ announcement.due_date }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && announcements.length === 0" class="text-center py-5">
        <div class="mb-4" style="font-size: 4rem;">📢</div>
        <h4 class="fw-semibold mb-2" style="color: #2C2C2C;">No Announcements Yet</h4>
        <p class="text-muted">Check back soon for library updates and reminders.</p>
      </div>

      <!-- Note for Users -->
      <div class="mt-5 text-center">
        <p class="text-muted small">
          Announcements are published by library administrators. 
          Overdue reminders will appear here automatically.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const authStore = useAuthStore()
const loading = ref(true)
const announcements = ref([])

const isAdmin = computed(() => authStore.user?.role === 'admin')

const loadAnnouncements = async () => {
  loading.value = true
  try {
    const res = await api.get('/announcements.php?action=get_published')
    announcements.value = res.data
  } catch (error) {
    console.error('Failed to load announcements:', error)
    announcements.value = []
  } finally {
    loading.value = false
  }
}

onMounted(loadAnnouncements)
</script>

<style scoped>
.bg-pink {
  background-color: #E8B4B8 !important;
  color: #2C2C2C !important;
}
</style>