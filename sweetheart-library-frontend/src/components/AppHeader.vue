<template>
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm"
       style="background: linear-gradient(#2C2C2C, #1F1F1F);">

    <div class="container-fluid px-4 d-flex align-items-center">

      <!-- Logo (Left) -->
      <router-link class="navbar-brand fw-bold d-flex align-items-center me-3" to="/">
        <img
          src="../assets/images/logo.png"
          alt="Sweetheart Library"
          class="me-2 logo"
          style="width: 42px; height: 42px;"
        >
        <span style="color: #F8F4F0; font-size: 1.35rem; letter-spacing: 0.5px;">
          Sweetheart Library
        </span>
      </router-link>


      <!-- Right Side: Hamburger + Profile -->
      <div class="d-flex align-items-center">

        <!-- Hamburger Button (Mobile) -->
        <button
          class="navbar-toggler border-0 me-2 d-lg-none"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Profile Dropdown (Always at the FAR RIGHT) -->
        <div class="dropdown">
          <!-- Not logged in -->
          <router-link
            v-if="!authStore.token"
            to="/login"
            class="btn btn-outline-light px-3 py-1"
          >
            Login
          </router-link>

          <!-- Logged in Profile -->
          <div v-else>
            <button
              class="btn p-0 border-0 bg-transparent profile-btn"
              data-bs-toggle="dropdown"
              aria-label="Profile menu"
            >
              <img
                :src="authStore.user?.avatar || '../assets/images/profile-avatar.png'"
                alt="Profile"
                class="rounded-circle profile-avatar"
              >
            </button>

            <!-- Dark Dropdown Menu -->
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
              <li v-if="authStore.isUser">
                <router-link class="dropdown-item" to="/feedback">Send Feedback</router-link>
              </li>

              <li v-if="authStore.isAdmin">
                <router-link class="dropdown-item" to="/admin">Admin Dashboard</router-link>
              </li>
              <li v-if="authStore.isAdmin">
                <router-link class="dropdown-item" to="/admin/feedback">View Feedback</router-link>
              </li>

              <li><hr class="dropdown-divider"></li>
              <li>
                <button class="dropdown-item text-danger fw-medium" @click="logout">
                  <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
              </li>
            </ul>
          </div>
        </div>

      </div>

      <!-- Mobile Navigation Menu (Only links, no profile) -->
      <div class="collapse navbar-collapse d-lg-none mt-2" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link class="nav-link text-light px-3" to="/books">Books</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link text-light px-3" to="/rooms">Study Rooms</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link text-light px-3" to="/events">Events</router-link>
          </li>
          <li class="nav-item" v-if="authStore.isUser">
            <router-link class="nav-link text-light px-3" to="/my-bookings">My Bookings</router-link>
          </li>
        </ul>
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
/* Logo */
.logo {
  transition: transform 0.3s ease;
}
.navbar-brand:hover .logo {
  transform: scale(1.08);
}

/* Profile Avatar */
.profile-avatar {
  width: 42px;
  height: 42px;
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

/* Dark Dropdown */
.elegant-dropdown {
  background-color: #1F1F1F;
  border: 1px solid #3A3A3A;
  border-radius: 14px;
  padding: 8px 0;
  min-width: 240px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
}

.elegant-dropdown .dropdown-item {
  color: #F8F4F0;
  padding: 11px 20px;
  transition: all 0.2s ease;
}

.elegant-dropdown .dropdown-item:hover {
  background-color: #333;
  color: #E8B4B8;
}

.elegant-dropdown .dropdown-divider {
  border-color: #3A3A3A;
}
</style>
