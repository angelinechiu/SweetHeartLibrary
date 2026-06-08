<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container text-center">

      <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
      <h2 class="fw-bold mt-4" style="color: #2C2C2C;">Booking Confirmation</h2>
      <p class="text-muted">Please review your booking details before confirming.</p>

      
      <div v-if="isBookBorrow" class="card border-0 shadow-sm mx-auto mt-4" style="max-width: 520px;">
        <div class="card-body text-start p-4">
          <h6 class="fw-bold mb-3">Book Borrowing Summary</h6>

          <div class="row mb-2">
            <div class="col-5 text-muted">Book Title</div>
            <div class="col-7 fw-semibold">{{ bookTitle }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Borrowing Period</div>
            <div class="col-7 fw-semibold">14 days</div>
          </div>
          <div class="row mb-2">
            <div class="col-5 text-muted">Due Date</div>
            <div class="col-7 fw-semibold">{{ dueDate }}</div>
          </div>
          <div class="row">
            <div class="col-5 text-muted">Status</div>
            <div class="col-7">
              <span class="badge bg-warning text-dark px-3 py-1">Pending Confirmation</span>
            </div>
          </div>
        </div>
      </div>

      
      <div v-else class="card border-0 shadow-sm mx-auto mt-4" style="max-width: 520px;">
        <div class="card-body text-start p-4">
          <h6 class="fw-bold mb-3">Room Booking Summary</h6>

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
          <div class="row">
            <div class="col-5 text-muted">Status</div>
            <div class="col-7">
              <span class="badge bg-warning text-dark px-3 py-1">Pending Confirmation</span>
            </div>
          </div>
        </div>
      </div>

      
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

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const isSubmitting = ref(false)


const isBookBorrow = computed(() => route.query.type === 'book')


const bookId = route.query.bookId
const bookTitle = route.query.bookTitle || ''


const dueDate = computed(() => {
  const date = new Date()
  date.setDate(date.getDate() + 14)
  return date.toISOString().split('T')[0]
})


const roomName = route.query.roomName || 'Study Room'
const date = route.query.date || ''
const startTime = route.query.startTime || ''
const endTime = route.query.endTime || ''

const confirmBooking = async () => {
  isSubmitting.value = true

  try {
    if (isBookBorrow.value) {
      

      
      console.log('bookId from query:', bookId)
      console.log('Current user from store:', authStore.user)

      if (!bookId) {
        alert('Error: Book ID is missing. Please go back and try again.')
        return
      }

      if (!authStore.user?.id) {
        alert('Error: You are not logged in. Please login again.')
        router.push('/login')
        return
      }

      const response = await api.post('/bookings.php', {
        action: 'borrow_book',
        book_id: bookId,
        user_id: authStore.user.id
      })

      if (response.data.success) {
        alert('Book borrowed successfully!')
        router.push('/my-bookings')
      } else {
        alert(response.data.message || 'Failed to borrow book')
      }

    } else {
      
      await api.post('/bookings.php', {
        action: 'create_room_booking',
        user_id: authStore.user?.id,
        room_name: roomName,
        start_time: `${date} ${startTime}:00`,
        end_time: `${date} ${endTime}:00`
      })
      alert('Room booking confirmed successfully!')
      router.push('/my-bookings')
    }

  } catch (error) {
    console.error('Borrow error full details:', error)
    const message = error.response?.data?.message || 'Failed to confirm booking. Please try again.'
    alert(message)
  } finally {
    isSubmitting.value = false
  }
}
</script>
