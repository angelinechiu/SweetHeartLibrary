<template>
  <div class="book-detail-page">
    <div class="container py-5">

      
      <router-link to="/books" class="text-decoration-none text-muted mb-4 d-inline-block">
        ← Back to Books
      </router-link>

      
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-pink" role="status"></div>
        <p class="mt-3 text-muted">Loading book details...</p>
      </div>

      
      <div v-else-if="book" class="row">

        
        <div class="col-md-4 mb-4">
          <div class="book-emoji-box w-100">
            <span class="book-emoji">📖</span>
            <div class="mt-3">
              <span
                class="badge px-3 py-2 fs-6"
                :class="book.available_copies > 0 ? 'bg-success' : 'bg-secondary'"
              >
                {{ book.available_copies > 0 ? 'Available' : 'Out of Stock' }}
              </span>
            </div>
          </div>
        </div>

        
        <div class="col-md-8">
          <h1 class="fw-bold mb-2">{{ book.title }}</h1>
          <h4 class="text-muted mb-4">{{ book.author }}</h4>

          
          <div class="row g-3 mb-4">
            <div class="col-6 col-md-4" v-if="book.isbn">
              <div class="info-box">
                <div class="text-muted small">ISBN</div>
                <div class="fw-semibold">{{ book.isbn }}</div>
              </div>
            </div>
            <div class="col-6 col-md-4" v-if="book.category">
              <div class="info-box">
                <div class="text-muted small">Category</div>
                <div class="fw-semibold">{{ book.category }}</div>
              </div>
            </div>
            <div class="col-6 col-md-4" v-if="book.year">
              <div class="info-box">
                <div class="text-muted small">Published Year</div>
                <div class="fw-semibold">{{ book.year }}</div>
              </div>
            </div>
            <div class="col-6 col-md-4">
              <div class="info-box">
                <div class="text-muted small">Total Copies</div>
                <div class="fw-semibold">{{ book.total_copies || 0 }}</div>
              </div>
            </div>
          </div>

          
          <div class="mb-4">
            <h5 class="fw-bold mb-2">Description</h5>
            <p class="lead text-muted" style="line-height: 1.7;">
              {{ book.description || 'No description available.' }}
            </p>
          </div>

          
          <div class="d-flex flex-column gap-2 mt-4">

            
            <button
              v-if="book.available_copies > 0"
              class="btn btn-pink px-5 py-2"
              @click="showBorrowModal"
            >
              Borrow this Book
            </button>

            <button
              v-else
              class="btn btn-secondary px-5 py-2"
              disabled
            >
              Out of Stock
            </button>

            
            <div v-if="book.available_copies > 0" class="text-success small">
              {{ book.available_copies }} / {{ book.total_copies }} copies available
            </div>
            <div v-else class="text-danger small">
              This book is currently not available for borrowing.
            </div>

          </div>

        </div>
      </div>

      
      <div v-else class="text-center py-5">
        <p class="text-muted">Book not found.</p>
        <router-link to="/books" class="btn btn-pink">Back to Catalog</router-link>
      </div>

      
      <div class="mt-5 pt-4" v-if="recommendedBooks.length > 0">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="fw-bold">Books You May Like</h3>
          <router-link to="/books" class="section-link">See more →</router-link>
        </div>

        <div class="row g-4">
          <div
            class="col-6 col-md-3"
            v-for="recommended in recommendedBooks"
            :key="recommended.id"
          >
            <div class="recommended-card" @click="goToBook(recommended.id)">
              <div class="recommended-icon">📖</div>
              <h6 class="mt-3 mb-1">{{ recommended.title }}</h6>
              <p class="text-muted small mb-0">{{ recommended.author }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  
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
            <input
              class="form-check-input"
              type="checkbox"
              id="agreeTerms"
              v-model="agreedToTerms"
            >
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
import { useRoute, useRouter } from 'vue-router'
import { ref, watch, onMounted } from 'vue'
import api from '../services/api.js'

const route = useRoute()
const router = useRouter()

const book = ref(null)
const recommendedBooks = ref([])
const loading = ref(true)
const allBooks = ref([])


const showTermsModal = ref(false)
const agreedToTerms = ref(false)

const fetchBooks = async () => {
  try {
    loading.value = true
    const response = await api.get('/books.php')
    allBooks.value = response.data || []
  } catch (error) {
    console.error('Failed to fetch books:', error)
    allBooks.value = []
  } finally {
    loading.value = false
  }
}

const loadBookDetails = () => {
  const bookId = Number(route.params.id)
  book.value = allBooks.value.find(b => b.id == bookId)

  if (book.value) {
    recommendedBooks.value = allBooks.value
      .filter(b => b.id != bookId)
      .sort(() => 0.5 - Math.random())
      .slice(0, 4)
  }
}

const goToBook = (id) => {
  router.push(`/books/${id}`)
}


const showBorrowModal = () => {
  agreedToTerms.value = false
  showTermsModal.value = true
}

const closeModal = () => {
  showTermsModal.value = false
  agreedToTerms.value = false
}

const proceedToConfirmation = () => {
  if (!agreedToTerms.value || !book.value) return
  closeModal()

  router.push({
    name: 'BookingConfirmation',
    query: {
      type: 'book',
      bookId: book.value.id,
      bookTitle: book.value.title
    }
  })
}

onMounted(async () => {
  await fetchBooks()
  loadBookDetails()
})

watch(() => route.params.id, async () => {
  if (allBooks.value.length === 0) await fetchBooks()
  loadBookDetails()
})
</script>

<style scoped>
.book-emoji-box {
  background-color: #F8E7E9;
  border-radius: 20px;
  padding: 40px 20px;
  text-align: center;
}

.book-emoji {
  font-size: 6rem;
}

.info-box {
  background-color: #fff;
  border: 1px solid #f0e6d9;
  border-radius: 12px;
  padding: 12px 16px;
}

.recommended-card {
  background-color: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  border: 1px solid #f0e6d9;
}

.recommended-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.recommended-icon {
  font-size: 2.2rem;
}

.section-link {
  color: #E8B4B8;
  text-decoration: none;
  font-weight: 600;
}

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
</style>
