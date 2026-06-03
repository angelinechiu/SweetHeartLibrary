<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h1 class="fw-bold mb-4" style="color: #2C2C2C;">Welcome back, {{ authStore.user?.name }} 💕</h1>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading" class="row g-4">

        <!-- Active Bookings -->
        <div class="col-md-3" @click="goToMyBookings">
          <div class="card border-0 shadow-sm text-center h-100 hover-card" style="background-color: #D9CFC2; cursor: pointer;">
            <div class="card-body py-4">
              <h2 style="color: #E8B4B8;">{{ stats.activeBookings }}</h2>
              <p class="mb-0" style="color: #2C2C2C;">Active Bookings</p>
            </div>
          </div>
        </div>

        <!-- Books Borrowed -->
        <div class="col-md-3" @click="goToHistory">
          <div class="card border-0 shadow-sm text-center h-100 hover-card" style="background-color: #D9CFC2; cursor: pointer;">
            <div class="card-body py-4">
              <h2 style="color: #E8B4B8;">{{ stats.booksBorrowed }}</h2>
              <p class="mb-0" style="color: #2C2C2C;">Books Borrowed</p>
            </div>
          </div>
        </div>

        <!-- Study Rooms Booked -->
        <div class="col-md-3" @click="goToRoomHistory">
          <div class="card border-0 shadow-sm text-center h-100 hover-card" style="background-color: #D9CFC2; cursor: pointer;">
            <div class="card-body py-4">
              <h2 style="color: #E8B4B8;">{{ stats.studyRooms }}</h2>
              <p class="mb-0" style="color: #2C2C2C;">Study Rooms Booked</p>
            </div>
          </div>
        </div>

        <!-- Events Attended -->
        <div class="col-md-3" @click="goToEvents">
          <div class="card border-0 shadow-sm text-center h-100 hover-card" style="background-color: #D9CFC2; cursor: pointer;">
            <div class="card-body py-4">
              <h2 style="color: #E8B4B8;">{{ stats.eventsAttended }}</h2>
              <p class="mb-0" style="color: #2C2C2C;">Events Attended</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(true)

const stats = ref({
  activeBookings: 0,
  booksBorrowed: 0,
  studyRooms: 0,
  eventsAttended: 5   // You can make this dynamic later
})

const loadDashboardStats = async () => {
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?user_id=${authStore.user.id}`)
    const userBookings = res.data

    stats.value.activeBookings = userBookings.filter(b => b.status === 'confirmed').length
    stats.value.studyRooms = userBookings.filter(b => b.purpose?.toLowerCase().includes('room')).length
    stats.value.booksBorrowed = userBookings.length
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const goToMyBookings = () => router.push('/my-bookings')
const goToHistory = () => router.push('/history')
const goToRoomHistory = () => router.push('/room-history')
const goToEvents = () => router.push('/events')

onMounted(() => {
  if (authStore.user) loadDashboardStats()
})
</script>

<style scoped>
.page-wrapper {
  padding: 10px 0;
}

.page-header h1 {
  font-size: 2.1rem;
  letter-spacing: 0.3px;
}

@media (max-width: 576px) {
  .page-header h1 {
    font-size: 1.7rem;
  }
}
</style>
<style scoped>
.page-wrapper {
  padding: 10px 0;
}

.page-header h1 {
  font-size: 2.1rem;
  letter-spacing: 0.3px;
}

@media (max-width: 576px) {
  .page-header h1 {
    font-size: 1.7rem;
  }
}
</style>
