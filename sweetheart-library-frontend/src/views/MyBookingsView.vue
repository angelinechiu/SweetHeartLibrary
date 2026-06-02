<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4" style="color: #2C2C2C;">My Bookings</h2>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading">

        <!-- ==================== PENDING FOR CONFIRMATION ==================== -->
        <h5 class="mb-3 text-warning">Pending for Confirmation</h5>

        <div v-if="pendingBookings.length > 0" class="alert alert-warning mb-3">
          <strong>Important:</strong> Please confirm your booking within <strong>2 hours</strong> after creation.
          Otherwise, it will be automatically cancelled.
        </div>

        <div class="table-responsive mb-4">
          <table class="table table-hover">
            <thead style="background-color: #2C2C2C; color: #F8F4F0;">
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Purpose</th>
                <th>Time Left</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="b in pendingBookings" :key="b.id">
                <td>{{ b.booking_date }}</td>
                <td>{{ b.start_time }} — {{ b.end_time }}</td>
                <td>{{ b.purpose }}</td>
                <td>
                  <span v-if="getTimeLeft(b) > 0" class="text-danger fw-bold">
                    {{ getTimeLeft(b) }} hour(s) left
                  </span>
                  <span v-else class="text-danger">Expiring soon...</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-success me-2" @click="confirmBooking(b.id)">Confirm Now</button>
                  <button class="btn btn-sm btn-outline-danger" @click="cancelBooking(b.id)">Cancel</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ==================== CONFIRMED BOOKINGS ==================== -->
        <h5 class="mb-3 text-success">Confirmed Bookings</h5>
        <div class="table-responsive mb-4">
          <table class="table table-hover">
            <thead style="background-color: #2C2C2C; color: #F8F4F0;">
              <tr><th>Date</th><th>Time</th><th>Purpose</th><th>Action</th></tr>
            </thead>
            <tbody>
              <tr v-for="b in confirmedBookings" :key="b.id">
                <td>{{ b.booking_date }}</td>
                <td>{{ b.start_time }} — {{ b.end_time }}</td>
                <td>{{ b.purpose }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-danger" @click="cancelBooking(b.id)">Cancel</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ==================== CANCELLED BOOKINGS ==================== -->
        <h5 class="mb-3 text-danger">Cancelled Bookings</h5>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead style="background-color: #2C2C2C; color: #F8F4F0;">
              <tr><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr v-for="b in cancelledBookings" :key="b.id">
                <td>{{ b.booking_date }}</td>
                <td>{{ b.start_time }} — {{ b.end_time }}</td>
                <td>{{ b.purpose }}</td>
                <td><span class="badge bg-danger">Cancelled</span></td>
              </tr>
            </tbody>
          </table>
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

const pendingBookings = computed(() => allBookings.value.filter(b => b.status === 'pending'))
const confirmedBookings = computed(() => allBookings.value.filter(b => b.status === 'confirmed'))
const cancelledBookings = computed(() => allBookings.value.filter(b => b.status === 'cancelled'))

// Calculate remaining hours before auto-cancel
const getTimeLeft = (booking) => {
  if (!booking.created_at) return 0
  const createdAt = new Date(booking.created_at)
  const now = new Date()
  const hoursPassed = (now - createdAt) / (1000 * 60 * 60)
  const remaining = Math.max(0, 2 - hoursPassed)
  return Math.floor(remaining)
}

const loadBookings = async () => {
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?user_id=${authStore.user.id}`)
    allBookings.value = res.data
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const confirmBooking = async (id) => {
  await api.post('/bookings.php', { id, status: 'confirmed' })
  loadBookings()
}

const cancelBooking = async (id) => {
  if (confirm('Cancel this booking?')) {
    await api.post('/bookings.php', { id, status: 'cancelled' })
    loadBookings()
  }
}

onMounted(loadBookings)
</script>
