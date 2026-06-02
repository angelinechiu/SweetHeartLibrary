<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4" style="color: #2C2C2C;">Our Elegant Collection</h2>

      <!-- Search Bar -->
      <div class="row g-2 mb-4">
        <div class="col-md-5">
          <input
            v-model="searchQuery"
            class="form-control form-control-lg"
            placeholder="Search by Title or Author"
          >
        </div>
        <div class="col-md-2">
          <input
            v-model="searchYear"
            type="number"
            class="form-control form-control-lg"
            placeholder="Year"
          >
        </div>
        <div class="col-md-2">
          <button class="btn btn-pink btn-lg w-100" @click="resetFilters">Clear</button>
        </div>
      </div>

      <!-- Loading -->
      <LoadingSpinner :loading="loading" message="Loading books..." />

      <!-- Book Grid -->
      <div v-if="!loading" class="row g-4">
        <div class="col-md-4 col-lg-3" v-for="book in paginatedBooks" :key="book.id">
          <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2;">
            <img
              :src="book.cover_image || 'https://picsum.photos/id/201/400/300'"
              class="card-img-top"
              style="height: 260px; object-fit: cover;"
            >
            <div class="card-body d-flex flex-column">
              <h5 style="color: #2C2C2C;">{{ book.title }}</h5>
              <p class="text-muted mb-1">{{ book.author }}</p>
              <p class="text-muted small mb-3" v-if="book.year">Year: {{ book.year }}</p>

              <router-link :to="`/books/${book.id}`" class="btn btn-pink mt-auto">View Details</router-link>
            </div>
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
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const books = ref([])
const loading = ref(true)
const searchQuery = ref('')
const searchYear = ref('')
const currentPage = ref(1)
const pageSize = 8

// Load books from API
const loadBooks = async () => {
  loading.value = true
  try {
    const res = await api.get('/books.php')
    books.value = res.data
  } catch (error) {
    console.error('Failed to load books:', error)
  } finally {
    loading.value = false
  }
}

// Filtered books based on search
const filteredBooks = computed(() => {
  return books.value.filter(book => {
    const matchesSearch =
      book.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      book.author?.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesYear = !searchYear.value || book.year == searchYear.value

    return matchesSearch && matchesYear
  })
})

// Pagination
const totalPages = computed(() => {
  return Math.ceil(filteredBooks.value.length / pageSize)
})

const paginatedBooks = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filteredBooks.value.slice(start, start + pageSize)
})

// Reset filters
const resetFilters = () => {
  searchQuery.value = ''
  searchYear.value = ''
  currentPage.value = 1
}

onMounted(() => {
  loadBooks()
})
</script>
