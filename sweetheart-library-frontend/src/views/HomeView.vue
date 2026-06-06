<template>
  <div class="homepage">

    <!-- ==================== HERO ==================== -->
    <div class="hero">
      <div class="container text-center">
        <img src="../assets/images/logo.png" alt="Sweetheart Library" class="hero-logo mb-4" />
        <h1 class="hero-title">
          Welcome to <span class="brand-accent">Sweetheart Library</span>
        </h1>
        <p class="hero-subtitle">
          A refined space for readers, thinkers, and quiet moments.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
          <router-link to="/books" class="btn btn-lg btn-pink px-5 py-3">Browse Books</router-link>
        </div>
      </div>
    </div>

    <!-- ==================== FEATURED BOOKS (Horizontal Scroll) ==================== -->
    <div class="section">
      <div class="container">
        <div class="section-header">
          <h2>Featured Books</h2>
          <router-link to="/books" class="section-link">Browse all books →</router-link>
        </div>

        <div v-if="loadingBooks" class="text-center py-4">
          <div class="spinner-border text-pink"></div>
        </div>

        <div v-else class="horizontal-scroll">
          <div class="book-card" v-for="book in featuredBooks" :key="book.id">
            <div class="card-icon pink">📖</div>
            <div class="card-content">
              <h6 class="card-title">{{ book.title }}</h6>
              <p class="card-subtitle">{{ book.author }}</p>
              <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-pink mt-2 w-100">
                View Details
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== POPULAR BOOKS (Horizontal Scroll) ==================== -->
    <div class="section">
      <div class="container">
        <div class="section-header">
          <h2>Popular Books</h2>
          <router-link to="/books" class="section-link">See more →</router-link>
        </div>

        <div v-if="loadingBooks" class="text-center py-4">
          <div class="spinner-border text-pink"></div>
        </div>

        <div v-else class="horizontal-scroll">
          <div class="book-card" v-for="book in popularBooks" :key="book.id">
            <div class="card-icon pink">📖</div>
            <div class="card-content">
              <h6 class="card-title">{{ book.title }}</h6>
              <p class="card-subtitle">{{ book.author }}</p>
              <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-pink mt-2 w-100">
                View Details
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== FEATURED STUDY ROOMS ==================== -->
    <div class="section">
      <div class="container">
        <div class="section-header">
          <h2>Featured Study Rooms</h2>
          <router-link to="/rooms" class="section-link">See all rooms →</router-link>
        </div>

        <div v-if="loadingRooms" class="text-center py-4">
          <div class="spinner-border text-pink"></div>
        </div>

        <div v-else class="row g-4">
          <div class="col-12 col-md-4" v-for="room in featuredRooms" :key="room.id">
            <div class="room-card-clean">
              <div class="room-icon-box">
                <span class="room-icon">🛋️</span>
              </div>
              <div class="room-info">
                <h6 class="room-name">{{ room.name }}</h6>
                <p class="room-details">{{ room.location || 'Main Building' }} • {{ room.capacity || 0 }} seats</p>
              </div>
              <router-link to="/rooms" class="btn-room">View Room</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== WHY SWEETHEART LIBRARY ==================== -->
    <div class="section">
      <div class="container">
        <div class="text-center mb-5">
          <h2>Why Sweetheart Library?</h2>
          <p class="text-muted mt-2">Designed for focus, comfort, and inspiration.</p>
        </div>

        <div class="row g-4">
          <div class="col-md-4">
            <div class="benefit-card">
              <div class="benefit-icon">📚</div>
              <h5>Curated Collection</h5>
              <p>Thoughtfully selected books across literature, knowledge, and inspiration.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="benefit-card">
              <div class="benefit-icon">🛋️</div>
              <h5>Beautiful Study Spaces</h5>
              <p>Quiet, elegant rooms designed for deep focus and productive work.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="benefit-card">
              <div class="benefit-icon">✨</div>
              <h5>Seamless Experience</h5>
              <p>Easy booking, clear availability, and a calm, welcoming atmosphere.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== FINAL CTA ==================== -->
    <div class="cta-section">
      <div class="container text-center">
        <h2 class="cta-title">Ready to begin your journey?</h2>
        <p class="cta-subtitle">Join Sweetheart Library today and find your perfect space.</p>
        <router-link to="/register" class="btn btn-lg btn-pink px-5 py-3 mt-2">
          Become a Member
        </router-link>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const featuredBooks = ref([])
const popularBooks = ref([])
const featuredRooms = ref([])

const loadingBooks = ref(true)
const loadingRooms = ref(true)

const fetchBooks = async () => {
  loadingBooks.value = true
  try {
    const res = await api.get('/books.php')
    const allBooks = res.data || []

    featuredBooks.value = allBooks.filter(book => book.is_featured == 1)
    popularBooks.value = allBooks.filter(book => book.is_popular == 1)

    // Fallback if no books are marked
    if (featuredBooks.value.length === 0 && allBooks.length > 0) {
      featuredBooks.value = allBooks.slice(0, 8)
    }
    if (popularBooks.value.length === 0 && allBooks.length > 0) {
      popularBooks.value = allBooks.slice(0, 8)
    }
  } catch (error) {
    console.error('Failed to fetch books:', error)
  } finally {
    loadingBooks.value = false
  }
}

const fetchRooms = async () => {
  loadingRooms.value = true
  try {
    const res = await api.get('/rooms.php')
    const allRooms = res.data || []
    featuredRooms.value = allRooms.slice(0, 3)
  } catch (error) {
    console.error('Failed to fetch rooms:', error)
  } finally {
    loadingRooms.value = false
  }
}

onMounted(() => {
  fetchBooks()
  fetchRooms()
})
</script>

<style scoped>
/* ==================== HERO ==================== */
.hero {
  background-color: #2C2C2C;
  color: #F8F4F0;
  padding: 80px 0 80px;
}
.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  letter-spacing: -2px;
}
.hero-logo {
  width: 120px;
  height: 120px;
}
.brand-accent {
  color: #E8B4B8;
}
.hero-subtitle {
  font-size: 1.25rem;
  color: #D9CFC2;
  max-width: 560px;
  margin: 20px auto 0;
}

/* ==================== SECTIONS ==================== */
.section {
  padding: 30px 0;
}
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}
.section-header h2 {
  font-size: 1.9rem;
  font-weight: 700;
  color: #2C2C2C;
}
.section-link {
  color: #E8B4B8;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.95rem;
}

/* ==================== HORIZONTAL SCROLL ==================== */
.horizontal-scroll {
  display: flex;
  gap: 20px;
  overflow-x: auto;
  padding-bottom: 20px;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

.horizontal-scroll::-webkit-scrollbar {
  height: 6px;
}
.horizontal-scroll::-webkit-scrollbar-thumb {
  background-color: #E8B4B8;
  border-radius: 10px;
}

/* ==================== BOOK CARD ==================== */
.book-card {
  min-width: 220px;
  max-width: 220px;
  background-color: #ffffff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  border: 1px solid #f0e6d9;
  transition: transform 0.3s ease;
  flex-shrink: 0;
}
.book-card:hover {
  transform: translateY(-8px);
}
.card-icon {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  margin-bottom: 16px;
}
.card-icon.pink {
  background-color: #F8E7E9;
}
.card-title {
  font-weight: 700;
  color: #2C2C2C;
  font-size: 1.05rem;
  margin-bottom: 6px;
}
.card-subtitle {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 16px;
}

/* ==================== ROOM CARDS ==================== */
.room-card-clean {
  background-color: #ffffff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  border: 1px solid #f0e6d9;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.room-card-clean:hover {
  transform: translateY(-6px);
}
.room-icon-box {
  width: 48px;
  height: 48px;
  background-color: #E8F0E8;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
}
.room-icon {
  font-size: 1.6rem;
}
.room-info {
  flex-grow: 1;
}
.room-name {
  font-weight: 700;
  color: #2C2C2C;
  font-size: 1.1rem;
}
.room-details {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 18px;
}
.btn-room {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  text-decoration: none;
  padding: 8px 24px;
  border-radius: 50px;
  font-size: 0.9rem;
  text-align: center;
}

/* ==================== BENEFIT CARDS ==================== */
.benefit-card {
  background-color: #fff;
  border-radius: 16px;
  padding: 32px 24px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  border: 1px solid #f0e6d9;
}
.benefit-icon {
  font-size: 2.5rem;
  margin-bottom: 16px;
}
.benefit-card h5 {
  font-weight: 700;
  color: #2C2C2C;
  margin-bottom: 12px;
}
.benefit-card p {
  color: #666;
  font-size: 0.95rem;
  margin: 0;
}

/* ==================== CTA ==================== */
.cta-section {
  background-color: #2C2C2C;
  color: #F8F4F0;
  padding: 90px 0;
}
.cta-title {
  font-size: 2.3rem;
  font-weight: 700;
  margin-bottom: 12px;
}
.cta-subtitle {
  color: #D9CFC2;
  max-width: 520px;
  margin: 0 auto 24px;
}

/* ==================== BUTTONS ==================== */
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
  border-radius: 50px;
}
.btn-pink:hover {
  background-color: #D89CA1;
  transform: translateY(-2px);
}
</style>
