<template>
  <div class="py-5" style="background-color: #F8F4F0; min-height: 80vh;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-7">
          <div class="text-center mb-5">
            <h1 class="fw-bold" style="color: #2C2C2C;">We Value Your Feedback</h1>
            <p class="lead" style="color: #555;">Your thoughts help us improve</p>
          </div>

          <div v-if="submitted" class="alert alert-success text-center py-4 rounded-4">
            <h5>Thank you!</h5>
            <p class="mb-0">Your feedback has been submitted successfully.</p>
          </div>

          <div v-else class="card border-0 shadow-sm p-4 p-md-5 rounded-4">
            <form @submit.prevent="submitFeedback">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-medium">Your Name</label>
                  <input v-model="form.name" type="text" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-medium">Email</label>
                  <input v-model="form.email" type="email" class="form-control" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium">Feedback Type</label>
                <select v-model="form.type" class="form-select" required>
                  <option value="">Please select...</option>
                  <option value="General">General Feedback</option>
                  <option value="Suggestion">Suggestion</option>
                  <option value="Complaint">Complaint</option>
                  <option value="Praise">Praise</option>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label fw-medium">Your Message</label>
                <textarea v-model="form.message" class="form-control" rows="6" required></textarea>
              </div>

              <div class="text-center">
                <button type="submit" class="btn px-5 py-2 fw-semibold"
                        style="background-color: #E8B4B8; color: #2C2C2C; border-radius: 50px;">
                  Submit Feedback
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api.js'   // Make sure you have this file

const form = ref({
  name: '',
  email: '',
  type: '',
  message: ''
})

const submitted = ref(false)

const submitFeedback = async () => {
  try {
    await api.post('/feedback.php', form.value)
    submitted.value = true

    // Reset form
    form.value = { name: '', email: '', type: '', message: '' }

    setTimeout(() => {
      submitted.value = false
    }, 3000)
  } catch (error) {
    alert('Failed to submit feedback. Please try again.')
    console.error(error)
  }
}
</script>
