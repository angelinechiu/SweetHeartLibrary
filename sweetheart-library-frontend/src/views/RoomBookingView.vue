<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">

      <!-- Header -->
      <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #2C2C2C;">Sweetheart Library Map</h2>
        <p class="text-muted">Level 2 & Level 3 • Study Rooms • <strong>8:00 AM – 10:00 PM</strong></p>
        <div class="alert alert-warning py-2 px-3 d-inline-block mt-2 small">
          Each user can only book a maximum of <strong>2 hours per day</strong>.
        </div>
      </div>

      <!-- Library Map -->
      <div class="card border-0 shadow-sm p-4 mb-5" style="background-color: #fff;">
        <div class="row">

          <!-- Level 2 -->
          <div class="col-md-6 mb-4">
            <h5 class="fw-semibold mb-3 text-center">Level 2</h5>
            <div class="border rounded-3 p-3" style="background-color: #fdfaf5;">
              <div class="row g-3">
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border" style="background-color: #E8B4B8; color: #2C2C2C;">
                    <strong>The Rose Study</strong><br>
                    <small>4 seats • WiFi, Whiteboard, AC</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border" style="background-color: #D4E6C3; color: #2C2C2C;">
                    <strong>The Garden Room</strong><br>
                    <small>6 seats • Projector, Printer, AC</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border" style="background-color: #F5B7B1; color: #2C2C2C;">
                    <strong>The Silent Room</strong><br>
                    <small>2 seats • WiFi, Power Outlets</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border" style="background-color: #AED6F1; color: #2C2C2C;">
                    <strong>The Focus Pod</strong><br>
                    <small>3 seats • Charging Station, AC</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Level 3 -->
          <div class="col-md-6 mb-4">
            <h5 class="fw-semibold mb-3 text-center">Level 3 (Quiet Zone)</h5>
            <div class="border rounded-3 p-3" style="background-color: #fdfaf5;">
              <div class="row g-3">
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border border-warning" style="background-color: #FFF3CD;">
                    <strong>Quiet Corner</strong><br>
                    <small class="text-danger">★ Recommended</small><br>
                    <small>2 seats • Noise-Cancelling, Desk Lamp</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border" style="background-color: #D6EAF8;">
                    <strong>The Study Pod</strong><br>
                    <small>3 seats • Charging Station, AC</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded-3 text-center border" style="background-color: #FADBD8; color: #2C2C2C;">
                    <strong>The Creative Corner</strong><br>
                    <small>4 seats • WiFi, Whiteboard, Projector</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Available Rooms -->
      <h5 class="fw-semibold mb-3">Available Rooms</h5>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-pink" role="status"></div>
        <p class="mt-2 text-muted">Loading available rooms...</p>
      </div>

      <div v-else class="row g-3">
        <div
          v-for="room in availableRooms"
          :key="room.id"
          class="col-md-6 col-lg-4"
        >
          <div class="card border-0 shadow-sm h-100" :class="{ 'border-warning border-2': room.recommended }">
            <div class="card-body">
              <!-- Header -->
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-info text-white px-3 py-1">{{ room.capacity }} seats</span>
                <span v-if="room.recommended" class="badge bg-warning text-dark px-3 py-1">Recommended</span>
                <span class="badge bg-success text-white">Available</span>
              </div>

              <!-- Room Info -->
              <h5 class="fw-semibold mb-1">{{ room.name }}</h5>
              <p class="text-muted small mb-2">{{ room.location }}</p>

              <!-- Facilities -->
              <div class="mb-3">
                <strong class="small d-block mb-1">Facilities:</strong>
                <div class="d-flex flex-wrap gap-1">
                  <span
                    v-for="facility in getFacilities(room)"
                    :key="facility"
                    class="badge bg-light text-dark small"
                  >
                    {{ facility.trim() }}
                  </span>
                </div>
              </div>

              <!-- Book Button -->
              <button class="btn btn-pink w-100" @click="selectRoom(room)">
                Book This Room
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="availableRooms.length === 0 && !loading" class="col-12">
          <div class="text-center py-5 bg-white rounded-3 shadow-sm">
            <i class="bi bi-door-closed fs-1 text-muted d-block mb-2"></i>
            <p class="text-muted mb-0">No available rooms at the moment.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api.js'

const router = useRouter()
const allRooms = ref([])
const loading = ref(true)

// Fetch rooms from database
const fetchRooms = async () => {
  loading.value = true
  try {
    const res = await api.get('/rooms.php')
    allRooms.value = res.data || []
  } catch (error) {
    console.error('Failed to fetch rooms:', error)
    allRooms.value = []
  } finally {
    loading.value = false
  }
}

// Get facilities as array (handles both string and array)
const getFacilities = (room) => {
  if (!room.facilities) return []
  if (Array.isArray(room.facilities)) return room.facilities
  return room.facilities.split(',')
}

// Only show available rooms
const availableRooms = computed(() => {
  return allRooms.value.filter(room => {
    if (room.available === undefined || room.available === null) return true
    return room.available === true || room.available === 1 || room.available === '1'
  })
})

const selectRoom = (room) => {
  router.push({
    path: '/booking-form',
    query: { roomId: room.id, roomName: room.name }
  })
}

onMounted(fetchRooms)
</script>
