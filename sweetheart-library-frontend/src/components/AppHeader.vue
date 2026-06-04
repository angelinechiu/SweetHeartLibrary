<template>
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm"
       style="background: linear-gradient(#2C2C2C, #1F1F1F);">

    <div class="container-fluid px-4">

      <!-- Logo - Left -->
      <router-link class="navbar-brand fw-bold d-flex align-items-center" to="/">
        <img src="../assets/images/logo.png" alt="Sweetheart Library" class="navbar-logo me-2">
        <span style="color: #F8F4F0; font-size: 1.45rem; letter-spacing: 0.5px; margin-left: 8px;">
          Sweetheart Library
        </span>
      </router-link>

      <!-- Main Navigation + Profile Group -->
      <div class="d-flex align-items-center ms-auto">

        <!-- Desktop Navigation Links -->
        <div class="collapse navbar-collapse d-lg-block justify-content-end" id="navbarNav">
          <ul class="navbar-nav me-4 text-end">
            <li class="nav-item">
              <router-link class="nav-link text-light px-3" to="/books">Books</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link text-light px-3" to="/rooms">Study Rooms</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link text-light px-3" to="/events">Events</router-link>
            </li>
          </ul>
        </div>

        <!-- Profile Dropdown - Always Far Right -->
        <div class="dropdown">
          <!-- Not Logged In -->
          <router-link
            v-if="!authStore.token"
            to="/login"
            class="btn btn-outline-light px-3 py-1"
          >
            Login
          </router-link>

          <!-- Logged In -->
          <div v-else>
            <button
              class="btn p-0 border-0 bg-transparent profile-btn"
              data-bs-toggle="dropdown"
            >
              <img
                :src="authStore.user?.avatar || defaultAvatar"
                alt="Profile"
                class="rounded-circle profile-avatar"
              >
            </button>

            <!-- Dark Dropdown -->
            <ul class="dropdown-menu dropdown-menu-end shadow elegant-dropdown">
              <li class="px-3 py-2">
                <div class="fw-semibold" style="color: #E8B4B8;">
                  {{ authStore.user?.name || 'User' }}
                </div>
                <small class="text-muted">{{ authStore.user?.email }}</small>
                <div v-if="authStore.isAdmin" class="mt-1">
                  <span class="badge px-2 py-1"
                        style="background-color: #E8B4B8; color: #2C2C2C; font-size: 0.7rem;">
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
              <li v-if="authStore.isUser">
                <router-link class="dropdown-item" to="/announcements">Announcements</router-link>
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
                <button class="dropdown-item text-danger" @click="logout">
                  <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
              </li>
            </ul>
          </div>
        </div>

        <button
          class="navbar-toggler border-0 d-lg-none ms-2"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import defaultAvatar from '../assets/images/profile-avatar.png'


const authStore = useAuthStore()

const logout = () => {
  authStore.logout()
  window.location.href = '/login'
}
</script>

<style scoped>
.profile-avatar {
  width: 42px;
  height: 42px;
  object-fit: cover;
  background-color: #ffffff;
  border: 3px solid #E8B4B8;
  border-radius: 50%;
  box-shadow: 0 3px 12px rgba(232, 180, 184, 0.35);
}

.navbar-logo {
  width: 34px;
  height: 34px;
  object-fit: contain;
}

.profile-avatar:hover {
  transform: scale(1.08);
  border-color: #D89CA1;
}

.navbar {
  position: relative;
}

@media (max-width: 991.98px) {
  .navbar-collapse {
    position: absolute;
    left: 0;
    right: 0;
    top: 100%;
    width: 100%;
    background-color: #1F1F1F;
    border: none;
    border-top: 1px solid #3A3A3A;
    border-radius: 0;
    padding: 0.5rem 0;
    z-index: 1050;
  }

  .navbar-collapse .navbar-nav {
    margin: 0;
  }

  .navbar-collapse .nav-link {
    padding: 0.75rem 1rem;
  }
}

.elegant-dropdown {
  background-color: #1F1F1F;
  border: 1px solid #3A3A3A;
  border-radius: 14px;
  min-width: 240px;
}

.elegant-dropdown .dropdown-item {
  color: #F8F4F0;
  padding: 10px 20px;
}

.elegant-dropdown .dropdown-item:hover {
  background-color: #333;
  color: #E8B4B8;
}
</style>