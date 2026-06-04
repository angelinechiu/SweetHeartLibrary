<template>
  <div class="homepage">

    <!-- ==================== ELEGANT HERO ==================== -->
    <div class="hero">
      <div class="container text-center">
        <img
          src="../assets/images/logo.png"
          alt="Sweetheart Library"
          class="hero-logo mb-4"
        />

        <h1 class="hero-title">
          Welcome to <span class="brand-accent">Sweetheart Library</span>
        </h1>

        <p class="hero-subtitle">
          A refined space for readers, thinkers, and quiet moments.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
          <router-link to="/books" class="btn btn-lg btn-pink px-5 py-3">
            Browse the Collection
          </router-link>
          <router-link to="/rooms" class="btn btn-lg btn-outline-pink px-5 py-3">
            Reserve a Study Room
          </router-link>
        </div>
      </div>
    </div>

    <!-- ==================== RECENTLY ADDED ==================== -->
    <div class="section">
      <div class="container">
        <div class="section-header">
          <h2>Recently Added</h2>
          <router-link to="/books" class="section-link">View all →</router-link>
        </div>

        <div class="row g-4">
          <div class="col-6 col-md-4 col-lg-3" v-for="book in recentlyAddedBooks" :key="book.id">
            <div class="book-card">
              <div class="book-image-wrapper">
                <img :src="book.cover_image" class="book-image" alt="Book cover">
              </div>
              <div class="book-content">
                <h6 class="book-title">{{ book.title }}</h6>
                <p class="book-author">{{ book.author }}</p>
                <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-pink w-100 mt-2">
                  View Details
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== POPULAR THIS WEEK ==================== -->
    <div class="section section-alt">
      <div class="container">
        <div class="section-header">
          <h2>Popular This Week</h2>
          <router-link to="/books" class="section-link">Explore more →</router-link>
        </div>

        <div class="row g-4">
          <div class="col-6 col-md-4 col-lg-3" v-for="book in popularBooks" :key="book.id">
            <div class="book-card">
              <div class="book-image-wrapper">
                <img :src="book.cover_image" class="book-image" alt="Book cover">
              </div>
              <div class="book-content">
                <h6 class="book-title">{{ book.title }}</h6>
                <p class="book-author">{{ book.author }}</p>
                <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-pink w-100 mt-2">
                  View Details
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== FEATURED STUDY ROOMS (Simplified) ==================== -->
    <div class="section">
      <div class="container">
        <div class="section-header">
          <h2>Featured Study Rooms</h2>
          <router-link to="/rooms" class="section-link">See all rooms →</router-link>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-4" v-for="room in featuredRooms" :key="room.id">
            <div class="room-card">
              <div class="room-image-wrapper">
                <img :src="room.image" class="room-image" alt="Study room">
              </div>
              <div class="room-content">
                <h5 class="room-name">{{ room.name }}</h5>
                <p class="room-meta">
                  {{ room.location }} • {{ room.capacity }} seats
                </p>

                <router-link
                  to="/rooms"
                  class="btn btn-pink w-100 mt-3"
                >
                  View
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== ELEGANT CLOSING CTA ==================== -->
    <div class="cta-section">
      <div class="container text-center">
        <h2 class="cta-title">Your next chapter awaits.</h2>
        <p class="cta-subtitle">Join Sweetheart Library and enjoy a space designed for focus and inspiration.</p>

        <router-link to="/register" class="btn btn-lg btn-pink px-5 py-3 mt-3">
          Become a Member
        </router-link>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'

const recentlyAddedBooks = ref([
  { id: 1, title: "The Silent Patient", author: "Alex Michaelides", cover_image: "https://picsum.photos/id/1015/400/300" },
  { id: 2, title: "Educated", author: "Tara Westover", cover_image: "https://picsum.photos/id/106/400/300" },
  { id: 3, title: "The Midnight Library", author: "Matt Haig", cover_image: "https://picsum.photos/id/201/400/300" },
  { id: 4, title: "Atomic Habits", author: "James Clear", cover_image: "https://picsum.photos/id/160/400/300" }
])

const popularBooks = ref([
  { id: 5, title: "Dune", author: "Frank Herbert", cover_image: "https://picsum.photos/id/251/400/300" },
  { id: 6, title: "The Seven Husbands of Evelyn Hugo", author: "Taylor Jenkins Reid", cover_image: "https://picsum.photos/id/1005/400/300" },
  { id: 7, title: "Project Hail Mary", author: "Andy Weir", cover_image: "https://picsum.photos/id/29/400/300" },
  { id: 8, title: "The Alchemist", author: "Paulo Coelho", cover_image: "https://picsum.photos/id/29/400/300" }
])

const featuredRooms = ref([
  {
    id: 1,
    name: "The Rose Study",
    location: "2nd Floor, East Wing",
    capacity: 6,
    image: "https://picsum.photos/id/1018/600/300"
  },
  {
    id: 2,
    name: "The Garden Room",
    location: "Ground Floor, West Wing",
    capacity: 8,
    image: "https://picsum.photos/id/160/600/300"
  },
  {
    id: 3,
    name: "The Library Nook",
    location: "3rd Floor, Quiet Zone",
    capacity: 4,
    image: "https://picsum.photos/id/201/600/300"
  }
])
</script>

<style scoped>
/* ==================== HERO ==================== */
.hero {
  background-color: #2C2C2C;
  color: #F8F4F0;
  padding: 100px 0 80px;
}

.hero-logo {
  width: 120px;
  height: 120px;
}

.hero-title {
  font-size: 3.2rem;
  font-weight: 700;
  color: #F8F4F0;
  margin-bottom: 16px;
  letter-spacing: -1.5px;
}

.brand-accent {
  color: #E8B4B8;
}

.hero-subtitle {
  font-size: 1.15rem;
  color: #D9CFC2;
  max-width: 520px;
  margin: 0 auto;
}

/* ==================== SECTIONS ==================== */
.section {
  padding: 70px 0;
}

.section-alt {
  background-color: #fff;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 32px;
}

.section-header h2 {
  font-size: 1.85rem;
  font-weight: 700;
  color: #2C2C2C;
  margin: 0;
}

.section-link {
  color: #E8B4B8;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  transition: color 0.2s ease;
}

.section-link:hover {
  color: #D89CA1;
}

/* ==================== BOOK CARDS ==================== */
.book-card {
  background-color: #D9CFC2;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
              box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
}

.book-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 30px rgba(0, 0, 0, 0.12);
}

.book-image-wrapper {
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 210px;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.book-card:hover .book-image {
  transform: scale(1.07);
}

.book-content {
  padding: 20px;
}

.book-title {
  font-weight: 700;
  color: #2C2C2C;
  margin-bottom: 4px;
  font-size: 1rem;
}

.book-author {
  color: #5C5C5C;
  font-size: 0.9rem;
  margin-bottom: 12px;
}

/* ==================== ROOM CARDS (Simplified & Beautiful) ==================== */
.room-card {
  background-color: #D9CFC2;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
              box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
}

.room-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 30px rgba(0, 0, 0, 0.12);
}

.room-image-wrapper {
  position: relative;
  overflow: hidden;
}

.room-image {
  width: 100%;
  height: 190px;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.room-card:hover .room-image {
  transform: scale(1.06);
}

.room-content {
  padding: 24px;
}

.room-name {
  font-weight: 700;
  color: #2C2C2C;
  margin-bottom: 8px;
}

.room-meta {
  color: #5C5C5C;
  font-size: 0.9rem;
  margin-bottom: 16px;
}

/* ==================== BUTTONS ==================== */
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn-pink:hover {
  background-color: #D89CA1;
  color: #2C2C2C;
  transform: translateY(-2px);
}

.btn-outline-pink {
  background-color: transparent;
  color: #F8F4F0;
  border: 2px solid #E8B4B8;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn-outline-pink:hover {
  background-color: #E8B4B8;
  color: #2C2C2C;
  border-color: #E8B4B8;
}

/* ==================== CTA SECTION ==================== */
.cta-section {
  background-color: #2C2C2C;
  color: #F8F4F0;
  padding: 80px 0;
}

.cta-title {
  font-size: 2.1rem;
  font-weight: 700;
  margin-bottom: 12px;
}

.cta-subtitle {
  color: #D9CFC2;
  max-width: 480px;
  margin: 0 auto 24px;
}
</style>
