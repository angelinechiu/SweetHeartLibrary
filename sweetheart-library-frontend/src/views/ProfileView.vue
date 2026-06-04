<template>
  <div class="profile-page py-5" style="background-color: #F8F4F0;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          
          <!-- Header -->
          <div class="text-center mb-5">
            <h1 class="fw-bold mb-2" style="color: #2C2C2C;">My Profile</h1>
            <p class="text-muted">Manage your account and view your library activity</p>
          </div>

          <LoadingSpinner :loading="loading" />

          <div v-if="!loading" class="row g-4">
            
            <!-- Left Column: Avatar + Personal Info -->
            <div class="col-lg-5">
              <!-- Avatar Card -->
              <div class="card border-0 shadow-sm mb-4" style="background-color: #D9CFC2; border-radius: 20px;">
                <div class="card-body text-center p-5">
                  <div class="mx-auto mb-4 position-relative" style="width: 140px; height: 140px;">
                    <div class="rounded-circle overflow-hidden border border-4 border-white shadow" 
                         style="width: 140px; height: 140px; background-color: #E8B4B8;">
                      <img v-if="user.avatar" :src="user.avatar" class="w-100 h-100" style="object-fit: cover;">
                      <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center" style="font-size: 4rem; color: #2C2C2C;">
                        👤
                      </div>
                    </div>
                  </div>
                  
                  <h3 class="fw-bold mb-1" style="color: #2C2C2C;">{{ user.name }}</h3>
                  <p class="text-muted mb-0">{{ user.email }}</p>
                  <div class="mt-3">
                    <span class="badge px-3 py-2" style="background-color: #E8B4B8; color: #2C2C2C;">
                      {{ user.role === 'admin' ? 'Administrator' : 'Member' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Personal Information Card -->
              <div class="card border-0 shadow-sm" style="background-color: #fff; border-radius: 20px;">
                <div class="card-body p-4">
                  <h5 class="fw-semibold mb-4" style="color: #2C2C2C;">Personal Information</h5>
                  
                  <form @submit.prevent="updateProfile">
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Full Name</label>
                      <input v-model="form.name" type="text" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-4">
                      <label class="form-label fw-semibold">Email Address</label>
                      <input v-model="form.email" type="email" class="form-control form-control-lg" required>
                    </div>
                    <button type="submit" class="btn btn-pink w-100 py-2 fw-semibold" :disabled="loading">
                      Save Changes
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Right Column: Statistics + Activities -->
            <div class="col-lg-7">
              
              <!-- Borrowing Statistics -->
              <div class="card border-0 shadow-sm mb-4" style="background-color: #fff; border-radius: 20px;">
                <div class="card-body p-4">
                  <h5 class="fw-semibold mb-4" style="color: #2C2C2C;">
                    <i class="bi bi-book me-2"></i> Borrowing Statistics
                  </h5>
                  
                  <div class="row g-3 text-center">
                    <div class="col-4">
                      <div class="p-3 bg-light rounded-3">
                        <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ stats.totalBorrowed }}</div>
                        <div class="small text-muted">Total Borrowed</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="p-3 bg-light rounded-3">
                        <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ stats.currentlyBorrowed }}</div>
                        <div class="small text-muted">Currently Borrowed</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="p-3 bg-light rounded-3">
                        <div class="fs-2 fw-bold text-danger">{{ stats.overdue }}</div>
                        <div class="small text-muted">Overdue</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Room Booking Statistics -->
              <div class="card border-0 shadow-sm mb-4" style="background-color: #fff; border-radius: 20px;">
                <div class="card-body p-4">
                  <h5 class="fw-semibold mb-4" style="color: #2C2C2C;">
                    <i class="bi bi-door-open me-2"></i> Room Booking Statistics
                  </h5>
                  
                  <div class="row g-3 text-center">
                    <div class="col-4">
                      <div class="p-3 bg-light rounded-3">
                        <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ stats.totalRoomBookings }}</div>
                        <div class="small text-muted">Total Bookings</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="p-3 bg-light rounded-3">
                        <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ stats.upcomingRooms }}</div>
                        <div class="small text-muted">Upcoming</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="p-3 bg-light rounded-3">
                        <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ stats.completedRooms }}</div>
                        <div class="small text-muted">Completed</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recent Activities -->
              <div class="card border-0 shadow-sm" style="background-color: #fff; border-radius: 20px;">
                <div class="card-body p-4">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0" style="color: #2C2C2C;">Recent Activities</h5>
                    <router-link to="/history" class="small text-decoration-none" style="color: #E8B4B8;">View All →</router-link>
                  </div>
                  
                  <div v-if="recentActivities.length > 0">
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom" v-for="activity in recentActivities" :key="activity.id">
                      <div class="me-3 mt-1">
                        <span class="badge" :class="activity.type === 'book' ? 'bg-pink text-dark' : 'bg-info text-white'">
                          {{ activity.type === 'book' ? '📖' : '🚪' }}
                        </span>
                      </div>
                      <div class="flex-grow-1">
                        <div class="fw-semibold" style="color: #2C2C2C;">{{ activity.title }}</div>
                        <div class="small text-muted">{{ activity.date }} • {{ activity.status }}</div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-muted text-center py-3">
                    No recent activity yet.
                  </div>
                </div>
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
import { useAuthStore } from '../stores/auth'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const authStore = useAuthStore()

const user = ref({})
const form = ref({ name: '', email: '' })
const loading = ref(true)

const stats = ref({
  totalBorrowed: 12,
  currentlyBorrowed: 2,
  overdue: 1,
  totalRoomBookings: 8,
  upcomingRooms: 1,
  completedRooms: 6
})

const recentActivities = ref([
  { id: 1, type: 'book', title: 'Borrowed "The Silent Patient"', date: 'June 1, 2026', status: 'Due June 15' },
  { id: 2, type: 'room', title: 'Booked "The Rose Study"', date: 'May 28, 2026', status: 'Completed' },
  { id: 3, type: 'book', title: 'Renewed "Educated"', date: 'May 25, 2026', status: 'Due June 8' }
])

const loadProfile = () => {
  if (authStore.user) {
    user.value = { ...authStore.user }
    form.value.name = user.value.name
    form.value.email = user.value.email
  }
  loading.value = false
}

const updateProfile = async () => {
  loading.value = true
  try {
    authStore.user.name = form.value.name
    authStore.user.email = form.value.email
    localStorage.setItem('user', JSON.stringify(authStore.user))
    user.value = { ...authStore.user }
    alert('Profile updated successfully!')
  } catch (error) {
    console.error('Profile update failed:', error)
    alert('Failed to update profile')
  } finally {
    loading.value = false
  }
}

onMounted(loadProfile)
</script>

<style scoped>
.profile-page {
  min-height: 80vh;
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
.bg-pink {
  background-color: #E8B4B8 !important;
}
</style>