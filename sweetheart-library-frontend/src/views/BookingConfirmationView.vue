<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container text-center">

      <!-- Success Icon -->
      <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
      <h2 class="fw-bold mt-4" style="color: #2C2C2C;">Booking Confirmation</h2>
      <p class="text-muted">Please review your booking details before confirming.</p>

      <!-- Booking Summary Card -->
      <div class="card border-0 shadow-sm mx-auto mt-4" style="max-width: 520px;">
        <div class="card-body text-start p-4">
          <h6 class="fw-bold mb-3">Booking Summary</h6>

          <div class="row mb-2">
            <div class="col-5 text-muted">Room</div>
            <div class="col-7 fw-semibold">{{ roomName }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Date</div>
            <div class="col-7 fw-semibold">{{ date }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Time</div>
            <div class="col-7 fw-semibold">{{ startTime }} — {{ endTime }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Duration</div>
            <div class="col-7 fw-semibold">2 hours</div>
          </div>
          <div class="row">
            <div class="col-5 text-muted">Status</div>
            <div class="col-7">
              <span class="badge bg-warning text-dark px-3 py-1">Pending Confirmation</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirm Button -->
      <div class="mt-4">
        <button
          class="btn btn-pink px-5 py-2"
          @click="confirmBooking"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting">Confirming...</span>
          <span v-else>Confirm Booking</span>
        </button>
      </div>

      <p class="text-muted small mt-3">You must confirm this booking to complete the process.</p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const isSubmitting = ref(false)

const roomName = route.query.roomName || 'Study Room'
const date = route.query.date || ''
const startTime = route.query.startTime || ''
const endTime = route.query.endTime || ''

const confirmBooking = async () => {
  isSubmitting.value = true

  try {
    await api.post('/bookings.php', {
      action: 'create_room_booking',
      user_id: authStore.user.id,
      room_id: route.query.roomId,
      booking_date: date,
      start_time: startTime,
      end_time: endTime
    })

    alert('Booking confirmed successfully!')
    router.push('/my-bookings')   // Redirect after confirmation

  } catch (error) {
    console.error(error)
    alert('Failed to confirm booking. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}
</script>
