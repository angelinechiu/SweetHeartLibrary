<template>
  <div class="homepage" style="background-color: #F8F4F0;">

    <!-- ==================== HERO BANNER ==================== -->
    <div style="background-color: #2C2C2C; color: #F8F4F0;" class="py-5">
      <div class="container text-center py-4">
        <div class="mb-4">
          <img src="../assets/images/logo.png" alt="Sweetheart Library Logo" style="width: 110px; height: 110px;" />
        </div>

        <h1 class="display-3 fw-bold mb-3">
          Welcome to <span style="color: #E8B4B8;">Sweetheart Library</span>
        </h1>
        <p class="lead fs-5 mb-4" style="color: #D9CFC2; max-width: 620px; margin: 0 auto;">
          Your elegant sanctuary for knowledge, study, and quiet moments
        </p>

        <!-- Search Bar -->
        <div class="row justify-content-center mb-4">
          <div class="col-md-8 col-lg-6">
            <div class="input-group input-group-lg shadow-sm" style="border-radius: 50px; overflow: hidden;">
              <input
                v-model="searchQuery"
                @keyup.enter="performSearch"
                type="text"
                class="form-control border-0 ps-4"
                placeholder="Search books by title, author or ISBN..."
                style="background-color: #F8F4F0; color: #2C2C2C;"
              >
              <button @click="performSearch" class="btn btn-pink px-4" style="border-radius: 0 50px 50px 0;">
                <i class="bi bi-search"></i> Search
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Access Buttons -->
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <router-link to="/books" class="btn btn-lg px-4 py-2 fw-semibold" style="background-color: #E8B4B8; color: #2C2C2C; border-radius: 50px;">
            Browse Catalog
          </router-link>
          <router-link to="/rooms" class="btn btn-lg px-4 py-2 fw-semibold btn-outline-light" style="border-radius: 50px; color: #F8F4F0; border-color: #E8B4B8;">
            Book a Study Room
          </router-link>
          <router-link to="/dashboard" class="btn btn-lg px-4 py-2 fw-semibold btn-outline-light" style="border-radius: 50px; color: #F8F4F0; border-color: #E8B4B8;">
            My Dashboard
          </router-link>
        </div>
      </div>
    </div>

    <!-- ==================== RECENTLY ADDED BOOKS ==================== -->
    <div class="py-5">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0" style="color: #2C2C2C;">Recently Added Books</h2>
          <router-link to="/books" class="text-decoration-none fw-semibold" style="color: #E8B4B8;">View All →</router-link>
        </div>

        <div class="row g-4">
          <div class="col-6 col-md-4 col-lg-3" v-for="book in recentlyAddedBooks" :key="book.id">
            <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2; border-radius: 16px; overflow: hidden;">
              <img :src="book.cover_image || 'https://picsum.photos/id/1015/400/300'" class="card-img-top" style="height: 220px; object-fit: cover;" alt="Book cover">
              <div class="card-body d-flex flex-column p-3">
                <h6 class="fw-semibold mb-1 text-truncate" style="color: #2C2C2C;">{{ book.title }}</h6>
                <p class="text-muted small mb-2">{{ book.author }}</p>
                <div class="mt-auto">
                  <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-pink w-100">View Details</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== POPULAR BOOKS ==================== -->
    <div class="py-5" style="background-color: #fff;">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0" style="color: #2C2C2C;">Popular This Week</h2>
          <router-link to="/books" class="text-decoration-none fw-semibold" style="color: #E8B4B8;">Explore More →</router-link>
        </div>

        <div class="row g-4">
          <div class="col-6 col-md-4 col-lg-3" v-for="book in popularBooks" :key="book.id">
            <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2; border-radius: 16px; overflow: hidden;">
              <img :src="book.cover_image || 'https://picsum.photos/id/106/400/300'" class="card-img-top" style="height: 220px; object-fit: cover;" alt="Book cover">
              <div class="card-body d-flex flex-column p-3">
                <h6 class="fw-semibold mb-1 text-truncate" style="color: #2C2C2C;">{{ book.title }}</h6>
                <p class="text-muted small mb-2">{{ book.author }}</p>
                <div class="mt-auto">
                  <router-link :to="`/books/${book.id}`" class="btn btn-sm btn-pink w-100">View Details</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== FEATURED ROOMS ==================== -->
    <div class="py-5">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0" style="color: #2C2C2C;">Featured Study Rooms</h2>
          <router-link to="/rooms" class="text-decoration-none fw-semibold" style="color: #E8B4B8;">See All Rooms →</router-link>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-4" v-for="room in featuredRooms" :key="room.id">
            <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2; border-radius: 16px; overflow: hidden;">
              <img :src="room.image || 'https://picsum.photos/id/1018/600/300'" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Room image">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h5 class="fw-semibold mb-0" style="color: #2C2C2C;">{{ room.name }}</h5>
                  <span class="badge bg-success">Featured</span>
                </div>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> {{ room.location }} • Capacity: {{ room.capacity }}</p>

                <div class="mb-3">
                  <span v-for="facility in room.facilities" :key="facility" class="badge me-1 mb-1" style="background-color: #E8B4B8; color: #2C2C2C; font-size: 0.75rem;">
                    {{ facility }}
                  </span>
                </div>

                <div class="d-flex gap-2 mt-3">
                  <router-link :to="`/rooms/${room.id}`" class="btn btn-sm btn-outline-dark flex-fill">View Details</router-link>
                  <button @click="bookRoom(room)" class="btn btn-sm btn-pink flex-fill">Book Room</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== BOOK CATEGORIES ==================== -->
    <div class="py-5" style="background-color: #fff;">
      <div class="container">
        <h2 class="fw-bold text-center mb-5" style="color: #2C2C2C;">Explore by Category</h2>
        <div class="row g-3 justify-content-center">
          <div class="col-6 col-md-4 col-lg-2" v-for="category in categories" :key="category.name">
            <router-link :to="`/books?category=${category.name}`" class="text-decoration-none">
              <div class="card border-0 shadow-sm text-center p-4 hover-card h-100" style="background-color: #D9CFC2; border-radius: 16px;">
                <div class="mb-3" style="font-size: 2.2rem;">{{ category.icon }}</div>
                <h6 class="fw-semibold mb-0" style="color: #2C2C2C;">{{ category.name }}</h6>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')

// Sample data - Replace with real API calls
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
    image: "https://picsum.photos/id/1018/600/300",
    facilities: ["WiFi", "Projector", "Whiteboard", "AC"]
  },
  {
    id: 2,
    name: "The Garden Room",
    location: "Ground Floor, West Wing",
    capacity: 8,
    image: "https://picsum.photos/id/160/600/300",
    facilities: ["WiFi", "TV", "Power Sockets", "AC"]
  },
  {
    id: 3,
    name: "The Library Nook",
    location: "3rd Floor, Quiet Zone",
    capacity: 4,
    image: "https://picsum.photos/id/201/600/300",
    facilities: ["WiFi", "Whiteboard", "Natural Light"]
  }
])

const categories = ref([
  { name: "Drama", icon: "🎭" },
  { name: "Horror", icon: "👻" },
  { name: "Fiction", icon: "📖" },
  { name: "Mystery", icon: "🔍" },
  { name: "Romance", icon: "💕" },
  { name: "Science", icon: "🔬" },
  { name: "Technology", icon: "💻" },
  { name: "History", icon: "🏛️" },
  { name: "Education", icon: "📚" }
])

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/books', query: { search: searchQuery.value.trim() } })
  } else {
    router.push('/books')
  }
}

const bookRoom = (room) => {
  router.push({ path: '/booking-form', query: { roomId: room.id, roomName: room.name } })
}
</script>

<style scoped>
.hover-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-card:hover {
  transform: translateY(-10px);
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
