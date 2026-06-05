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

      <!-- Book Grid - Now matches My Bookings card layout -->
      <div v-if="!loading" class="row g-3">
        <div
          v-for="book in paginatedBooks"
          :key="book.id"
          class="col-md-6 col-lg-4"
        >
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
              <!-- Top badges row (matching MyBookings style) -->
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span 
                  class="badge px-3 py-1" 
                  style="background-color: #E8B4B8; color: #2C2C2C;"
                >
                  📖 {{ book.category }}
                </span>
                <span 
                  class="badge px-3 py-1"
                  :class="book.available_copies > 0 ? 'bg-success text-white' : 'bg-secondary text-white'"
                >
                  {{ book.available_copies > 0 ? 'Available' : 'Unavailable' }}
                </span>
              </div>

              <!-- Title -->
              <h5 class="fw-semibold mb-1">{{ book.title }}</h5>
              
              <!-- Author -->
              <p class="text-muted small mb-1">{{ book.author }}</p>

              <!-- Additional info -->
              <div class="small mb-3">
                <div class="text-muted">Published: {{ book.year || 'N/A' }}</div>
                <div class="text-muted">Copies available: <strong>{{ book.available_copies }}</strong></div>
              </div>

              <!-- Action buttons (pushed to bottom) -->
              <div class="mt-auto d-flex gap-2">
                <router-link 
                  :to="`/books/${book.id}`" 
                  class="btn btn-sm btn-outline-dark flex-fill"
                >
                  View Details
                </router-link>
                
                <button 
                  v-if="book.available_copies > 0"
                  class="btn btn-sm btn-pink flex-fill"
                  @click="borrowBook(book)"
                >
                  Borrow Book
                </button>
                <button 
                  v-else 
                  class="btn btn-sm btn-secondary flex-fill" 
                  disabled
                >
                  Currently Unavailable
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty state for no results -->
        <div v-if="filteredBooks.length === 0" class="col-12">
          <div class="text-center py-5 bg-white rounded-3 shadow-sm">
            <p class="text-muted mb-0">No books found matching your filters.</p>
          </div>
        </div>
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

// Rich sample data - 20+ books across all categories
const sampleBooks = [
  // Drama
  { id: 1, title: "The Silent Patient", author: "Alex Michaelides", category: "Drama", year: 2019, available_copies: 4, cover_image: "https://picsum.photos/id/1015/400/300", isbn: "978-1-250-30169-7", description: "A woman shoots her husband and then never speaks another word." },
  { id: 2, title: "Where the Crawdads Sing", author: "Delia Owens", category: "Drama", year: 2018, available_copies: 2, cover_image: "https://picsum.photos/id/106/400/300", isbn: "978-0-7352-1909-0" },
  
  // Horror
  { id: 3, title: "The Shining", author: "Stephen King", category: "Horror", year: 1977, available_copies: 3, cover_image: "https://picsum.photos/id/251/400/300", isbn: "978-0-385-12167-5" },
  { id: 4, title: "It", author: "Stephen King", category: "Horror", year: 1986, available_copies: 1, cover_image: "https://picsum.photos/id/160/400/300", isbn: "978-0-670-81302-5" },
  
  // Fiction
  { id: 5, title: "The Midnight Library", author: "Matt Haig", category: "Fiction", year: 2020, available_copies: 5, cover_image: "https://picsum.photos/id/201/400/300", isbn: "978-0-525-55947-4" },
  { id: 6, title: "The Alchemist", author: "Paulo Coelho", category: "Fiction", year: 1988, available_copies: 6, cover_image: "https://picsum.photos/id/29/400/300", isbn: "978-0-06-112241-5" },
  
  // Mystery
  { id: 7, title: "The Girl on the Train", author: "Paula Hawkins", category: "Mystery", year: 2015, available_copies: 2, cover_image: "https://picsum.photos/id/180/400/300", isbn: "978-1-59463-366-9" },
  { id: 8, title: "Gone Girl", author: "Gillian Flynn", category: "Mystery", year: 2012, available_copies: 3, cover_image: "https://picsum.photos/id/1005/400/300", isbn: "978-0-307-58836-4" },
  
  // Romance
  { id: 9, title: "The Seven Husbands of Evelyn Hugo", author: "Taylor Jenkins Reid", category: "Romance", year: 2017, available_copies: 4, cover_image: "https://picsum.photos/id/1009/400/300", isbn: "978-1-5011-3923-9" },
  { id: 10, title: "It Ends With Us", author: "Colleen Hoover", category: "Romance", year: 2016, available_copies: 5, cover_image: "https://picsum.photos/id/133/400/300", isbn: "978-1-5011-1036-8" },
  
  // Science
  { id: 11, title: "Dune", author: "Frank Herbert", category: "Science", year: 1965, available_copies: 2, cover_image: "https://picsum.photos/id/251/400/300", isbn: "978-0-441-17271-9" },
  { id: 12, title: "Project Hail Mary", author: "Andy Weir", category: "Science", year: 2021, available_copies: 3, cover_image: "https://picsum.photos/id/180/400/300", isbn: "978-0-593-13520-4" },
  
  // Technology
  { id: 13, title: "Atomic Habits", author: "James Clear", category: "Technology", year: 2018, available_copies: 7, cover_image: "https://picsum.photos/id/160/400/300", isbn: "978-0-7352-1129-2" },
  { id: 14, title: "The Lean Startup", author: "Eric Ries", category: "Technology", year: 2011, available_copies: 4, cover_image: "https://picsum.photos/id/201/400/300", isbn: "978-0-307-88789-4" },
  
  // History
  { id: 15, title: "Sapiens", author: "Yuval Noah Harari", category: "History", year: 2011, available_copies: 3, cover_image: "https://picsum.photos/id/106/400/300", isbn: "978-0-06-231609-7" },
  { id: 16, title: "Educated", author: "Tara Westover", category: "History", year: 2018, available_copies: 2, cover_image: "https://picsum.photos/id/29/400/300", isbn: "978-0-399-59050-4" },
  
  // Education
  { id: 17, title: "Thinking, Fast and Slow", author: "Daniel Kahneman", category: "Education", year: 2011, available_copies: 5, cover_image: "https://picsum.photos/id/180/400/300", isbn: "978-0-374-27563-1" },
  { id: 18, title: "How to Win Friends and Influence People", author: "Dale Carnegie", category: "Education", year: 1936, available_copies: 6, cover_image: "https://picsum.photos/id/133/400/300", isbn: "978-0-671-02703-2" },
  
  // More Drama & Fiction
  { id: 19, title: "Normal People", author: "Sally Rooney", category: "Drama", year: 2018, available_copies: 3, cover_image: "https://picsum.photos/id/1005/400/300", isbn: "978-1-984-82217-8" },
  { id: 20, title: "The Great Gatsby", author: "F. Scott Fitzgerald", category: "Fiction", year: 1925, available_copies: 4, cover_image: "https://picsum.photos/id/1015/400/300", isbn: "978-0-7432-7356-5" }
]

// Load books
const loadBooks = async () => {
  loading.value = true
  try {
    const res = await api.get('/books.php')
    books.value = res.data && res.data.length > 0 ? res.data : sampleBooks
  } catch (error) {
    console.error('Failed to load books:', error)
    books.value = sampleBooks
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