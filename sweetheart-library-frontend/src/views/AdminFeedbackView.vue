<template>
  <div class="admin-feedback-page">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
          <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.4rem;">
            <i class="bi bi-chat-heart-fill me-2" style="color: #E8B4B8;"></i>
            User Feedback
          </h1>
          <p class="text-muted mb-0 fs-5">View all feedback submitted by users</p>
        </div>
        <div class="mt-3 mt-md-0">
          <span class="badge px-4 py-2 fs-6"
                style="background-color: #E8B4B8; color: #2C2C2C; font-weight: 700; border-radius: 50px;">
            {{ feedbacks.length }} Feedbacks
          </span>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="summary-card">
          <div class="d-flex align-items-center">
            <div class="summary-icon bg-pink">
              <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Total Feedback</div>
              <h3 class="fw-bold mb-0">{{ feedbacks.length }}</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="summary-card">
          <div class="d-flex align-items-center">
            <div class="summary-icon bg-success">
              <i class="bi bi-star-fill"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Average Rating</div>
              <h3 class="fw-bold mb-0">{{ averageRating }} ★</h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="summary-card">
          <div class="d-flex align-items-center">
            <div class="summary-icon bg-info">
              <i class="bi bi-lightbulb-fill"></i>
            </div>
            <div class="ms-3">
              <div class="text-muted small">Suggestions</div>
              <h3 class="fw-bold mb-0">{{ suggestionCount }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Feedback Table -->
    <div class="card elegant-card">
      <div class="card-header elegant-card-header">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>All User Feedback</h5>
      </div>

      <div v-if="feedbacks.length === 0" class="card-body text-center py-5">
        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
        <h5 class="text-muted">No feedback yet</h5>
        <p class="text-muted">Feedback from users will appear here.</p>
      </div>

      <div v-else class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th style="width: 140px;">Date</th>
                <th>User</th>
                <th>Type</th>
                <th>Message</th>
                <th style="width: 100px;">Rating</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="fb in feedbacks" :key="fb.id">
                <td class="text-muted small">
                  {{ formatDate(fb.created_at) }}
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
                <td style="max-width: 420px; white-space: pre-wrap; line-height: 1.5;">
                  {{ fb.message }}
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <span class="fw-bold me-1">{{ fb.rating }}</span>
                    <i class="bi bi-star-fill text-warning"></i>
                  </div>
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const feedbacks = ref([])

const API_URL = 'http://localhost/sweetheart-library-backend/api/feedback.php'

// Fetch all feedback
const fetchFeedbacks = async () => {
  try {
    const res = await axios.get(API_URL)
    feedbacks.value = res.data || []
  } catch (error) {
    console.error('Failed to load feedbacks:', error)
    alert('Could not load feedback from server')
  }
}

// Computed values
const averageRating = computed(() => {
  if (feedbacks.value.length === 0) return '0.0'
  const total = feedbacks.value.reduce((sum, fb) => sum + parseFloat(fb.rating || 0), 0)
  return (total / feedbacks.value.length).toFixed(1)
})

const suggestionCount = computed(() => {
  return feedbacks.value.filter(fb => fb.type === 'Suggestion').length
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-MY', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getTypeColor = (type) => {
  if (type === 'Suggestion') return '#E8B4B8'
  if (type === 'Complaint') return '#FF6B6B'
  if (type === 'Praise') return '#A8DADC'
  return '#B8B8B8'
}

onMounted(() => {
  fetchFeedbacks()
})
</script>

<style scoped>
.admin-feedback-page {
  padding: 25px 30px;
}

.summary-card {
  background: white;
  border-radius: 16px;
  padding: 20px 24px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
  border: 1px solid #f0f0f0;
}

.summary-icon {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  color: white;
}

.bg-pink { background-color: #E8B4B8; }
.bg-success { background-color: #4CAF50; }
.bg-info { background-color: #5BC0DE; }

.elegant-card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
}

.elegant-card-header {
  background-color: #fff;
  border-bottom: 1px solid #f0f0f0;
  padding: 18px 24px;
}

.table th {
  background-color: #fafafa;
  font-weight: 600;
  color: #555;
}
</style>
