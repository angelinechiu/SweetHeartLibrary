<template>
  <Transition name="fade">
    <div v-if="book" class="card h-100 border-0 shadow-sm hover-card">
      <img :src="book.cover_image || 'https://picsum.photos/id/201/400/300'" class="card-img-top" style="height: 260px; object-fit: cover;">
      <div class="card-body d-flex flex-column">
        <h5 class="card-title">{{ book.title }}</h5>
        <p class="text-muted">{{ book.author }}</p>
        <router-link :to="`/books/${book.id}`" class="btn btn-dark mt-auto">View Details</router-link>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({ book: Object })
</script>

<style scoped>
.hover-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 25px rgba(248, 193, 209, 0.3) !important;
}
</style>
