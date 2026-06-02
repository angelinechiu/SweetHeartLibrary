<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4" style="color: #2C2C2C;">Study Room Booking</h2>

      <!-- Operating Hours Notice -->
      <div class="alert alert-info mb-4">
        <strong>Operating Hours:</strong> Monday – Saturday, <strong>10:00 AM – 11:00 PM</strong><br>
        <small>Closed on Sundays. Bookings must be within operating hours.</small>
      </div>

      <!-- ==================== 2D FLOOR MAP ==================== -->
      <div class="card mb-5 shadow-sm">
        <div class="card-header" style="background-color: #2C2C2C; color: #F8F4F0;">
          <h5 class="mb-0">Library Floor Map - 2nd Floor</h5>
        </div>
        <div class="card-body p-4">
          <div class="floor-map-container" style="position: relative; width: 100%; max-width: 900px; margin: 0 auto; border: 3px solid #2C2C2C; border-radius: 12px; background-color: #f8f9fa; height: 520px;">

            <!-- Rooms -->
            <div class="room-box" style="top: 40px; left: 40px;" @click="bookRoom(rooms[0])">
              <div class="room-label">Rose Room<br><small>4 pax</small></div>
            </div>
            <div class="room-box" style="top: 40px; left: 230px;" @click="bookRoom(rooms[1])">
              <div class="room-label">Lily Room<br><small>6 pax</small></div>
            </div>
            <div class="room-box" style="top: 40px; left: 420px;" @click="bookRoom(rooms[2])">
              <div class="room-label">Orchid Room<br><small>8 pax</small></div>
            </div>
            <div class="room-box" style="top: 280px; left: 40px;" @click="bookRoom(rooms[3])">
              <div class="room-label">Jasmine Room<br><small>3 pax</small></div>
            </div>
            <div class="room-box" style="top: 280px; left: 230px;" @click="bookRoom(rooms[4])">
              <div class="room-label">Lavender Room<br><small>10 pax</small></div>
            </div>

            <!-- Main Study Area -->
            <div class="area-box" style="top: 180px; left: 420px; width: 200px; height: 210px; background-color: #D9CFC2;">
              <div class="area-label">Main Study Area</div>
            </div>

            <!-- Legend -->
            <div style="position: absolute; bottom: 12px; right: 15px; font-size: 0.85rem; background: white; padding: 6px 12px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <span style="color: #E8B4B8;">■</span> Study Room
            </div>
          </div>
        </div>
      </div>

      <!-- Room List -->
      <h5 class="mb-3" style="color: #2C2C2C;">Available Study Rooms</h5>
      <div class="row g-4">
        <div class="col-md-4" v-for="room in rooms" :key="room.id">
          <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2;">
            <div class="card-body">
              <h5>{{ room.name }}</h5>
              <p>Capacity: <strong>{{ room.capacity }}</strong> people</p>
              <button class="btn btn-pink w-100 mt-2" @click="bookRoom(room)">Book This Room</button>
            </div>
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

const rooms = ref([
  { id: 1, name: "Rose Room", capacity: 4, equipment: "Whiteboard, Projector" },
  { id: 2, name: "Lily Room", capacity: 6, equipment: "TV, Speakers" },
  { id: 3, name: "Orchid Room", capacity: 8, equipment: "Projector, Coffee Machine" },
  { id: 4, name: "Jasmine Room", capacity: 3, equipment: "Whiteboard" },
  { id: 5, name: "Lavender Room", capacity: 10, equipment: "TV, Projector, Table" }
])

const bookRoom = (room) => {
  router.push({
    path: '/booking-form',
    query: {
      type: 'room',
      roomId: room.id,
      roomName: room.name
    }
  })
}
</script>

<style scoped>
.room-box {
  position: absolute;
  width: 160px;
  height: 110px;
  background-color: #E8B4B8;
  border: 2px solid #2C2C2C;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.3s;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.room-box:hover {
  background-color: #D89CA1;
  transform: scale(1.03);
}
.room-label {
  text-align: center;
  font-weight: 600;
  color: #2C2C2C;
  font-size: 0.95rem;
}
.area-box {
  position: absolute;
  border: 2px solid #8B7D6B;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.area-label {
  font-weight: 600;
  color: #2C2C2C;
}
</style>
