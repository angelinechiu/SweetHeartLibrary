<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold" style="color: #2C2C2C;">User Feedback</h2>
      <span class="badge bg-primary">{{ feedbacks.length }} Total</span>
    </div>

    <div v-if="feedbacks.length === 0" class="text-center py-5">
      <p class="text-muted">No feedback received yet.</p>
    </div>

    <div v-else class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Name</th>
              <th>Email</th>
              <th>Type</th>
              <th>Message</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fb in feedbacks" :key="fb.id">
              <td>{{ new Date(fb.created_at).toLocaleDateString() }}</td>
              <td>{{ fb.name }}</td>
              <td>{{ fb.email }}</td>
              <td><span class="badge bg-secondary">{{ fb.type }}</span></td>
              <td style="max-width: 280px;">{{ fb.message }}</td>
              <td>
                <select v-model="fb.status" @change="updateStatus(fb)" class="form-select form-select-sm">
                  <option value="New">New</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Resolved">Resolved</option>
                </select>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-danger" @click="deleteFeedback(fb.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const feedbacks = ref([])

const loadFeedback = async () => {
  try {
    const res = await api.get('/feedback.php')
    feedbacks.value = res.data
  } catch (error) {
    console.error('Failed to load feedback', error)
  }
}

const updateStatus = async (feedback) => {
  try {
    await api.put('/feedback.php', {
      id: feedback.id,
      status: feedback.status
    })
    alert('Status updated successfully')
  } catch (error) {
    console.error('Failed to update status', error)
    alert('Failed to update status')
  }
}

const deleteFeedback = async (id) => {
  if (!confirm('Are you sure you want to delete this feedback?')) return

  try {
    await api.delete(`/feedback.php?id=${id}`)
    feedbacks.value = feedbacks.value.filter(f => f.id !== id)
  } catch (error) {
    console.error('Failed to delete feedback', error)
    alert('Failed to delete feedback')
  }
}

onMounted(loadFeedback)
</script>
