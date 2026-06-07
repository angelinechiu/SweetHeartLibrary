<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold mb-1" style="color: #2C2C2C;">Our Elegant Collection</h2>
          <p class="text-muted mb-0">Discover our curated selection of books</p>
        </div>
        <span class="text-muted">{{ filteredBooks.length }} books</span>
      </div>

      <!-- Search + Filters -->
      <div class="row g-3 mb-4">
        <div class="col-md-5">
          <input v-model="searchQuery" class="form-control form-control-lg" placeholder="Search by title, author or ISBN...">
        </div>
        <div class="col-md-3">
          <select v-model="selectedCategory" class="form-select form-select-lg">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-pink btn-lg w-100" @click="resetFilters">Clear Filters</button>
        </div>
      </div>

      <LoadingSpinner :loading="loading" message="Loading beautiful books..." />

      <!-- Book Cards -->
      <div v-if="!loading" class="row g-3">
        <div v-for="book in paginatedBooks" :key="book.id" class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
              <!-- Badges -->
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge px-3 py-1" style="background-color: #E8B4B8; color: #2C2C2C;">
                  {{ book.category }}
                </span>
                <span class="badge px-3 py-1" :class="book.available_copies > 0 ? 'bg-success' : 'bg-secondary'">
                  {{ book.available_copies > 0 ? 'Available' : 'Unavailable' }}
                </span>
              </div>

              <h5 class="fw-semibold mb-1">{{ book.title }}</h5>
              <p class="text-muted small mb-1">{{ book.author }}</p>

              <div class="small mb-3">
                <div class="text-muted">Published: {{ book.year || 'N/A' }}</div>
                <div class="text-muted">Copies: <strong>{{ book.available_copies }}</strong></div>
              </div>

              <!-- Buttons -->
              <div class="mt-auto d-flex gap-2">
                <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-outline-dark flex-fill">
                  View Details
                </router-link>

                <button
                  v-if="book.available_copies > 0"
                  class="btn btn-sm btn-pink flex-fill"
                  @click="borrowBook(book)"
                >
                  Borrow Book
                </button>
                <button v-else class="btn btn-sm btn-secondary flex-fill" disabled>
                  Currently Unavailable
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================== PAGINATION ==================== -->
      <div v-if="totalPages > 1" class="d-flex justify-content-center mt-5">
        <nav aria-label="Book pagination">
          <ul class="pagination pagination-lg custom-pagination">
            <!-- Previous -->
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button class="page-link" @click="prevPage">
                ← Previous
              </button>
            </li>

            <!-- Page Numbers -->
            <li
              v-for="page in totalPages"
              :key="page"
              class="page-item"
              :class="{ active: currentPage === page }"
            >
              <button class="page-link" @click="goToPage(page)">
                {{ page }}
              </button>
            </li>

            <!-- Next -->
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <button class="page-link" @click="nextPage">
                Next →
              </button>
            </li>
          </ul>
        </nav>
      </div>

      <div v-if="totalPages > 1" class="text-center text-muted small mt-2">
        Page {{ currentPage }} of {{ totalPages }} • {{ filteredBooks.length }} books
      </div>
    </div>
  </div>

  <!-- ==================== T&C MODAL ==================== -->
  <div v-if="showTermsModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 shadow">

        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Borrowing Terms & Conditions</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>

        <div class="modal-body" style="max-height: 420px; overflow-y: auto;">
          <div class="mb-3">
            <h6 class="fw-semibold">Please read the following rules carefully before borrowing:</h6>
            <ul class="text-muted small">
              <li>You may borrow up to <strong>3 books</strong> at a time.</li>
              <li>The standard borrowing period is <strong>14 days</strong>.</li>
              <li>A fine of RM1.00 per day will be charged for late returns.</li>
              <li>Books must be returned in good condition.</li>
            </ul>
          </div>

          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" id="agreeTerms" v-model="agreedToTerms">
            <label class="form-check-label" for="agreeTerms">
              I have read and agree to the <strong>Terms &amp; Conditions</strong>.
            </label>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" @click="closeModal">Cancel</button>
          <button
            type="button"
            class="btn btn-pink px-4"
            :disabled="!agreedToTerms"
            @click="proceedToConfirmation"
          >
            Continue to Borrow
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const router = useRouter()
const authStore = useAuthStore()

const books = ref([])
const borrowedBookIds = ref(new Set())
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)
const pageSize = ref(12)

// Modal states
const showTermsModal = ref(false)
const agreedToTerms = ref(false)
const selectedBook = ref(null)

const categories = ['Drama', 'Horror', 'Fiction', 'Mystery', 'Romance', 'Science', 'Technology', 'History', 'Education']

const fetchBooks = async () => {
  try {
    loading.value = true
    const response = await api.get('/books.php')
    books.value = response.data || []
  } catch (error) {
    console.error('Failed to fetch books:', error)
    books.value = []
  }finally {
    loading.value = false
  }
}

const fetchUserBorrowedBooks = async () => {
  if (!authStore.user?.id) return
  try {
    const res = await api.get(`/bookings.php?action=get&user_id=${authStore.user.id}`)
    const borrowed = res.data?.borrowed_books || []
    // Store IDs of books that are currently borrowed (not returned)
    borrowedBookIds.value = new Set(
      borrowed
        .filter(b => ['borrowed', 'overdue'].includes((b.status || '').toLowerCase()))
        .map(b => b.book_id || b.id)   // adjust if your backend uses different field
    )
  } catch (error) {
    console.error('Failed to fetch user borrows:', error)
  }
}

const filteredBooks = computed(() => {
  return books.value
    .filter(book => {
      // NEW: Hide books the user has already borrowed
      if (borrowedBookIds.value.has(book.id)) {
        return false
      }
      const matchesSearch =
        book.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        book.author.toLowerCase().includes(searchQuery.value.toLowerCase())
      const matchesCategory = !selectedCategory.value || book.category === selectedCategory.value
      return matchesSearch && matchesCategory
    })
})

const paginatedBooks = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredBooks.value.slice(start, start + pageSize.value)
})

const totalPages = computed(() => {
  return Math.ceil(filteredBooks.value.length / pageSize.value) || 1
})

const goToPage = (page) => {
  currentPage.value = page
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

watch([searchQuery, selectedCategory], () => {
  currentPage.value = 1
})

// Open T&C Modal
const borrowBook = (book) => {
  selectedBook.value = book
  agreedToTerms.value = false
  showTermsModal.value = true
}

const closeModal = () => {
  showTermsModal.value = false
  agreedToTerms.value = false
  selectedBook.value = null
}

const proceedToConfirmation = () => {
  if (!agreedToTerms.value || !selectedBook.value) return

  showTermsModal.value = false
  agreedToTerms.value = false

  router.push({
    name: 'BookingConfirmation',
    query: {
      type: 'book',
      bookId: selectedBook.value.id,
      bookTitle: selectedBook.value.title
    }
  })
}

onMounted(async () => {
  await Promise.all([fetchBooks(), fetchUserBorrowedBooks()])
  loading.value = false
})
</script>

<style scoped>
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
  border-radius: 50px;
}
.btn-pink:hover {
  background-color: #D89CA1;
}

/* ==================== THEME-MATCHED PAGINATION ==================== */
.custom-pagination .page-link {
  color: #2C2C2C;
  background-color: #fff;
  border: 1px solid #E8B4B8;
  font-weight: 500;
  padding: 0.5rem 1rem;
  transition: all 0.2s ease;
}

.custom-pagination .page-item.active .page-link {
  background-color: #E8B4B8;
  border-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 700;
  box-shadow: 0 2px 6px rgba(232, 180, 184, 0.3);
}

.custom-pagination .page-link:hover {
  background-color: #F8E8EA;
  border-color: #D89CA1;
  color: #2C2C2C;
}

.custom-pagination .page-item.disabled .page-link {
  background-color: #f8f4f0;
  border-color: #E8B4B8;
  color: #aaa;
  cursor: not-allowed;
}

/* Make pagination buttons slightly rounded like your theme */
.custom-pagination .page-link {
  border-radius: 30px !important;
  margin: 0 4px;
}
</style>
