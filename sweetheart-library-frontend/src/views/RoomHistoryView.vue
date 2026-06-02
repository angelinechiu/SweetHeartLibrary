<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4" style="color: #2C2C2C;">Study Room Booking History</h2>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading" class="table-responsive">
        <table class="table table-striped">
          <thead style="background-color: #2C2C2C; color: #F8F4F0;">
            <tr>
              <th>Date</th>
              <th>Time</th>
              <th>Room / Purpose</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in roomHistory" :key="booking.id">
              <td>{{ booking.booking_date }}</td>
              <td>{{ booking.start_time }} — {{ booking.end_time }}</td>
              <td>{{ booking.purpose }}</td>
              <td>
                <span class="badge" :class="getStatusClass(booking.status)">
                  {{ booking.status }}
                </span>
              </td>
            </tr>
            <tr v-if="roomHistory.length === 0">
              <td colspan="4" class="text-center text-muted">No study room booking history found.</td>
            </tr>
          </tbody>
        </table>
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
  return allBookings.value.filter(b =>
    b.purpose && b.purpose.toLowerCase().includes('room') &&
    (new Date(b.booking_date) < new Date() || b.status === 'completed' || b.status === 'cancelled')
  )
})

const getStatusClass = (status) => {
  if (status === 'confirmed' || status === 'completed') return 'bg-success'
  if (status === 'cancelled') return 'bg-danger'
  return 'bg-secondary'
}

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?user_id=${authStore.user.id}`)
    allBookings.value = res.data
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
})
</script>
