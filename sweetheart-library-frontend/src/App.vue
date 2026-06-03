<template>
  <div class="app-wrapper d-flex flex-column min-vh-100">
    <!-- Elegant Header -->
    <Header />

    <!-- Main Content Area -->
    <main class="main-content flex-grow-1">
      <div class="content-container">
        <router-view />
      </div>
    </main>

    <!-- Elegant Footer -->
    <Footer />
  </div>
</template>

<script setup>
import Header from './components/AppHeader.vue'
import Footer from './components/AppFooter.vue'
</script>

<style>
/* ==================== GLOBAL ELEGANT THEME ==================== */
:root {
  --primary: #2C2C2C;
  --accent: #E8B4B8;
  --light-bg: #F8F4F0;
  --beige: #D9CFC2;
}

/* ==================== APP LAYOUT ==================== */
.app-wrapper {
  background-color: #F8F4F0;
}

.main-content {
  padding: 50px 20px 70px;
  min-height: 70vh;
}

.content-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 15px;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 992px) {
  .main-content {
    padding: 35px 15px 55px;
  }
}

@media (max-width: 576px) {
  .main-content {
    padding: 25px 12px 45px;
  }
  .content-container {
    padding: 0 8px;
  }
}

/* ==================== YOUR EXISTING BEAUTIFUL STYLES ==================== */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.hover-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
}

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
