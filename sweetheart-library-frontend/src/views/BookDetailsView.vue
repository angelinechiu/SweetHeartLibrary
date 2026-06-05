<template>
  <div class="book-detail-page">
    <div class="container py-5">

      <!-- Back Button -->
      <router-link to="/books" class="text-decoration-none text-muted mb-4 d-inline-block">
        ← Back to Books
      </router-link>

      <div v-if="book" class="row align-items-center">

        <!-- Book Emoji (Smaller) -->
        <div class="col-md-4 mb-4 text-center">
          <div class="book-emoji-box">
            <span class="book-emoji">📖</span>
          </div>
        </div>

        <!-- Book Details -->
        <div class="col-md-8">
          <h1 class="fw-bold mb-2">{{ book.title }}</h1>
          <h4 class="text-muted mb-4">{{ book.author }}</h4>

          <div class="mb-3">
            <span class="badge bg-success px-3 py-2 me-2">Available</span>
            <span class="text-muted">Published 2023</span>
          </div>

          <p class="lead">
            A captivating story that explores deep themes of life, choice, and human nature.
            This book will stay with you long after you finish reading it.
          </p>

          <div class="d-flex gap-3 mt-4">
            <button class="btn btn-pink px-5 py-2">Borrow this Book</button>
            <button class="btn btn-outline-secondary px-4 py-2">Add to Wishlist</button>
          </div>
        </div>
      </div>

      <!-- ==================== BOOKS YOU MAY LIKE ==================== -->
      <div class="mt-5 pt-4">
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
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { ref, watch } from 'vue'

const route = useRoute()
const router = useRouter()

const allBooks = [
  { id: 1, title: "The Silent Patient", author: "Alex Michaelides" },
  { id: 2, title: "Educated", author: "Tara Westover" },
  { id: 3, title: "The Midnight Library", author: "Matt Haig" },
  { id: 4, title: "Atomic Habits", author: "James Clear" },
  { id: 5, title: "Dune", author: "Frank Herbert" },
  { id: 6, title: "The Seven Husbands of Evelyn Hugo", author: "Taylor Jenkins Reid" },
  { id: 7, title: "Project Hail Mary", author: "Andy Weir" },
  { id: 8, title: "The Alchemist", author: "Paulo Coelho" }
]

const book = ref(null)
const recommendedBooks = ref([])

const loadBook = () => {
  const bookId = Number(route.params.id)
  book.value = allBooks.find(b => b.id === bookId)

  // Get 4 random recommended books (excluding current book)
  recommendedBooks.value = allBooks
    .filter(b => b.id !== bookId)
    .sort(() => 0.5 - Math.random())
    .slice(0, 4)
}

const goToBook = (id) => {
  router.push(`/books/${id}`)
}

loadBook()

watch(() => route.params.id, () => {
  loadBook()
})
</script>

<style scoped>
/* ==================== SMALLER BOOK EMOJI ==================== */
.book-emoji-box {
  background-color: #F8E7E9;
  border-radius: 20px;
  padding: 30px;
  display: inline-flex;
  justify-content: center;
  align-items: center;
}

.book-emoji {
  font-size: 5rem;           /* ← Made smaller */
  line-height: 1;
}

/* ==================== RECOMMENDED BOOKS ==================== */
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
