<template>
  <div class="dashboard-page py-5" style="background-color: #F8F4F0;">
    <div class="container">

      <!-- Welcome Header -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <div>
          <h1 class="fw-bold mb-1" style="color: #2C2C2C;">Welcome back, {{ userName }} 💕</h1>
          <p class="text-muted mb-0">Your personal library overview</p>
        </div>
        <!-- Manage All button removed -->
      </div>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading">

        <!-- RECENTLY BOOKED ROOMS -->
        <div class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0" style="color: #2C2C2C;">
              <i class="bi bi-door-open me-2"></i> Recently Booked Rooms
            </h4>
            <router-link to="/room-history" class="text-decoration-none small fw-semibold" style="color: #E8B4B8;">
              See all room bookings →
            </router-link>
          </div>

          <div v-if="recentRoomBookings.length === 0" class="text-center py-4 bg-white rounded-3 shadow-sm">
            <p class="text-muted mb-0">You have no recent room bookings.</p>
          </div>

          <div class="row g-3" v-else>
            <div class="col-md-6 col-lg-4" v-for="room in recentRoomBookings" :key="room.id">
              <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info text-white px-3 py-1">Study Room</span>
                    <span class="badge" :class="getStatusBadgeClass(room.status)">{{ room.status }}</span>
                  </div>

                  <h5 class="fw-semibold mb-1" style="color: #2C2C2C;">{{ room.room_name }}</h5>
                  <p class="text-muted small mb-1">
                    {{ room.date }} • {{ room.time }}<br>
                    <small>{{ room.location || 'Main Library' }}</small>
                  </p>

                  <div class="d-flex gap-2 mt-3">
                    <!-- View button removed -->
                    <button
                      v-if="room.status !== 'completed' && room.status !== 'cancelled'"
                      class="btn btn-sm btn-outline-danger flex-fill"
                      @click="cancelRoomBooking(room)">
                      Cancel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- BORROWED BOOKS -->
        <div class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0" style="color: #2C2C2C;">
              <i class="bi bi-book me-2"></i> Borrowed Books
            </h4>
            <router-link to="/history" class="text-decoration-none small fw-semibold" style="color: #E8B4B8;">
              View borrowing history →
            </router-link>
          </div>

          <div v-if="borrowedBooks.length === 0" class="text-center py-4 bg-white rounded-3 shadow-sm">
            <p class="text-muted mb-0">You haven't borrowed any books yet.</p>
          </div>

          <div class="row g-3" v-else>
            <div class="col-md-6 col-lg-4" v-for="book in borrowedBooks" :key="book.id">
              <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="book-cover me-3 flex-shrink-0" style="width: 55px; height: 75px; background-color: #E8B4B8; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                      <span class="text-white fw-bold" style="font-size: 1.1rem;">📖</span>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="fw-semibold mb-1 text-truncate" style="color: #2C2C2C;">{{ book.title }}</h6>
                      <p class="text-muted small mb-1">{{ book.author }}</p>

                      <div class="small">
                        <div><strong>Borrowed:</strong> {{ book.borrowed_date }}</div>
                        <div><strong>Due:</strong> {{ book.due_date }}</div>
                        <span class="badge mt-1" :class="getBorrowStatusClass(book.status)">{{ book.status }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- OVERDUE BOOKS -->
        <div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0 text-danger">
              <i class="bi bi-exclamation-triangle me-2"></i> Overdue Books
            </h4>
            <span class="badge bg-danger text-white px-3 py-1">Action Required</span>
          </div>

          <div v-if="overdueBooks.length === 0" class="text-center py-4 bg-white rounded-3 shadow-sm">
            <p class="text-success mb-0">🎉 Great! You have no overdue books.</p>
          </div>

          <div class="row g-3" v-else>
            <div class="col-md-6 col-lg-4" v-for="book in overdueBooks" :key="book.id">
              <div class="card border-0 shadow-sm h-100 hover-card border-danger border-2">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="book-cover me-3 flex-shrink-0" style="width: 55px; height: 75px; background-color: #dc3545; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                      <span class="text-white fw-bold" style="font-size: 1.1rem;">⚠️</span>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="fw-semibold mb-1 text-truncate" style="color: #2C2C2C;">{{ book.title }}</h6>
                      <p class="text-muted small mb-1">{{ book.author }}</p>

                      <div class="small text-danger fw-semibold">
                        <div>Due: {{ book.due_date }}</div>
                        <div class="mt-1">Overdue by {{ calculateOverdueDays(book.due_date) }} days</div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3">
                    <button
                      class="btn btn-sm btn-danger w-100"
                      @click="renewOverdueBook(book)">
                      Renew for 7 More Days
                    </button>
                    <small class="text-muted d-block mt-1 text-center">Only Admin can mark as returned</small>
                  </div>
                </div>
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
import LoadingSpinner from '../components/LoadingSpinner.vue'
import api from '../services/api.js'
import {toast} from 'vue3-toastify'
import { useRoute } from 'vue-router'
const authStore = useAuthStore()
const route = useRoute()
const loading = ref(true)
const userName = computed(() => authStore.user?.name || 'Reader')

const recentRoomBookings = ref([])
const borrowedBooks = ref([])
const overdueBooks = ref([])

const getStatusBadgeClass = (status) => {
  if (status === 'confirmed' || status === 'active') return 'bg-success text-white'
  if (status === 'pending') return 'bg-warning text-dark'
  if (status === 'cancelled' || status === 'completed') return 'bg-secondary text-white'
  return 'bg-secondary text-white'
}

const getBorrowStatusClass = (status) => {
  if (status === 'Borrowed') return 'bg-pink text-dark'
  if (status === 'Returned') return 'bg-success text-white'
  if (status === 'Overdue') return 'bg-danger text-white'
  return 'bg-secondary text-white'
}

const calculateOverdueDays = (dueDate) => {
  const due = new Date(dueDate)
  const today = new Date()
  const diffTime = Math.abs(today - due)
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

const cancelRoomBooking = async (booking) => {
  if (!confirm(`Cancel booking for "${booking.room_name}"?`)) return

  try {
    await api.post('/bookings.php?action=cancel_room', { booking_id: booking.id })
    alert('Room booking cancelled successfully!')
    loadDashboardData()
  } catch (error) {
    console.error(error)
    alert('Failed to cancel room booking. Please try again.')
  }
}

const renewOverdueBook = async (book) => {
  if (!confirm(`Renew "${book.title}" for another 7 days?`)) return

  try {
    await api.post('/bookings.php?action=renew_book', { borrowing_id: book.id })
    alert(`"${book.title}" renewed successfully for 7 more days!`)
    loadDashboardData()
  } catch (error) {
    console.error(error)
    alert('Failed to renew book. Please try again.')
  }
}

const loadDashboardData = async () => {
  if (!authStore.user?.id) {
    loading.value = false
    return
  }

  loading.value = true
  try {
    const userId = authStore.user.id

    // Recent Room Bookings
    const roomRes = await api.get(`/bookings.php?action=get&user_id=${userId}`)
    const allRooms = roomRes.data?.room_bookings || []
    recentRoomBookings.value = allRooms
      .filter(r => !['completed', 'cancelled'].includes((r.status || '').toLowerCase()))
      .sort((a, b) => new Date(b.start_time) - new Date(a.start_time))
      .slice(0, 3)
      .map(room => ({
        id: room.id,
        room_name: room.room_name,
        date: new Date(room.start_time).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }),
        time: `${new Date(room.start_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})} - ${new Date(room.end_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}`,
        status: room.status,
        location: room.location || 'Main Library'
      }))

    // Borrowed Books
    const allBorrowed = roomRes.data?.borrowed_books || []
    borrowedBooks.value = allBorrowed
      .filter(b => ['borrowed', 'overdue'].includes((b.status || '').toLowerCase()))
      .sort((a, b) => new Date(b.borrow_date) - new Date(a.borrow_date))
      .slice(0, 3)

    // Overdue Books
    const today = new Date()
    overdueBooks.value = allBorrowed.filter(book => {
      if (!book.due_date) return false
      const due = new Date(book.due_date)
      return due < today && book.status !== 'Returned'
    }).slice(0, 3)

  } catch (error) {
    console.error('Failed to load dashboard data:', error)
    recentRoomBookings.value = []
    borrowedBooks.value = []
    overdueBooks.value = []
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadDashboardData()

  // Show success message if user just completed a booking
  if (route.query.success === 'true') {
    toast.success('Your booking has been confirmed!', { autoClose: 3000 })
    // Clean URL
    window.history.replaceState({}, document.title, window.location.pathname)
  }
})
</script>

<style scoped>
.dashboard-page {
  min-height: 80vh;
}

.hover-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
}

.hover-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
}

.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
  transition: all 0.3s ease;
}

.btn-pink:hover {
  background-color: #D89CA1;
  color: #2C2C2C;
  transform: translateY(-2px);
}

.bg-pink {
  background-color: #E8B4B8 !important;
}

.book-cover {
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}
</style>
