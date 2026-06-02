<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4" style="color: #2C2C2C;">Borrowing History</h2>

      <table class="table table-striped">
        <thead style="background-color: #2C2C2C; color: #F8F4F0;">
          <tr>
            <th>Date</th>
            <th>Book Name</th>
            <th>Author</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in history" :key="item.id">
            <td>{{ item.booking_date }}</td>
            <td>{{ item.purpose }}</td>
            <td>{{ item.author || 'N/A' }}</td>
            <td>
              <span class="badge" :class="getStatusClass(item.status)">
                {{ item.status }}
              </span>
            </td>
          </tr>
            <tr v-if="history.length === 0">
              <td colspan="4" class="text-center text-muted">No book booking history found.</td>
            </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api.js'

const authStore = useAuthStore()
const history = ref([])

const getStatusClass = (status) => {
  if (status === 'completed') return 'bg-success'
  if (status === 'cancelled') return 'bg-danger'
  return 'bg-secondary'
}

onMounted(async () => {
  const res = await api.get(`/bookings.php?user_id=${authStore.user.id}`)
  // Show only past bookings
  history.value = res.data.filter(b => new Date(b.booking_date) < new Date())
})
</script>
