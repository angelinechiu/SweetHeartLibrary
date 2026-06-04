import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/HomeView.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue')
  },

  // User Routes
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/books',
    name: 'BookCatalog',
    component: () => import('../views/BookCatalogView.vue')
  },
  {
    path: '/books/:id',
    name: 'BookDetail',
    component: () => import('../views/BookDetailsView.vue')
  },
  {
    path: '/rooms',
    name: 'RoomBooking',
    component: () => import('../views/RoomBookingView.vue')
  },
  {
    path: '/booking-form',
    name: 'BookingForm',
    component: () => import('../views/BookingFormView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/booking-confirmation',
    name: 'BookingConfirmation',
    component: () => import('../views/BookingConfirmationView.vue')
  },
  {
    path: '/my-bookings',
    name: 'MyBookings',
    component: () => import('../views/MyBookingsView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/ProfileView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/history',
    name: 'History',
    component: () => import('../views/HistoryView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/room-history',
    name: 'RoomHistory',
    component: () => import('../views/RoomHistoryView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/announcements',
    name: 'Announcements',
    component: () => import('../views/AnnouncementsView.vue')
  },
  {
    path: '/events',
    name: 'Events',
    component: () => import('../views/EventsView.vue')
  },
  {
    path: '/feedback',
    name: 'Feedback',
    component: () => import('../views/FeedbackView.vue')
  },
  {
    path: '/admin/feedback',
    name: 'AdminFeedback',
    component: () => import('../views/AdminFeedbackView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: () => import('../views/ForgotPasswordView.vue')
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: () => import('../views/ResetPasswordView.vue')
  },
  // Admin Route
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../views/AdminDashboardView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  }
  else if (to.meta.requiresAdmin && role !== 'admin') {
    next('/dashboard')
  }
  else {
    next()
  }
})

export default router
