<template>
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow"
       style="background: linear-gradient(#2C2C2C, #1F1F1F);">
    <div class="container">

      <!-- Left: App Name -->
      <router-link class="navbar-brand fw-bold d-flex align-items-center" to="/">
        <span class="me-2" style="color: #E8B4B8; font-size: 1.8rem;">❤️</span>
        <span style="color: #F8F4F0;">Sweetheart Library</span>
      </router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link text-light" to="/books">Books</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link text-light" to="/rooms">Study Rooms</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link text-light" to="/events">Events</router-link>
          </li>
        </ul>

        <!-- Right Side: Login or Profile Image -->
        <div class="d-flex align-items-center gap-3 ms-4">

          <!-- Not logged in -->
          <router-link v-if="!authStore.token" to="/login" class="btn btn-outline-light">
            Login
          </router-link>

          <!-- Logged in → Profile Image Button -->
          <div v-else class="dropdown">
            <button
              class="btn p-0 border-0 bg-transparent"
              data-bs-toggle="dropdown"
            >
              <img
                src="../assets/images/logo.png"
                alt="Profile"
                class="rounded-circle border border-secondary"
                style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
              >
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li class="px-3 py-2">
                <div class="fw-semibold" style="color: #E8B4B8;">{{ authStore.user?.name }}</div>
                <small class="text-muted">{{ authStore.user?.email }}</small>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li v-if="authStore.isUser">
                <router-link class="dropdown-item" to="/dashboard">Dashboard</router-link>
              </li>
              <li v-if="authStore.isUser">
                <router-link class="dropdown-item" to="/my-bookings">My Bookings</router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/profile">My Profile</router-link>
              </li>
              <li v-if="authStore.isAdmin">
                <router-link class="dropdown-item" to="/admin">Admin Dashboard</router-link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <button class="dropdown-item text-danger" @click="logout">Logout</button>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

const logout = () => {
  authStore.logout()
  window.location.href = '/login'
}
</script>
