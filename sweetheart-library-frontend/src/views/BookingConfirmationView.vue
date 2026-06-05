<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container text-center">

      <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
      <h2 class="fw-bold mt-4" style="color: #2C2C2C;">Booking Confirmation</h2>
      <p class="text-muted">Please review your booking details before confirming.</p>

      <!-- ==================== BOOK BORROWING ==================== -->
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

      <!-- ==================== ROOM BOOKING (Your Original) ==================== -->
      <div v-else class="card border-0 shadow-sm mx-auto mt-4" style="max-width: 520px;">
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

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'

const route = useRoute()
const router = useRouter()
const isSubmitting = ref(false)

// Detect booking type
const isBookBorrow = computed(() => route.query.type === 'book')

// Book data
const bookId = route.query.bookId
const bookTitle = route.query.bookTitle || ''

// Calculate due date (14 days)
const dueDate = computed(() => {
  const date = new Date()
  date.setDate(date.getDate() + 14)
  return date.toISOString().split('T')[0]
})

// Room data (your original)
const roomName = route.query.roomName || 'Study Room'
const date = route.query.date || ''
const startTime = route.query.startTime || ''
const endTime = route.query.endTime || ''

const confirmBooking = async () => {
  isSubmitting.value = true

  try {
    if (isBookBorrow.value) {
      // === BOOK BORROWING ===
      await api.post('/books.php', {
        action: 'borrow_book',
        book_id: bookId
      })

      alert('Book borrowed successfully!')
      router.push('/my-bookings')

    } else {
      // === ROOM BOOKING (Your original logic) ===
      await api.post('/bookings.php', {
        action: 'create_room_booking',
        user_id: 1, // You can replace with actual user ID from store
        room_id: route.query.roomId,
        booking_date: date,
        start_time: startTime,
        end_time: endTime
      })

      alert('Room booking confirmed successfully!')
      router.push('/my-bookings')
    }

  } catch (error) {
    console.error(error)
    alert('Failed to confirm booking. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}
</script>
