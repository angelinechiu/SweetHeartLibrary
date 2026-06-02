<template>
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm"
       style="background: linear-gradient(#2C2C2C, #1F1F1F);">
    <div class="container">

      <!-- Brand with New Logo -->
      <router-link class="navbar-brand fw-bold d-flex align-items-center" to="/">
        <img
          src="../assets/images/logo.png"
          alt="Sweetheart Library Logo"
          class="me-2 logo"
        >
        <span style="color: #F8F4F0; letter-spacing: 0.5px; font-size: 1.35rem;">
          Sweetheart Library
        </span>
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

        <!-- Profile Section -->
        <div class="d-flex align-items-center gap-3 ms-4">
          <!-- Not logged in -->
          <router-link
            v-if="!authStore.token"
            to="/login"
            class="btn btn-outline-light px-4 py-1"
          >
            Login
          </router-link>

          <!-- Logged in Profile -->
          <div v-else class="dropdown">
            <button
              class="btn p-0 border-0 bg-transparent profile-btn"
              data-bs-toggle="dropdown"
              aria-label="Profile menu"
            >
              <img
                src="../assets/images/profile-avatar.png"
                alt="Profile"
                class="rounded-circle profile-avatar"
              >
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow elegant-dropdown">
              <li class="px-3 py-2">
                <div class="fw-semibold" style="color: #E8B4B8;">
                  {{ authStore.user?.name || 'User' }}
                </div>
                <small class="text-muted">{{ authStore.user?.email }}</small>
                <div v-if="authStore.isAdmin" class="mt-1">
                  <span class="badge px-2 py-1"
                        style="background-color: #E8B4B8; color: #2C2C2C; font-size: 0.7rem; font-weight: 600;">
                    ADMIN
                  </span>
                </div>
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
                <button class="dropdown-item text-danger fw-medium" @click="logout">
                  Logout
                </button>
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

<style scoped>
/* Logo Styling */
.logo {
  width: 46px;
  height: 46px;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.navbar-brand:hover .logo {
  transform: scale(1.08);
}

/* Profile Avatar (same as before) */
.profile-avatar {
  width: 44px;
  height: 44px;
  object-fit: cover;
  border: 3px solid #E8B4B8;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background-color: #F8F4F0;
}

.profile-avatar:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 18px rgba(232, 180, 184, 0.5);
  border-color: #D89CA1;
}

/* Elegant Dropdown */
.elegant-dropdown {
  background-color: #2C2C2C;
  border: 1px solid #3A3A3A;
  border-radius: 14px;
  padding: 8px 0;
  min-width: 230px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.elegant-dropdown .dropdown-item {
  color: #F8F4F0;
  padding: 11px 20px;
  transition: all 0.2s ease;
}

.elegant-dropdown .dropdown-item:hover {
  background-color: #3A3A3A;
  color: #E8B4B8;
}

.elegant-dropdown .dropdown-divider {
  border-color: #3A3A3A;
}
</style>
