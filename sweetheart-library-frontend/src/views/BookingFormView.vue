<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow-lg border-0">
            <div class="card-body p-5" style="background-color: #F8F4F0;">
              <h3 class="text-center mb-4" style="color: #2C2C2C;">Complete Your Booking</h3>

              <form @submit.prevent="submitBooking">
                <div class="mb-3">
                  <label class="form-label">Date</label>
                  <input v-model="form.date" type="date" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Time</label>
                  <input v-model="form.time" type="time" class="form-control form-control-lg" required>
                </div>
                <div class="mb-4">
                  <label class="form-label">Purpose</label>
                  <textarea v-model="form.purpose" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-pink btn-lg w-100" :disabled="loading">
                  <LoadingSpinner v-if="loading" :loading="true" message="Saving booking..." />
                  <span v-else>Confirm Booking</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = ref({ date: '', time: '', purpose: '' })
const loading = ref(false)

const submitBooking = async () => {
  if (!authStore.user) {
    alert('Please login first')
    return router.push('/login')
  }

  // === Room Booking: Max 2 hours per day ===
  if (route.query.type === 'room') {
    const start = form.value.startTime
    const end = form.value.endTime

    // Calculate duration in hours
    const startHour = parseInt(start.split(':')[0])
    const endHour = parseInt(end.split(':')[0])
    const duration = endHour - startHour

    if (duration > 2) {
      alert('You can only book a room for a maximum of 2 hours per day.')
      return
    }
  }

  // === Book Borrowing: Max 3 books per week (Basic Check) ===
  if (route.query.type === 'book') {
    try {
      const res = await api.get(`/bookings.php?user_id=${authStore.user.id}`)
      const thisWeekBookings = res.data.filter(b => {
        const bookingDate = new Date(b.booking_date)
        const today = new Date()
        const diffDays = (today - bookingDate) / (1000 * 60 * 60 * 24)
        return diffDays <= 7 && b.status === 'confirmed'
      })

      if (thisWeekBookings.length >= 3) {
        alert('You have reached the limit of 3 book borrowings per week.')
        return
      }
    } catch (error) {
      console.error(error)
    }
  }

  loading.value = true
  try {
    await api.post('/bookings.php', {
      user_id: authStore.user.id,
      booking_date: form.value.date,
      booking_time: form.value.time,
      purpose: form.value.purpose
    })

    router.push({
      path: '/booking-confirmation',
      query: {
        date: form.value.date,
        time: form.value.time,
        purpose: form.value.purpose
      }
    })
  } catch (error) {
    console.error('Booking failed:', error)
    alert('Failed to save booking. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
