<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold mb-1" style="color: #2C2C2C;">Borrowing History</h2>
          <p class="text-muted mb-0">All your book borrowing records</p>
        </div>
        <router-link to="/books" class="btn btn-pink">
          <i class="bi bi-plus-lg me-1"></i> Browse Books
        </router-link>
      </div>

      <LoadingSpinner :loading="loading" />

      <div v-if="!loading" class="row g-3">
        
        <div v-if="overdueBooks.length > 0" class="col-12 mb-2">
          <h5 class="text-danger fw-bold mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Overdue Books ({{ overdueBooks.length }})
          </h5>
        </div>

        <div
          v-for="book in borrowedHistory"
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

              
              <h5 class="fw-semibold mb-1" style="color: #2C2C2C;">
                {{ book.book_title || book.title }}
              </h5>
              <p class="text-muted small mb-2">{{ book.author }}</p>

              
              <div class="small mb-3">
                <div><strong>Borrowed:</strong> {{ book.borrow_date || book.borrowed_date }}</div>
                <div><strong>Due:</strong> {{ formatDate(book.due_date) }}</div>

                <div v-if="isOverdue(book)" class="text-danger fw-semibold mt-1">
                  <i class="bi bi-exclamation-circle-fill me-1"></i>
                  Overdue by {{ calculateOverdueDays(book.due_date) }} days
                </div>
              </div>

              
              <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-sm btn-outline-secondary flex-fill">View</button>

                
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

        
        <div v-if="borrowedHistory.length === 0" class="col-12">
          <div class="text-center py-5 bg-white rounded-3 shadow-sm">
            <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
            <p class="text-muted mb-0">No borrowing history found.</p>
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
const allBorrowed = ref([])
const loading = ref(true)

const borrowedHistory = computed(() => {
  if (!allBorrowed.value) return []
  return [...allBorrowed.value].sort((a, b) => {
    const aOverdue = isOverdue(a) ? 1 : 0
    const bOverdue = isOverdue(b) ? 1 : 0
    return bOverdue - aOverdue
  })
})

const overdueBooks = computed(() => {
  if (!allBorrowed.value) return []
  return allBorrowed.value.filter(book => isOverdue(book))
})

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A'
  return new Date(dateStr).toLocaleDateString('en-GB', {
    day: 'numeric', month: 'short', year: 'numeric'
  })
}

const getStatusClass = (status) => {
  if (!status) return 'bg-secondary text-white'
  const s = status.toLowerCase()
  if (s === 'borrowed') return 'bg-pink text-dark'
  if (s === 'returned') return 'bg-success text-white'
  if (s === 'overdue') return 'bg-danger text-white'
  return 'bg-secondary text-white'
}

const isOverdue = (book) => {
  if (!book.due_date) return false
  const due = new Date(book.due_date)
  const today = new Date()
  return due < today && book.status !== 'Returned'
}

const calculateOverdueDays = (dueDate) => {
  if (!dueDate) return 0
  const due = new Date(dueDate)
  const today = new Date()
  return Math.ceil((today - due) / (1000 * 60 * 60 * 24))
}


const canRenew = (book) => {
  return isOverdue(book) || book.status === 'Overdue'
}

const renewBook = async (book) => {
  if (!confirm(`Renew "${book.book_title}" for 7 more days?`)) return

  try {
    await api.post('/bookings.php?action=renew_book', { borrowing_id: book.id })
    alert('Book renewed successfully!')
    await loadHistory()
  } catch (error) {
    console.error(error)
    alert('Failed to renew book')
  }
}

const loadHistory = async () => {
  if (!authStore.user?.id) return
  loading.value = true
  try {
    const res = await api.get(`/bookings.php?action=get&user_id=${authStore.user.id}`)
    allBorrowed.value = res.data?.borrowed_books || []
  } catch (error) {
    console.error(error)
    allBorrowed.value = []
  } finally {
    loading.value = false
  }
}

onMounted(loadHistory)
</script>
