<template>
  <div class="feedback-page">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="text-center mb-5">
            <h1 class="fw-bold mb-3" style="color: #2C2C2C; font-size: 2.6rem;">
              We Value Your Feedback ❤️
            </h1>
            <p class="text-muted fs-5">Help us improve Sweetheart Library</p>
          </div>

          <div class="card elegant-card shadow-lg">
            <div class="card-body p-5">
              <form @submit.prevent="submitFeedback">
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Your Name</label>
                    <input v-model="form.name" type="text" class="form-control form-control-lg" placeholder="Enter your name" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input v-model="form.email" type="email" class="form-control form-control-lg" placeholder="your@email.com" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Feedback Type</label>
                    <select v-model="form.type" class="form-select form-select-lg" required>
                      <option value="">Select type...</option>
                      <option value="Suggestion">Suggestion</option>
                      <option value="Complaint">Complaint</option>
                      <option value="Praise">Praise</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Rating</label>
                    <select v-model="form.rating" class="form-select form-select-lg">
                      <option v-for="r in 5" :key="r" :value="r">{{ r }} ★</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold">Your Message</label>
                    <textarea v-model="form.message" class="form-control form-control-lg" rows="6" placeholder="Tell us what you think..." required></textarea>
                  </div>
                </div>

                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-lg py-3 fw-bold" :disabled="loading"
                          style="background-color: #E8B4B8; color: #2C2C2C; border: none; border-radius: 12px;">
                    <span v-if="!loading">Submit Feedback</span>
                    <span v-else>Submitting...</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
          <div class="modal-body text-center p-5">
            <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: #4CAF50;"></i>
            <h3 class="fw-bold mt-4 mb-3">Thank You!</h3>
            <p class="text-muted fs-5">Your feedback has been submitted successfully.</p>
            <button type="button" class="btn px-5 py-2 fw-bold mt-3"
                    style="background-color: #E8B4B8; color: #2C2C2C; border-radius: 50px;"
                    data-bs-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import * as bootstrap from 'bootstrap'

const form = ref({
  name: '',
  email: '',
  type: '',
  message: '',
  rating: 5
})

const loading = ref(false)

const submitFeedback = async () => {
  loading.value = true
  try {
    const res = await axios.post(
      'http://localhost/sweetheart-library-backend/api/feedback.php',
      form.value
    )

    if (res.data.success) {
      const modal = new bootstrap.Modal(document.getElementById('successModal'))
      modal.show()

      form.value = { name: '', email: '', type: '', message: '', rating: 5 }
    } else {
      alert(res.data.message)
    }
  } catch (error) {
    console.error('Failed to submit feedback:', error)
    alert('Failed to submit feedback.')
  } finally {
    loading.value = false
  }
}
</script>
