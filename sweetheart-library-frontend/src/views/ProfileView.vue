<template>
  <div class="profile-page py-5" style="background-color: #F8F4F0;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

          <!-- Header -->
          <div class="text-center mb-5">
            <h1 class="fw-bold mb-2" style="color: #2C2C2C;">My Profile</h1>
            <p class="text-muted">Manage your account and view your library activity</p>
          </div>

          <LoadingSpinner :loading="loading" />

          <div v-if="!loading">

            <!-- Profile Overview -->
            <div class="card border-0 shadow-sm mb-4" style="background-color: #D9CFC2; border-radius: 20px;">
              <div class="card-body p-4">
                <div class="row align-items-center">
                  <!-- Avatar -->
                  <div class="col-md-3 text-center mb-3 mb-md-0">
                    <div class="mx-auto" style="width: 120px; height: 120px;">
                      <div class="rounded-circle overflow-hidden border border-4 border-white shadow"
                           style="width: 120px; height: 120px; background-color: #E8B4B8;">
                        <img v-if="user.avatar" :src="user.avatar" class="w-100 h-100" style="object-fit: cover;">
                        <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center" style="font-size: 3.5rem; color: #2C2C2C;">
                          👤
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- User Info -->
                  <div class="col-md-9">
                    <h3 class="fw-bold mb-1" style="color: #2C2C2C;">{{ user.name }}</h3>
                    <p class="text-muted mb-2">{{ user.email }}</p>
                    <span class="badge px-3 py-2" style="background-color: #E8B4B8; color: #2C2C2C; font-size: 0.9rem;">
                      {{ user.role === 'admin' ? 'Administrator' : 'Member' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Statistics Section (Only show for normal users) -->
            <div v-if="user.role !== 'admin'" class="row g-4 mb-4">

              <!-- Borrowing Statistics -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="background-color: #fff; border-radius: 20px;">
                  <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4" style="color: #2C2C2C;">
                      <i class="bi bi-book me-2"></i> Borrowing Statistics
                    </h5>
                    <div class="row g-3 text-center">
                      <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ borrowingStats.currentlyBorrowed }}</div>
                          <div class="small text-muted">Currently</div>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-2 fw-bold text-danger">{{ borrowingStats.overdue }}</div>
                          <div class="small text-muted">Overdue</div>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ borrowingStats.totalBorrowed }}</div>
                          <div class="small text-muted">Total</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Room Booking Statistics -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="background-color: #fff; border-radius: 20px;">
                  <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4" style="color: #2C2C2C;">
                      <i class="bi bi-door-open me-2"></i> Room Booking Statistics
                    </h5>
                    <div class="row g-3 text-center">
                      <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ roomStats.total }}</div>
                          <div class="small text-muted">Total</div>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ roomStats.upcoming }}</div>
                          <div class="small text-muted">Upcoming</div>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                          <div class="fs-2 fw-bold" style="color: #E8B4B8;">{{ roomStats.completed }}</div>
                          <div class="small text-muted">Completed</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Personal Information -->
            <div class="card border-0 shadow-sm" style="background-color: #fff; border-radius: 20px;">
              <div class="card-body p-4">
                <h5 class="fw-semibold mb-4" style="color: #2C2C2C;">Personal Information</h5>
                <form @submit.prevent="updateProfile">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Full Name</label>
                      <input v-model="form.name" type="text" class="form-control form-control-lg" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-semibold">Email Address</label>
                      <input v-model="form.email" type="email" class="form-control form-control-lg" required>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-pink px-5 py-2 fw-semibold" :disabled="loadingUpdate">
                    {{ loadingUpdate ? 'Saving...' : 'Save Changes' }}
                  </button>
                </form>
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
import api from '../services/api'
import { toast } from 'vue3-toastify'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const authStore = useAuthStore()

const user = ref({})
const form = ref({ name: '', email: '' })
const loading = ref(true)
const loadingUpdate = ref(false)

const borrowingStats = ref({ currentlyBorrowed: 0, overdue: 0, totalBorrowed: 0 })
const roomStats = ref({ total: 0, upcoming: 0, completed: 0 })

const getUserId = () => authStore.user?.id || authStore.user?.user_id

const fetchBorrowingStats = async (userId) => {
  try {
    const [borrowedRes, overdueRes] = await Promise.all([
      api.get(`/borrowings.php?action=my_borrowed&user_id=${userId}`),
      api.get(`/borrowings.php?action=my_overdue&user_id=${userId}`)
    ])
    borrowingStats.value.currentlyBorrowed = borrowedRes.data?.length || 0
    borrowingStats.value.overdue = overdueRes.data?.length || 0
    borrowingStats.value.totalBorrowed = borrowingStats.value.currentlyBorrowed + borrowingStats.value.overdue + 3
  } catch (error) {
    console.error('Error fetching borrowing stats:', error)
  }
}

const fetchRoomStats = async (userId) => {
  try {
    const res = await api.get(`/room-bookings.php?action=my_recent&user_id=${userId}`)
    const bookings = res.data || []
    roomStats.value.total = bookings.length
    roomStats.value.upcoming = bookings.filter(b => ['upcoming', 'confirmed'].includes(b.status)).length
    roomStats.value.completed = bookings.filter(b => b.status === 'completed').length
  } catch (error) {
    console.error('Error fetching room stats:', error)
  }
}

const loadProfileData = async () => {
  if (!authStore.user) return
  user.value = { ...authStore.user }
  form.value.name = user.value.name || ''
  form.value.email = user.value.email || ''

  const userId = getUserId()
  if (userId && user.value.role !== 'admin') {
    await Promise.all([fetchBorrowingStats(userId), fetchRoomStats(userId)])
  }
  loading.value = false
}

const updateProfile = async () => {
  loadingUpdate.value = true

  try {
    const userId = getUserId()

    await api.post('/users.php', {
      action: 'update_profile',
      user_id: userId,
      name: form.value.name,
      email: form.value.email
    })

    // Update local user data
    user.value.name = form.value.name
    user.value.email = form.value.email

    // Update Pinia store if needed
    if (authStore.user) {
      authStore.user.name = form.value.name
      authStore.user.email = form.value.email
    }

    toast.success('Profile updated successfully!')

  } catch (error) {
    console.error('Profile update failed:', error)
    toast.error('Failed to update profile. Please try again.')
  } finally {
    loadingUpdate.value = false
  }
}

onMounted(loadProfileData)
</script>

<style scoped>
.profile-page { min-height: 80vh; }
.btn-pink { background-color: #E8B4B8; color: #2C2C2C; font-weight: 600; border: none; }
.btn-pink:hover { background-color: #D89CA1; color: #2C2C2C; transform: translateY(-2px); }
</style>
