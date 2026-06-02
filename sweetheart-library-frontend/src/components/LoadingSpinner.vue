<template>
  <div v-if="loading" class="text-center my-4">
    <div class="spinner-border"
         role="status"
         :style="{ width: size + 'rem', height: size + 'rem', color: '#E8B4B8' }">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p v-if="message" class="mt-3" style="color: #8B7D6B;">{{ message }}</p>
  </div>
</template>

<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: ''
  },
  size: {
    type: Number,
    default: 3
  }
})
</script>
