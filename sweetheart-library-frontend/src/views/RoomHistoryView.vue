<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold mb-1" style="color: #2C2C2C;">Study Room Booking History</h2>
          <p class="text-muted mb-0">All your room booking records</p>
        </div>
        <router-link to="/rooms" class="btn btn-pink">
          <i class="bi bi-plus-lg me-1"></i> Book New Room
        </router-link>
      </div>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading" class="row g-3">
        <div
          v-for="booking in roomHistory"
          :key="booking.id"
          class="col-md-6 col-lg-4"
        >
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-info text-white px-3 py-1">Study Room</span>
                <span class="badge px-3 py-1" :class="getStatusClass(booking.status)">
                  {{ booking.status || 'Pending' }}
                </span>
              </div>

              
              <h5 class="fw-semibold mb-1" style="color: #2C2C2C;">
                {{ booking.room_name || booking.purpose || 'Study Room' }}
              </h5>

              
              <p class="text-muted small mb-3">
                {{ formatDate(booking.booking_date || booking.start_time) }}
                • {{ formatTime(booking) }}
              </p>

              
              <div class="d-flex gap-2 mt-auto">
                
                <button
                  v-if="canCancel(booking)"
                  class="btn btn-sm btn-outline-danger flex-fill"
                  @click="cancelRoomBooking(booking)">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>

        
        <div v-if="roomHistory.length === 0" class="col-12">
          <div class="text-center py-5 bg-white rounded-3 shadow-sm">
            <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
            <p class="text-muted mb-0">No room booking history found.</p>
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
import LoadingSpinner from '../components/LoadingSpinner.vue'

const authStore = useAuthStore()
const allBookings = ref([])
const loading = ref(true)

const roomHistory = computed(() => {
  if (!allBookings.value || !Array.isArray(allBookings.value)) return []

  return allBookings.value.filter(b => {
    const purpose = (b.purpose || b.room_name || '').toLowerCase()
    return purpose.includes('room') || purpose.includes('study')
  })
})

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A'
  return new Date(dateStr).toLocaleDateString('en-GB', {
    day: 'numeric', month: 'short', year: 'numeric'
  })
}

const formatTime = (booking) => {
  if (booking.start_time && booking.end_time) {
    return `${booking.start_time} — ${booking.end_time}`
  }
  return booking.booking_time || '—'
}

const getStatusClass = (status) => {
  if (!status) return 'bg-secondary text-white'
  const s = status.toLowerCase()
  if (['confirmed', 'active'].includes(s)) return 'bg-success text-white'
  if (s === 'completed') return 'bg-secondary text-white'
  if (s === 'cancelled') return 'bg-danger text-white'
  if (s === 'pending') return 'bg-warning text-dark'
  return 'bg-secondary text-white'
}

const canCancel = (booking) => {
  const status = (booking.status || '').toLowerCase()
  return !['completed', 'cancelled'].includes(status)
}

const cancelRoomBooking = async (booking) => {
  const roomName = booking.room_name || booking.purpose || 'this room'
  if (!confirm(`Cancel booking for "${roomName}"?`)) return

  try {
    await api.post('/bookings.php?action=cancel_room', { booking_id: booking.id })
    alert('Room booking cancelled successfully!')
    await loadBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to cancel room booking. Please try again.')
  }
}

const loadBookings = async () => {
  if (!authStore.user?.id) return
  try {
    const res = await api.get(`/bookings.php?action=get&user_id=${authStore.user.id}`)
    allBookings.value = res.data?.room_bookings || res.data || []
  } catch (error) {
    console.error(error)
    allBookings.value = []
  }
}

onMounted(async () => {
  loading.value = true
  await loadBookings()
  loading.value = false
})
</script>
