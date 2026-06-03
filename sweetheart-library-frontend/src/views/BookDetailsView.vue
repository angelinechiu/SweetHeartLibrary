<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <LoadingSpinner :loading="loading" message="Loading book details..." />

      <div v-if="!loading && book" class="row">
        <div class="col-md-5">
          <img :src="book.cover_image || 'https://picsum.photos/id/201/700/900'"
               class="img-fluid rounded-4 shadow" alt="">
        </div>
        <div class="col-md-7">
          <h1 class="fw-bold" style="color: #2C2C2C;">{{ book.title }}</h1>
          <h5 class="text-muted">{{ book.author }} <span v-if="book.year">({{ book.year }})</span></h5>
          <p class="mt-4 lead">{{ book.description }}</p>

          <button @click="reserveBook" class="btn btn-pink btn-lg mt-4 px-5">
            Reserve This Book
          </button>
        </div>
      </div>

      <div v-if="!loading && !book" class="text-center">
        <h3>Book not found</h3>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const route = useRoute()
const router = useRouter()
const book = ref(null)
const loading = ref(true)

const loadBook = async () => {
  loading.value = true
  try {
    const res = await api.get('/books.php')
    const foundBook = res.data.find(b => b.id == route.params.id)
    book.value = foundBook
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const reserveBook = () => {
  router.push({
    path: '/booking-form',
    query: { type: 'book', bookId: book.value.id, bookTitle: book.value.title }
  })
}

onMounted(loadBook)
</script>

<style scoped>
.page-wrapper {
  padding: 10px 0;
}

.page-header h1 {
  font-size: 2.1rem;
  letter-spacing: 0.3px;
}

@media (max-width: 576px) {
  .page-header h1 {
    font-size: 1.7rem;
  }
}
</style>
