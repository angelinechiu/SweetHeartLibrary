<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold mb-1" style="color: #2C2C2C;">My Bookings</h2>
          <p class="text-muted mb-0">Your current active bookings</p>
        </div>
        <div class="d-flex gap-2">
          <router-link to="/rooms" class="btn btn-pink">Book Room</router-link>
          <router-link to="/books" class="btn btn-outline-dark">Browse Books</router-link>
        </div>
      </div>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading">

        <!-- ACTIVE ROOM BOOKINGS -->
        <div class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0" style="color: #2C2C2C;">
              <i class="bi bi-door-open me-2"></i>
              Active Room Bookings <span class="text-muted small">({{ activeRoomBookings.length }})</span>
            </h4>
          </div>

          <div class="row g-3">
            <div v-for="room in activeRoomBookings" :key="room.id" class="col-md-6 col-lg-4">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info text-white px-3 py-1">Study Room</span>
                    <span class="badge px-3 py-1" :class="getStatusClass(room.status)">
                      {{ room.status || 'Pending' }}
                    </span>
                  </div>

                  <h5 class="fw-semibold mb-1">{{ room.room_name || room.purpose }}</h5>
                  <p class="text-muted small mb-2">
                    {{ formatDate(room.start_time) }} • {{ formatTime(room) }}
                  </p>

                  <div class="d-flex gap-2 mt-auto">
                    <button
                      v-if="canCancelRoom(room)"
                      class="btn btn-sm btn-outline-danger flex-fill"
                      @click="cancelRoom(room)">
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeRoomBookings.length === 0" class="col-12">
              <div class="text-center py-4 bg-white rounded-3 shadow-sm">
                <p class="text-muted mb-0">No active room bookings.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ACTIVE BORROWED BOOKS -->
        <div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0" style="color: #2C2C2C;">
              <i class="bi bi-book me-2"></i>
              Active Borrowed Books <span class="text-muted small">({{ activeBorrowedBooks.length }})</span>
            </h4>
          </div>

          <div class="row g-3">
            <div
              v-for="book in activeBorrowedBooks"
              :key="book.id"
              class="col-md-6 col-lg-4"
            >
              <div
                class="card border-0 shadow-sm h-100"
                :class="{ 'border-danger border-2': isOverdue(book) }"
              >
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-pink text-dark px-3 py-1">Book</span>
                    <span class="badge px-3 py-1" :class="getStatusClass(book.status)">
                      {{ book.status || 'Borrowed' }}
                    </span>
                  </div>

                  <h5 class="fw-semibold mb-1">{{ book.book_title || book.title }}</h5>
                  <p class="text-muted small mb-1">{{ book.author }}</p>

                  <div class="small mb-3">
                    <div><strong>Borrowed:</strong> {{ formatDate(book.borrow_date) }}</div>
                    <div><strong>Due:</strong> {{ formatDate(book.due_date) }}</div>
                    <div v-if="isOverdue(book)" class="text-danger fw-semibold mt-1">
                      Overdue by {{ calculateOverdueDays(book.due_date) }} days
                    </div>
                  </div>

                  <div class="d-flex gap-2 mt-auto">
                    <button
                      v-if="canRenew(book)"
                      class="btn btn-sm btn-danger flex-fill"
                      @click="renewBook(book)">
                      Renew
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="activeBorrowedBooks.length === 0" class="col-12">
              <div class="text-center py-4 bg-white rounded-3 shadow-sm">
                <p class="text-muted mb-0">No active borrowed books.</p>
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
import LoadingSpinner from '../components/LoadingSpinner.vue'

const authStore = useAuthStore()
const allData = ref({ borrowed_books: [], room_bookings: [] })
const loading = ref(true)

const activeRoomBookings = computed(() => {
  if (!allData.value.room_bookings) return []
  return allData.value.room_bookings.filter(room => {
    const s = (room.status || '').toLowerCase()
    return !['completed', 'cancelled', 'returned'].includes(s)
  })
})

const activeBorrowedBooks = computed(() => {
  if (!allData.value.borrowed_books) return []
  return allData.value.borrowed_books.filter(book => {
    const s = (book.status || '').toLowerCase()
    return ['borrowed', 'overdue'].includes(s)
  })
})

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A'
  return new Date(dateStr).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

const formatTime = (room) => {
  if (!room.start_time || !room.end_time) return ''
  return `${new Date(room.start_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})} - ${new Date(room.end_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}`
}

const getStatusClass = (status) => {
  if (!status) return 'bg-secondary text-white'
  const s = status.toLowerCase()
  if (['confirmed', 'active', 'borrowed'].includes(s)) return 'bg-success text-white'
  if (s === 'overdue') return 'bg-danger text-white'
  if (s === 'returned') return 'bg-success text-white'
  if (s === 'pending') return 'bg-warning text-dark'
  return 'bg-secondary text-white'
}

const isOverdue = (book) => {
  if (!book.due_date) return false
  return new Date(book.due_date) < new Date() && book.status !== 'Returned'
}

const calculateOverdueDays = (dueDate) => {
  if (!dueDate) return 0
  return Math.ceil((new Date() - new Date(dueDate)) / (1000 * 60 * 60 * 24))
}

const canCancelRoom = (room) => {
  const s = (room.status || '').toLowerCase()
  return !['completed', 'cancelled'].includes(s)
}

const canRenew = (book) => {
  return isOverdue(book) || book.status === 'Overdue'
}

const cancelRoom = async (room) => {
  if (!confirm(`Cancel "${room.room_name || room.purpose}"?`)) return
  try {
    await api.post('/bookings.php?action=cancel_room', { booking_id: room.id })
    alert('Cancelled successfully')
    await loadMyBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to cancel')
  }
}

const renewBook = async (book) => {
  if (!confirm(`Renew "${book.book_title}"?`)) return
  try {
    await api.post('/bookings.php?action=renew_book', { borrowing_id: book.id })
    alert('Renewed successfully')
    await loadMyBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to renew')
  }
}

const loadMyBookings = async () => {
  if (!authStore.user?.id) return
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?action=get&user_id=${authStore.user.id}`)
    allData.value = res.data || { borrowed_books: [], room_bookings: [] }
  } catch (error) {
    console.error(error)
    allData.value = { borrowed_books: [], room_bookings: [] }
  } finally {
    loading.value = false
  }
}

onMounted(loadMyBookings)
</script>
