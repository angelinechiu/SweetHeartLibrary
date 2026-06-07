<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <div class="text-center mb-4">
        <h3 class="fw-bold">Book {{ roomName }}</h3>
        <p class="text-muted">Maximum <strong>2 hours</strong> per day</p>
      </div>

      <div class="card border-0 shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">

          <div class="mb-3">
            <label class="form-label fw-semibold">Date</label>
            <input type="date" v-model="bookingDate" class="form-control" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Start Time</label>
              <input type="time" v-model="startTime" class="form-control" @change="validateDuration">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">End Time</label>
              <input type="time" v-model="endTime" class="form-control" @change="validateDuration">
            </div>
          </div>

          <div v-if="duration > 0" class="mb-3">
            <div class="alert" :class="isValidDuration ? 'alert-success' : 'alert-danger'">
              Duration: <strong>{{ duration.toFixed(1) }} hour(s)</strong>
              <span v-if="!isValidDuration" class="d-block text-danger mt-1">
                You cannot book more than 2 hours.
              </span>
            </div>
          </div>

          <button
            class="btn btn-pink w-100"
            :disabled="!isFormValid"
            @click="checkAndProceed">
            Continue
          </button>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const roomName = route.query.roomName || 'Study Room'
const bookingDate = ref('')
const startTime = ref('')
const endTime = ref('')
const duration = ref(0)
const isValidDuration = ref(true)

const isFormValid = computed(() => {
  return bookingDate.value && startTime.value && endTime.value && isValidDuration.value
})

const validateDuration = () => {
  if (!startTime.value || !endTime.value) {
    duration.value = 0
    isValidDuration.value = true
    return
  }

  const [sh, sm] = startTime.value.split(':').map(Number)
  const [eh, em] = endTime.value.split(':').map(Number)

  let diff = ((eh * 60 + em) - (sh * 60 + sm)) / 60
  if (diff < 0) diff += 24

  duration.value = diff
  isValidDuration.value = diff > 0 && diff <= 2
}

const checkAndProceed = async () => {
  if (!isFormValid.value) return

  try {
    const res = await api.get(
      `/bookings.php?action=check_daily_limit&user_id=${authStore.user.id}&date=${bookingDate.value}`
    )

    if (res.data.success === false) {
      toast.error('Error checking booking limit: ' + (res.data.message || 'Unknown error'))
      return
    }

    if (res.data.total_hours >= 2) {
      toast.error('You have reached the maximum 2 hours booking limit for today.')
      return
    }

    router.push({
      path: '/booking-confirmation',
      query: {
        roomId: route.query.roomId,
        roomName: roomName,
        date: bookingDate.value,
        startTime: startTime.value,
        endTime: endTime.value
      }
    })
  } catch (error) {
    console.error('Booking limit check failed:', error)
    toast.error('Network error. Please check if the backend is running.')
  }
}
</script>
