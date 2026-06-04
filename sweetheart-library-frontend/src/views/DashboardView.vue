<template>
  <div class="dashboard-page py-5" style="background-color: #F8F4F0;">
    <div class="container">

      <!-- Welcome Header -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fw-bold mb-1" style="color: #2C2C2C;">Welcome back, {{ userName }} 💕</h1>
          <p class="text-muted mb-0">Here's a quick overview of your recent activity</p>
        </div>
        <div class="mt-2 mt-md-0">
          <router-link to="/my-bookings" class="btn btn-pink px-4">View All Bookings</router-link>
        </div>
      </div>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading">

        <!-- ==================== RECENTLY BOOKED ==================== -->
        <div class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0" style="color: #2C2C2C;">Recently Booked</h4>
            <router-link to="/my-bookings" class="text-decoration-none small" style="color: #E8B4B8;">See all →</router-link>
          </div>

          <div v-if="recentBookings.length === 0" class="text-center py-4 bg-white rounded-3 shadow-sm">
            <p class="text-muted mb-0">No recent bookings yet.</p>
          </div>

          <div class="row g-3" v-else>
            <div class="col-md-6 col-lg-4" v-for="booking in recentBookings" :key="booking.id">
              <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body">
                  <!-- Type Badge -->
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
                      v-if="booking.status !== 'cancelled'"
                      class="btn btn-sm btn-outline-danger flex-fill"
                      @click="cancelBooking(booking)"
                    >
                      Cancel
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

        <!-- ==================== BORROWED BOOKS HISTORY ==================== -->
        <div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0" style="color: #2C2C2C;">Borrowed Books History</h4>
            <router-link to="/history" class="text-decoration-none small" style="color: #E8B4B8;">View full history →</router-link>
          </div>

          <div v-if="borrowedBooks.length === 0" class="text-center py-4 bg-white rounded-3 shadow-sm">
            <p class="text-muted mb-0">You haven't borrowed any books yet.</p>
          </div>

          <div class="row g-3" v-else>
            <div class="col-md-6 col-lg-4" v-for="book in borrowedBooks" :key="book.id">
              <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body">
                  <div class="d-flex">
                    <!-- Book Cover Placeholder -->
                    <div class="book-cover me-3 flex-shrink-0"
                         style="width: 60px; height: 80px; background-color: #E8B4B8; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                      <span class="text-white fw-bold" style="font-size: 0.75rem;">📖</span>
                    </div>

                    <div class="flex-grow-1">
                      <h6 class="fw-semibold mb-1" style="color: #2C2C2C;">{{ book.title }}</h6>
                      <p class="text-muted small mb-1">{{ book.author }}</p>

                      <div class="small">
                        <div><strong>Borrowed:</strong> {{ book.borrowed_date }}</div>
                        <div><strong>Due:</strong> {{ book.due_date }}</div>
                        <div class="mt-1">
                          <span class="badge" :class="getBorrowStatusClass(book.status)">
                            {{ book.status }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary flex-fill" @click="viewBookDetails(book)">
                      View Details
                    </button>
                    <button v-if="book.status === 'Borrowed'" class="btn btn-sm btn-pink flex-fill" @click="renewBook(book)">
                      Renew
                    </button>
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
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const userName = computed(() => authStore.user?.name || 'Reader')

// ==================== MOCK DATA (Replace with real API later) ====================
const recentBookings = ref([])
const borrowedBooks = ref([])

// ==================== HELPER FUNCTIONS ====================
const getStatusBadgeClass = (status) => {
  if (status === 'confirmed') return 'bg-success text-white'
  if (status === 'pending') return 'bg-warning text-dark'
  if (status === 'cancelled') return 'bg-danger text-white'
  return 'bg-secondary text-white'
}

const getBorrowStatusClass = (status) => {
  if (status === 'Borrowed') return 'bg-pink text-dark'
  if (status === 'Returned') return 'bg-success text-white'
  return 'bg-secondary text-white'
}

// ==================== ACTIONS ====================
const viewBooking = (booking) => {
  if (booking.type === 'room') {
    router.push('/rooms')
  } else {
    router.push(`/books/${booking.booking_id}`)
  }
}

const cancelBooking = async (booking) => {
  if (!confirm(`Are you sure you want to cancel this ${booking.type} booking?`)) return

  // TODO: Call real API
  // await api.post('/bookings.php', { action: 'cancel', booking_id: booking.booking_id })

  // For now, update local state
  const index = recentBookings.value.findIndex(b => b.id === booking.id)
  if (index !== -1) {
    recentBookings.value[index].status = 'cancelled'
  }

  alert('Booking cancelled successfully!')
}

const rebook = (booking) => {
  if (booking.type === 'room') {
    router.push('/rooms')
  } else {
    router.push('/books')
  }
}

const viewBookDetails = (book) => {
  router.push(`/books/${book.id}`)
}

const renewBook = (book) => {
  if (!confirm(`Do you want to renew "${book.title}" for another 7 days?`)) return

  // Simulate renewal by extending due date by 7 days
  const currentDue = new Date(book.due_date)
  currentDue.setDate(currentDue.getDate() + 7)

  const newDueDate = currentDue.toISOString().split('T')[0]

  // Update in mock data
  const index = borrowedBooks.value.findIndex(b => b.id === book.id)
  if (index !== -1) {
    borrowedBooks.value[index].due_date = newDueDate
    borrowedBooks.value[index].status = 'Borrowed' // ensure status stays Borrowed
  }

  alert(`"${book.title}" has been renewed successfully!\nNew due date: ${newDueDate}`)

  // TODO: When backend is ready, call API like:
  // await api.post('/borrowings.php', { action: 'renew', borrowing_id: book.id, new_due_date: newDueDate })
}

// ==================== LOAD DATA ====================
const loadDashboardData = async () => {
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?action=get&user_id=${authStore.user?.id}`)
    recentBookings.value = (res.data.combined_bookings || []).slice(0, 6)
    borrowedBooks.value = res.data.borrowed_books || []
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (authStore.user) {
    loadDashboardData()
  } else {
    loading.value = false
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
