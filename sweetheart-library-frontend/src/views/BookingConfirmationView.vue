<template>
  <div style="background-color: #F8F4F0;" class="py-5 text-center">
    <div class="container">
      <div class="card shadow-lg mx-auto" style="max-width: 600px; background-color: #D9CFC2;">
        <div class="card-body p-5">
          <h1 class="display-1" style="color: #E8B4B8;">✓</h1>
          <h2 class="fw-bold" style="color: #2C2C2C;">Booking Confirmed!</h2>
          <p class="lead">Thank you for choosing Sweetheart Library</p>

          <!-- Booking Details -->
          <div class="text-start mt-4 p-4 rounded" style="background-color: #F8F4F0;">
            <p><strong>Date:</strong> {{ booking.date }}</p>
            <p><strong>Time:</strong> {{ booking.startTime }} — {{ booking.endTime }}</p>
            <p><strong>Purpose:</strong> {{ booking.purpose }}</p>
          </div>

          <!-- Reminder if booking is soon -->
          <div v-if="isSoon" class="alert alert-warning mt-4 text-start">
            <strong>Reminder:</strong> Your booking is in {{ daysLeft }} day(s). Please arrive on time!
          </div>

          <div class="mt-4">
            <router-link to="/my-bookings" class="btn btn-pink btn-lg me-2">View My Bookings</router-link>
            <router-link to="/dashboard" class="btn btn-outline-dark btn-lg">Go to Dashboard</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const booking = ref({
  date: route.query.date || '',
  startTime: route.query.startTime || '',
  endTime: route.query.endTime || '',
  purpose: route.query.purpose || ''
})

const isSoon = computed(() => {
  if (!booking.value.date) return false
  const bookingDate = new Date(booking.value.date)
  const today = new Date()
  const diffTime = bookingDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays <= 3 && diffDays >= 0
})

const daysLeft = computed(() => {
  if (!booking.value.date) return 0
  const bookingDate = new Date(booking.value.date)
  const today = new Date()
  const diffTime = bookingDate - today
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
})
</script>
