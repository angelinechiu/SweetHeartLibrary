<template>
  <div style="background-color: #F8F4F0; min-height: 70vh;" class="py-4">
    <div class="container">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold mb-1" style="color: #2C2C2C;">Announcements</h2>
          <p class="text-muted mb-0">Global notices and your personal reminders</p>
        </div>

        <router-link
          v-if="isAdmin"
          to="/admin"
          class="btn btn-pink"
        >
          <i class="bi bi-gear-fill me-2"></i> Manage
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-secondary"></div>
        <p class="mt-3 text-muted">Loading announcements...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="announcements.length === 0" class="text-center py-5">
        <i class="bi bi-megaphone fs-1 text-muted"></i>
        <h5 class="mt-3 text-muted">No announcements right now</h5>
      </div>

      <!-- Announcements -->
      <div v-else class="row g-4">
        <div
          v-for="announcement in announcements"
          :key="announcement.id"
          class="col-12 col-md-6 col-lg-4"
        >
          <div class="card h-100 border-0 shadow-sm hover-card">
            <div class="card-body d-flex flex-column">
              <!-- Personal Reminder Badge -->
              <div v-if="announcement.user_id" class="mb-2">
                <span class="badge bg-info text-white">
                  <i class="bi bi-person-fill me-1"></i> Personal Reminder
                </span>
              </div>

              <div class="mb-3">
                <span class="badge px-3 py-2" :class="getBadgeClass(announcement.type)">
                  {{ announcement.type || 'Notice' }}
                </span>
              </div>

              <h5 class="card-title fw-bold">{{ announcement.title }}</h5>
              <p class="card-text flex-grow-1 text-muted">{{ announcement.message }}</p>

              <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">
                  {{ formatDate(announcement.created_at) }}
                </small>
                <span v-if="announcement.due_date" class="badge bg-danger-subtle text-danger small">
                  Due {{ formatDate(announcement.due_date) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'

const authStore = useAuthStore()
const loading = ref(true)
const announcements = ref([])

const isAdmin = computed(() => authStore.user?.role === 'admin')

const loadAnnouncements = async () => {
  loading.value = true
  try {
    const userId = authStore.user?.id

    // Use new endpoint that supports personal reminders
    let url = '/announcements.php?action=get_visible'
    if (userId) {
      url += `&user_id=${userId}`
    }

    const res = await api.get(url)
    announcements.value = res.data || []
  } catch (error) {
    console.error('Failed to load announcements:', error)
    announcements.value = []
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short', day: 'numeric', year: 'numeric'
  })
}

const getBadgeClass = (type) => {
  const map = {
    'Important': 'bg-danger text-white',
    'Event': 'bg-primary text-white',
    'Notice': 'bg-warning text-dark',
    'Update': 'bg-success text-white'
  }
  return map[type] || 'bg-secondary text-white'
}

onMounted(loadAnnouncements)
</script>

<style scoped>
.hover-card {
  transition: all 0.3s ease;
  background-color: white;
}
.hover-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
</style>
