<template>
  <div class="d-flex flex-column min-vh-100" style="background-color: #F8F4F0;">
    <!-- Header -->
    <Header />

    <!-- Main Content -->
    <main class="flex-grow-1">
      <router-view />
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import Header from './components/AppHeader.vue'
import Footer from './components/AppFooter.vue'
</script>

<style>
/* Global Elegant Theme */
:root {
  --primary: #2C2C2C;
  --accent: #E8B4B8;
  --light-bg: #F8F4F0;
  --beige: #D9CFC2;
}

/* Smooth Transitions for all pages */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Beautiful Card Hover Effect */
.hover-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
}

/* Pink Buttons */
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
  transition: all 0.3s ease;
}

.btn-pink:hover {
  background-color: #D89CA1;
  color: #2C2C2C;
  transform: translateY(-2px);
}
</style>
