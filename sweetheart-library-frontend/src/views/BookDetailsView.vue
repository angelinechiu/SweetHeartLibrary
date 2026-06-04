<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <LoadingSpinner :loading="loading" message="Loading book details..." />

      <div v-if="!loading && book">
        <div class="row g-5">
          <!-- Large Book Cover -->
          <div class="col-lg-5">
            <div class="position-sticky" style="top: 20px;">
              <img 
                :src="book.cover_image || 'https://picsum.photos/id/201/700/900'" 
                class="img-fluid rounded-4 shadow-lg w-100" 
                style="max-height: 620px; object-fit: cover; border: 8px solid #D9CFC2;"
                alt="Book cover"
              >
            </div>
          </div>

          <!-- Book Information -->
          <div class="col-lg-7">
            <div class="mb-4">
              <span class="badge px-3 py-2 mb-3" style="background-color: #E8B4B8; color: #2C2C2C; font-size: 0.95rem;">
                {{ book.category }}
              </span>
              
              <h1 class="fw-bold display-5 mb-2" style="color: #2C2C2C;">{{ book.title }}</h1>
              <h4 class="text-muted mb-3">by {{ book.author }}</h4>
            </div>

            <!-- Key Details -->
            <div class="row g-3 mb-4">
              <div class="col-sm-6" v-if="book.isbn">
                <div class="p-3 bg-white rounded-3 shadow-sm">
                  <small class="text-muted d-block">ISBN</small>
                  <strong style="color: #2C2C2C;">{{ book.isbn }}</strong>
                </div>
              </div>
              <div class="col-sm-6" v-if="book.year">
                <div class="p-3 bg-white rounded-3 shadow-sm">
                  <small class="text-muted d-block">Publication Year</small>
                  <strong style="color: #2C2C2C;">{{ book.year }}</strong>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="p-3 bg-white rounded-3 shadow-sm">
                  <small class="text-muted d-block">Availability</small>
                  <span :class="book.available_copies > 0 ? 'text-success fw-bold' : 'text-danger fw-bold'">
                    {{ book.available_copies > 0 ? 'Available' : 'Currently Unavailable' }}
                  </span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="p-3 bg-white rounded-3 shadow-sm">
                  <small class="text-muted d-block">Available Copies</small>
                  <strong style="color: #2C2C2C; font-size: 1.3rem;">{{ book.available_copies || 0 }}</strong>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <h5 class="fw-semibold mb-3" style="color: #2C2C2C;">Description</h5>
              <p class="lead" style="color: #444; line-height: 1.7;">{{ book.description || 'No description available for this title.' }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-3 mt-4">
              <button 
                v-if="book.available_copies > 0"
                class="btn btn-pink btn-lg px-5 py-3 fw-semibold"
                @click="showBorrowModal = true">
                Borrow This Book
              </button>
              
              <button v-else class="btn btn-secondary btn-lg px-5 py-3" disabled>
                Currently Unavailable
              </button>

              <router-link to="/books" class="btn btn-outline-dark btn-lg px-4 py-3">
                Back to Catalog
              </router-link>
            </div>

            <div class="mt-3 text-muted small">
              Maximum 3 books per user per week • 2-week borrowing period • Renewal extends by 7 days
            </div>
          </div>
        </div>

        <!-- Related Books Section -->
        <div class="mt-5 pt-5 border-top">
          <h3 class="fw-bold mb-4" style="color: #2C2C2C;">You May Also Like</h3>
          
          <div class="row g-4">
            <div class="col-6 col-md-3" v-for="related in relatedBooks" :key="related.id">
              <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2; border-radius: 12px; overflow: hidden;">
                <img :src="related.cover_image || 'https://picsum.photos/id/106/400/300'" class="card-img-top" style="height: 180px; object-fit: cover;">
                <div class="card-body p-3">
                  <h6 class="fw-semibold text-truncate mb-1" style="color: #2C2C2C;">{{ related.title }}</h6>
                  <p class="text-muted small mb-2">{{ related.author }}</p>
                  <router-link :to="`/books/${related.id}`" class="btn btn-sm btn-pink w-100">View Details</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!loading && !book" class="text-center py-5">
        <h3>Book not found</h3>
        <router-link to="/books" class="btn btn-pink mt-3">Back to Catalog</router-link>
      </div>
    </div>

    <!-- Borrow Terms & Conditions Modal -->
    <div v-if="showBorrowModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none;">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Book Borrowing Terms & Conditions</h5>
            <button type="button" class="btn-close" @click="showBorrowModal = false"></button>
          </div>
          <div class="modal-body pt-2">
            <div class="p-3 bg-light rounded-3 mb-4">
              <h6 class="fw-semibold">Please read and accept the following rules:</h6>
              <ul class="mb-0 ps-3">
                <li>Books must be returned on time.</li>
                <li>Late returns will incur penalties.</li>
                <li>Maximum <strong>3 books</strong> can be borrowed per user per week.</li>
                <li>One book can only be borrowed for <strong>2 weeks</strong>.</li>
                <li>Renewal extends borrowing by <strong>7 days</strong> only.</li>
                <li>Users cannot cancel confirmed borrowings or mark books as returned (Admin only).</li>
              </ul>
            </div>
            
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" v-model="termsAccepted" id="termsCheck">
              <label class="form-check-label" for="termsCheck">
                I have read and agree to the borrowing terms and conditions.
              </label>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary px-4" @click="showBorrowModal = false">Cancel</button>
            <button 
              type="button" 
              class="btn btn-pink px-5" 
              :disabled="!termsAccepted"
              @click="confirmBorrowing">
              Accept & Continue to Confirmation
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const route = useRoute()
const router = useRouter()

const book = ref(null)
const loading = ref(true)
const showBorrowModal = ref(false)
const termsAccepted = ref(false)

const relatedBooks = ref([])

// Load book details
const loadBook = async () => {
  loading.value = true
  try {
    const res = await api.get('/books.php')
    const foundBook = res.data?.find(b => b.id == route.params.id)
    book.value = foundBook || null

    // Load related books (same category, exclude current)
    if (book.value) {
      relatedBooks.value = (res.data || [])
        .filter(b => b.category === book.value.category && b.id !== book.value.id)
        .slice(0, 4)
    }
  } catch (error) {
    console.error('Failed to load book:', error)
  } finally {
    loading.value = false
  }
}

// Confirm borrowing flow
const confirmBorrowing = () => {
  showBorrowModal.value = false
  termsAccepted.value = false
  
  // Navigate to borrowing confirmation page (or handle via API)
  router.push({
    path: '/booking-confirmation',
    query: { 
      type: 'book', 
      bookId: book.value.id, 
      bookTitle: book.value.title 
    }
  })
}

onMounted(loadBook)
</script>

<style scoped>
.hover-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 25px -5px rgb(0 0 0 / 0.1) !important;
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
</style>