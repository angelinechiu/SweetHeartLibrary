<template>
  <div>
    <!-- Hero Section -->
    <div style="background-color: #2C2C2C; color: #F8F4F0;" class="py-5">
      <div class="container text-center py-5">
        <h1 class="display-1 fw-bold mb-3">Sweetheart <span style="color: #E8B4B8;">Library</span></h1>
        <p class="lead fs-3 mb-5" style="color: #D9CFC2;">Where Timeless Elegance Meets Knowledge</p>
        <router-link to="/books" class="btn btn-pink btn-lg px-5 py-3 fs-5">Explore Collection</router-link>
      </div>
    </div>

    <!-- Featured Books from Database -->
    <div style="background-color: #F8F4F0;" class="py-5">
      <div class="container">
        <h2 class="text-center mb-5" style="color: #2C2C2C;">Featured Books</h2>

        <LoadingSpinner :loading="loading" message="Loading featured books..." />

        <div v-if="!loading" class="row g-4">
          <div class="col-md-4" v-for="book in featuredBooks" :key="book.id">
            <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2;">
              <img
                :src="book.cover_image || 'https://picsum.photos/id/201/400/300'"
                class="card-img-top"
                style="height: 300px; object-fit: cover;"
              >
              <div class="card-body">
                <h5 style="color: #2C2C2C;">{{ book.title }}</h5>
                <p class="text-muted">{{ book.author }}</p>
                <router-link :to="`/books/${book.id}`" class="btn btn-pink mt-2">View Details</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const featuredBooks = ref([])
const loading = ref(true)

const loadFeaturedBooks = async () => {
  loading.value = true
  try {
    const res = await api.get('/books.php')
    // Show first 3 books as featured
    featuredBooks.value = res.data.slice(0, 3)
  } catch (error) {
    console.error('Failed to load featured books:', error)
  } finally {
    loading.value = false
  }
}

onMounted(loadFeaturedBooks)
</script>
