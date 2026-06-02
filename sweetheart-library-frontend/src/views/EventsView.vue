<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-5" style="color: #2C2C2C;">Upcoming Events & Workshops</h2>

      <LoadingSpinner :loading="loading" message="Loading events..." />

      <div v-if="!loading" class="row g-4">
        <div class="col-md-6" v-for="event in upcomingEvents" :key="event.id">
          <div class="card border-0 shadow-sm hover-card h-100" style="background-color: #D9CFC2;">
            <div class="card-body">
              <h5 style="color: #2C2C2C;">{{ event.title }}</h5>
              <p class="text-muted mb-2">
                <strong>{{ event.event_date }}</strong> • {{ event.event_time }}
              </p>
              <p>{{ event.description }}</p>
            </div>
          </div>
        </div>

        <div v-if="upcomingEvents.length === 0" class="col-12 text-center">
          <p class="text-muted">No upcoming events at the moment. Please check back later!</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../services/api.js'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const events = ref([])
const loading = ref(true)

const upcomingEvents = computed(() => {
  const today = new Date()
  return events.value.filter(event => {
    const eventDate = new Date(event.event_date)
    return eventDate >= today
  })
})

const loadEvents = async () => {
  loading.value = true
  try {
    const res = await api.get('/events.php')
    events.value = res.data
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

onMounted(loadEvents)
</script>
