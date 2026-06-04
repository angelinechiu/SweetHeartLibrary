<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #2C2C2C;">My Bookings</h2>
        <router-link to="/dashboard" class="btn btn-outline-secondary btn-sm">← Back to Dashboard</router-link>
      </div>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading">
        <div class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0" style="color: #2C2C2C;">Recent Bookings</h5>
          </div>

          <div v-if="combinedBookings.length === 0" class="text-center py-4 bg-white rounded-3 shadow-sm">
            <p class="text-muted mb-0">You have no recent bookings yet.</p>
          </div>

          <div class="row g-3" v-else>
            <div class="col-md-6 col-lg-4" v-for="booking in combinedBookings" :key="`${booking.type}-${booking.id}`">
              <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span
                      class="badge px-3 py-1"
                      :class="booking.type === 'room' ? 'bg-info text-white' : 'bg-pink text-dark'"
                    >
                      {{ booking.type === 'room' ? 'Study Room' : 'Book' }}
                    </span>
                    <span class="badge" :class="getStatusBadgeClass(booking.status)">
                      {{ booking.status }}
                    </span>
                  </div>

                  <h5 class="card-title fw-semibold mb-1" style="color: #2C2C2C;">
                    {{ booking.title || booking.room_name }}
                  </h5>
                  <p class="text-muted small mb-2">
                    {{ booking.date }} • {{ booking.time }}
                  </p>

                  <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-sm btn-outline-secondary flex-fill" @click="viewBooking(booking)">
                      View
                    </button>
                    <button
                      v-if="booking.type === 'room' && booking.status !== 'Completed'"
                      class="btn btn-sm btn-outline-danger flex-fill"
                      @click="updateBookingStatus(booking)"
                    >
                      Cancel
                    </button>
                    <button
                      v-if="booking.type === 'book' && booking.status !== 'Returned'"
                      class="btn btn-sm btn-outline-danger flex-fill"
                      @click="updateBookingStatus(booking)"
                    >
                      Mark Returned
                    </button>
                    <button class="btn btn-sm btn-pink flex-fill" @click="rebook(booking)">
                      Rebook
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-lg-6">
            <h5 class="mb-3 text-success">My Borrowed Books</h5>
            <div class="table-responsive mb-4">
              <table class="table table-hover">
                <thead style="background-color: #2C2C2C; color: #F8F4F0;">
                  <tr>
                    <th>Book</th>
                    <th>Author</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="book in borrowedBooks" :key="book.id">
                    <td>{{ book.book_title }}</td>
                    <td>{{ book.author }}</td>
                    <td>{{ book.borrow_date }}</td>
                    <td>{{ book.due_date }}</td>
                    <td>
                      <span class="badge" :class="book.status === 'Overdue' ? 'bg-danger' : 'bg-success'">
                        {{ book.status }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="borrowedBooks.length === 0">
                    <td colspan="5" class="text-center text-muted py-4">You have no borrowed books at the moment.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-lg-6">
            <h5 class="mb-3 text-info">My Room Bookings</h5>
            <div class="table-responsive mb-4">
              <table class="table table-hover">
                <thead style="background-color: #2C2C2C; color: #F8F4F0;">
                  <tr>
                    <th>Room</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="room in roomBookings" :key="room.id">
                    <td>{{ room.room_name }}</td>
                    <td>{{ formatDateTime(room.start_time) }}</td>
                    <td>{{ formatDateTime(room.end_time) }}</td>
                    <td>
                      <span class="badge" :class="room.status === 'Active' ? 'bg-success' : 'bg-secondary'">
                        {{ room.status }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="roomBookings.length === 0">
                    <td colspan="4" class="text-center text-muted py-4">You have no room bookings at the moment.</td>
                  </tr>
                </tbody>
              </table>
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
const borrowedBooks = ref([])
const roomBookings = ref([])
const combinedBookings = ref([])
const loading = ref(true)

const getStatusBadgeClass = (status) => {
  if (status === 'confirmed' || status === 'Active' || status === 'On Time') return 'bg-success text-white'
  if (status === 'Overdue' || status === 'cancelled') return 'bg-danger text-white'
  if (status === 'Returned' || status === 'Completed') return 'bg-secondary text-white'
  return 'bg-secondary text-white'
}

const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  return new Date(dateTime).toLocaleString([], { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })
}

const transformCombinedBookings = (books, rooms) => {
  const combined = []

  books.forEach((book) => {
    combined.push({
      id: book.id,
      type: 'book',
      title: book.book_title,
      author: book.author,
      date: book.borrow_date,
      time: `Due ${book.due_date}`,
      status: book.status,
      details: book,
    })
  })

  rooms.forEach((room) => {
    const start = new Date(room.start_time)
    const end = new Date(room.end_time)
    combined.push({
      id: room.id,
      type: 'room',
      room_name: room.room_name,
      date: start.toISOString().split('T')[0],
      time: `${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`,
      status: room.status,
      details: room,
    })
  })

  return combined.sort((a, b) => {
    if (a.date === b.date) return a.time.localeCompare(b.time)
    return a.date < b.date ? 1 : -1
  })
}

const loadBookings = async () => {
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?action=get&user_id=${authStore.user.id}`)
    borrowedBooks.value = res.data.borrowed_books || []
    roomBookings.value = res.data.room_bookings || []
    combinedBookings.value = transformCombinedBookings(borrowedBooks.value, roomBookings.value)
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const viewBooking = (booking) => {
  if (booking.type === 'room') {
    return router.push({ name: 'RoomHistory' })
  }

  if (booking.details?.book_id) {
    return router.push({ name: 'BookDetail', params: { id: booking.details.book_id } })
  }

  return router.push({ name: 'History' })
}

const updateBookingStatus = async (booking) => {
  if (!confirm(`Are you sure you want to update this ${booking.type === 'room' ? 'room' : 'book'} booking?`)) return

  try {
    if (booking.type === 'room') {
      await api.post('/bookings.php', { action: 'mark_room_available', booking_id: booking.id })
      booking.status = 'Completed'
      roomBookings.value = roomBookings.value.map((item) => item.id === booking.id ? { ...item, status: 'Completed' } : item)
    } else {
      await api.post('/bookings.php', { action: 'mark_returned', booking_id: booking.id })
      booking.status = 'Returned'
      borrowedBooks.value = borrowedBooks.value.map((item) => item.id === booking.id ? { ...item, status: 'Returned' } : item)
    }
    combinedBookings.value = transformCombinedBookings(borrowedBooks.value, roomBookings.value)
    alert('Booking status updated successfully.')
  } catch (error) {
    console.error(error)
    alert('Unable to update booking status. Please try again.')
  }
}

const rebook = (booking) => {
  if (booking.type === 'room') {
    return router.push({ name: 'RoomBooking' })
  }
  return router.push({ name: 'BookCatalog' })
}

onMounted(loadBookings)
</script>

<style scoped>
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
}

.btn-pink:hover {
  background-color: #D89CA1;
  color: #2C2C2C;
}

.hover-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
}

.hover-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
}
</style>
