<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #2C2C2C;">Our Elegant Collection</h2>
        <span class="text-muted">{{ filteredBooks.length }} books</span>
      </div>

      <!-- Search + Filters -->
      <div class="row g-3 mb-4">
        <div class="col-md-5">
          <input
            v-model="searchQuery"
            class="form-control form-control-lg"
            placeholder="Search by title, author or ISBN..."
          >
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

      <!-- Loading -->
      <LoadingSpinner :loading="loading" message="Loading beautiful books..." />

      <!-- Book Grid -->
      <div v-if="!loading" class="row g-4">
        <div class="col-6 col-md-4 col-lg-3" v-for="book in paginatedBooks" :key="book.id">
          <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2; border-radius: 16px; overflow: hidden;">
            <div class="position-relative">
              <img
                :src="book.cover_image || 'https://picsum.photos/id/201/400/300'"
                class="card-img-top"
                style="height: 240px; object-fit: cover;"
                alt="Book cover"
              >
              <span 
                class="position-absolute top-0 end-0 m-2 badge"
                :class="book.available_copies > 0 ? 'bg-success' : 'bg-secondary'"
              >
                {{ book.available_copies > 0 ? 'Available' : 'Unavailable' }}
              </span>
            </div>
            
            <div class="card-body d-flex flex-column p-3">
              <h6 class="fw-semibold mb-1 text-truncate" style="color: #2C2C2C;">{{ book.title }}</h6>
              <p class="text-muted small mb-2">{{ book.author }}</p>
              
              <div class="mb-2">
                <span class="badge" style="background-color: #E8B4B8; color: #2C2C2C; font-size: 0.75rem;">{{ book.category }}</span>
              </div>

              <div class="mt-auto">
                <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-outline-dark w-100 mb-2">View Details</router-link>
                
                <button 
                  v-if="book.available_copies > 0"
                  class="btn btn-sm btn-pink w-100"
                  @click="borrowBook(book)">
                  Borrow This Book
                </button>
                <button v-else class="btn btn-sm btn-secondary w-100" disabled>
                  Currently Unavailable
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && filteredBooks.length === 0" class="text-center py-5">
        <p class="text-muted">No books found matching your filters.</p>
      </div>

      <!-- Pagination -->
      <nav class="mt-5" v-if="totalPages > 1">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="currentPage--">Previous</button>
          </li>
          <li class="page-item">
            <span class="page-link">Page {{ currentPage }} of {{ totalPages }}</span>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button class="page-link" @click="currentPage++">Next</button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const router = useRouter()

const books = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)
const pageSize = 12

const categories = [
  'Drama', 'Horror', 'Fiction', 'Mystery', 'Romance', 
  'Science', 'Technology', 'History', 'Education'
]

// Load books
const loadBooks = async () => {
  loading.value = true
  try {
    const res = await api.get('/books.php')
    books.value = res.data || []
  } catch (error) {
    console.error('Failed to load books:', error)
    // Fallback mock data
    books.value = [
      { id: 1, title: "The Silent Patient", author: "Alex Michaelides", category: "Mystery", year: 2019, available_copies: 3, cover_image: "https://picsum.photos/id/1015/400/300" },
      { id: 2, title: "Educated", author: "Tara Westover", category: "Education", year: 2018, available_copies: 5, cover_image: "https://picsum.photos/id/106/400/300" },
      { id: 3, title: "Dune", author: "Frank Herbert", category: "Science Fiction", year: 1965, available_copies: 0, cover_image: "https://picsum.photos/id/251/400/300" }
    ]
  } finally {
    loading.value = false
  }
}

// Filtered books
const filteredBooks = computed(() => {
  return books.value.filter(book => {
    const matchesSearch = 
      !searchQuery.value || 
      book.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      book.author?.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesCategory = !selectedCategory.value || book.category === selectedCategory.value

    return matchesSearch && matchesCategory
  })
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredBooks.value.length / pageSize))

const paginatedBooks = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filteredBooks.value.slice(start, start + pageSize)
})

// Reset filters
const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

// Borrow book action
const borrowBook = (book) => {
  router.push({ path: `/books/${book.id}`, query: { action: 'borrow' } })
}

onMounted(() => {
  loadBooks()
})
</script>

<style scoped>
.hover-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-card:hover {
  transform: translateY(-8px);
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
</style>