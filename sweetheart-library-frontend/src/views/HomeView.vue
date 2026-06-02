<template>
  <div>
    <!-- ==================== HERO SECTION (Smaller & Elegant) ==================== -->
    <div style="background-color: #2C2C2C; color: #F8F4F0;" class="py-4">
      <div class="container text-center py-4">

        <!-- Logo -->
        <div class="mb-3">
          <img
            src="../assets/images/logo.png"
            alt="Sweetheart Library Logo"
            style="width: 90px; height: 90px;"
          >
        </div>

        <h1 class="display-4 fw-bold mb-2">
          Sweetheart <span style="color: #E8B4B8;">Library</span>
        </h1>

        <p class="lead fs-5 mb-4" style="color: #D9CFC2; max-width: 520px; margin: 0 auto;">
          Where timeless elegance meets knowledge
        </p>

        <!-- Explore Collection Button -->
        <router-link
          to="/books"
          class="btn btn-lg px-4 py-2 fs-6 fw-semibold"
          style="
            background-color: #E8B4B8;
            color: #2C2C2C;
            border: none;
            border-radius: 50px;
            box-shadow: 0 6px 20px rgba(232, 180, 184, 0.4);
            transition: all 0.3s ease;
            text-decoration: none;
          "
        >
          Explore Collection
        </router-link>
      </div>
    </div>

    <!-- ==================== FEATURED BOOKS ==================== -->
    <div style="background-color: #F8F4F0;" class="py-5">
      <div class="container">
        <h2 class="text-center mb-5" style="color: #2C2C2C; font-weight: 700;">
          Featured Books
        </h2>

        <LoadingSpinner :loading="loading" message="Loading featured books..." />

        <div v-if="!loading" class="row g-4">
          <div
            class="col-md-4"
            v-for="book in featuredBooks"
            :key="book.id"
          >
            <div
              class="card border-0 shadow-sm hover-card h-100"
              style="background-color: #D9CFC2; border-radius: 16px; overflow: hidden;"
            >
              <img
                :src="book.cover_image || 'https://picsum.photos/id/201/400/300'"
                class="card-img-top"
                style="height: 260px; object-fit: cover;"
                alt="Book cover"
              >
              <div class="card-body d-flex flex-column">
                <h5 class="mb-1" style="color: #2C2C2C; font-weight: 600;">
                  {{ book.title }}
                </h5>
                <p class="text-muted mb-3">{{ book.author }}</p>

                <div class="mt-auto">
                  <router-link
                    :to="`/books/${book.id}`"
                    class="btn btn-pink w-100"
                  >
                    View Details
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!loading && featuredBooks.length === 0" class="text-center py-5">
          <p class="text-muted">No featured books available at the moment.</p>
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
    featuredBooks.value = res.data.slice(0, 3)
  } catch (error) {
    console.error('Failed to load featured books:', error)
  } finally {
    loading.value = false
  }
}

onMounted(loadFeaturedBooks)
</script>

<style scoped>
/* Button hover effect */
a[style*="background-color: #E8B4B8"]:hover {
  background-color: #D89CA1 !important;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(232, 180, 184, 0.5) !important;
}
</style>
