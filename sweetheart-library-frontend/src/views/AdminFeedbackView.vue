<template>
  <div class="admin-feedback-page">
    <!-- Page Header -->
    <div class="admin-header mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.3rem;">User Feedback</h1>
          <p class="text-muted mb-0 fs-5">View and manage feedback from library users</p>
        </div>
        <div>
          <span class="badge px-4 py-2 fs-6"
                style="background-color: #E8B4B8; color: #2C2C2C; font-weight: 700; border-radius: 50px;">
            {{ feedbacks.length }} Feedbacks
          </span>
        </div>
      </div>
    </div>

    <!-- Feedback Table -->
    <div class="card elegant-card">
      <div class="card-header elegant-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-chat-left-text-fill me-2"></i>All User Feedback</h5>
        <span v-if="feedbacks.length > 0" class="badge bg-light text-dark px-3 py-2">
          {{ feedbacks.length }} total
        </span>
      </div>

      <div v-if="feedbacks.length === 0" class="card-body text-center py-5">
        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
        <h5 class="text-muted">No feedback yet</h5>
        <p class="text-muted">Feedback submitted by users will appear here.</p>
      </div>

      <div v-else class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>User</th>
                <th>Type</th>
                <th>Message</th>
                <th>Status</th>
                <th class="text-end pe-4">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="fb in feedbacks" :key="fb.id">
                <td class="text-muted small">
                  {{ new Date(fb.created_at).toLocaleDateString('en-MY', {
                    month: 'short', day: 'numeric', year: 'numeric'
                  }) }}
                </td>
                <td>
                  <div class="fw-semibold">{{ fb.name }}</div>
                  <div class="text-muted small">{{ fb.email }}</div>
                </td>
                <td>
                  <span class="badge px-3 py-2"
                        :style="{ backgroundColor: getTypeColor(fb.type), color: '#2C2C2C', fontWeight: '600' }">
                    {{ fb.type }}
                  </span>
                </td>
                <td style="max-width: 320px;">
                  <div class="text-truncate" style="max-height: 60px; overflow: hidden;">
                    {{ fb.message }}
                  </div>
                </td>
                <td>
                  <select
                    v-model="fb.status"
                    @change="updateStatus(fb)"
                    class="form-select form-select-sm elegant-select">
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                  </select>
                </td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm btn-outline-danger px-3" @click="deleteFeedback(fb.id)">
                    <i class="bi bi-trash3 me-1"></i> Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const feedbacks = ref([])

const getTypeColor = (type) => {
  if (type === 'Suggestion') return '#E8B4B8'
  if (type === 'Complaint') return '#F8D7DA'
  if (type === 'Praise') return '#D4EDDA'
  return '#E8B4B8'
}

const loadFeedback = async () => {
  try {
    const res = await api.get('/feedback.php')
    feedbacks.value = res.data || []
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
    // Optional: show toast instead of alert
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

<style scoped>
/* ==================== ELEGANT ADMIN FEEDBACK STYLES ==================== */
.admin-feedback-page {
  max-width: 1400px;
  margin: 0 auto;
}

.elegant-card {
  border: none;
  border-radius: 18px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07);
  overflow: hidden;
}

.elegant-card-header {
  background: linear-gradient(#2C2C2C, #1F1F1F);
  color: #F8F4F0;
  padding: 18px 24px;
  font-weight: 700;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 700;
  color: #2C2C2C;
  padding: 16px 20px;
}

.table td {
  padding: 16px 20px;
  vertical-align: middle;
}

.elegant-select {
  border: 2px solid #E8B4B8;
  border-radius: 8px;
  font-weight: 600;
}

.elegant-select:focus {
  border-color: #D89CA1;
  box-shadow: 0 0 0 0.2rem rgba(232, 180, 184, 0.25);
}

.btn-outline-danger {
  transition: all 0.2s ease;
}

.btn-outline-danger:hover {
  transform: translateY(-1px);
}
</style>
