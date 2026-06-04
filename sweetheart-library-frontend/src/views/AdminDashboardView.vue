<template>
  <div class="admin-dashboard">
    <!-- Header -->
    <div class="admin-header mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="flex-grow-1">
          <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.4rem;">Admin Dashboard</h1>
          <p class="text-muted mb-0 fs-5">Manage Books • Rooms • Events • Bookings • Users • Announcements</p>
        </div>

        <div class="d-flex align-items-center gap-3">
          <span class="badge px-4 py-2 fs-6" style="background-color: #E8B4B8; color: #2C2C2C; font-weight: 700; border-radius: 50px;">
            ADMIN
          </span>

          <div class="dropdown d-md-none">
            <button class="btn btn-dark dropdown-toggle px-3 py-2 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
              <i :class="currentTabIcon"></i>
              <span>{{ currentTabLabel }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li v-for="tab in tabs" :key="tab.key">
                <button class="dropdown-item d-flex align-items-center gap-2" @click="activeTab = tab.key">
                  <i :class="tab.icon"></i>
                  <span>{{ tab.label }}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Tabs -->
    <div class="mb-4 d-none d-md-block">
      <ul class="nav nav-pills elegant-admin-tabs">
        <li class="nav-item" v-for="tab in tabs" :key="tab.key">
          <button class="nav-link d-flex align-items-center gap-2" :class="{ active: activeTab === tab.key }" @click="activeTab = tab.key">
            <i :class="tab.icon"></i>
            <span>{{ tab.label }}</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- BOOKS TAB -->
    <div v-if="activeTab === 'books'" class="tab-pane">
      <div class="row g-4">
        <div class="col-xl-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>{{ editingBook ? 'Edit Book' : 'Add New Book' }}</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12"><input v-model="newBook.title" class="form-control" placeholder="Book Title"></div>
                <div class="col-12"><input v-model="newBook.author" class="form-control" placeholder="Author"></div>
                <div class="col-md-6"><input v-model="newBook.isbn" class="form-control" placeholder="ISBN"></div>
                <div class="col-md-6"><input v-model="newBook.category" class="form-control" placeholder="Category"></div>
                <div class="col-md-6"><input v-model.number="newBook.publication_year" type="number" class="form-control" placeholder="Year"></div>
                <div class="col-md-6"><input v-model.number="newBook.copies" type="number" class="form-control" placeholder="Copies"></div>
                <div class="col-12">
                  <select v-model="newBook.availability_status" class="form-select">
                    <option value="Available">Available</option>
                    <option value="Borrowed">Borrowed</option>
                    <option value="Reserved">Reserved</option>
                  </select>
                </div>
                <div class="col-12 d-flex gap-2 mt-3">
                  <button v-if="!editingBook" class="btn btn-pink flex-fill" @click="addBook">Add Book</button>
                  <button v-else class="btn btn-success flex-fill" @click="saveBookEdit">Save Changes</button>
                  <button v-if="editingBook" class="btn btn-secondary flex-fill" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header d-flex justify-content-between">
              <h5><i class="bi bi-book-fill me-2"></i>Manage Books</h5>
              <span class="badge bg-white text-dark">{{ books.length }} books</span>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Year</th><th>Copies</th><th>Status</th><th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="book in books" :key="book.id">
                    <td>{{ book.title }}</td>
                    <td>{{ book.author }}</td>
                    <td>{{ book.isbn }}</td>
                    <td>{{ book.category }}</td>
                    <td>{{ book.publication_year }}</td>
                    <td>{{ book.copies }}</td>
                    <td>{{ book.availability_status }}</td>
                    <td class="text-end">
                      <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditBook(book)">Edit</button>
                      <button class="btn btn-sm action-btn delete-btn" @click="deleteBook(book.id)">Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ROOMS TAB -->
    <div v-if="activeTab === 'rooms'" class="tab-pane">
      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5><i class="bi bi-plus-circle-fill me-2"></i>{{ editingRoom ? 'Edit Room' : 'Add New Room' }}</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12"><input v-model="newRoom.name" class="form-control" placeholder="Room Name"></div>
                <div class="col-12"><input v-model.number="newRoom.capacity" type="number" class="form-control" placeholder="Capacity"></div>
                <div class="col-12"><input v-model="newRoom.equipment" class="form-control" placeholder="Equipment"></div>
                <div class="col-12 d-flex gap-2 mt-3">
                  <button v-if="!editingRoom" class="btn btn-pink w-100" @click="addRoom">Add Room</button>
                  <button v-else class="btn btn-success w-100" @click="saveRoomEdit">Save</button>
                  <button v-if="editingRoom" class="btn btn-secondary w-100" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header d-flex justify-content-between">
              <h5><i class="bi bi-door-open-fill me-2"></i>Manage Rooms</h5>
              <span class="badge bg-white text-dark">{{ rooms.length }} rooms</span>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="room in rooms" :key="room.id">
                    <td class="fw-semibold">{{ room.name }}</td>
                    <td><span class="badge bg-pink text-dark">{{ room.capacity }} seats</span></td>
                    <td>{{ room.equipment }}</td>
                    <td class="text-end">
                      <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditRoom(room)">Edit</button>
                      <button class="btn btn-sm action-btn delete-btn" @click="deleteRoom(room.id)">Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- EVENTS TAB -->
    <div v-if="activeTab === 'events'" class="tab-pane">
      <div class="row g-3">
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5><i class="bi bi-plus-circle-fill me-2"></i>{{ editingEvent ? 'Edit Event' : 'Add New Event' }}</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12"><input v-model="newEvent.title" class="form-control" placeholder="Event Title"></div>
                <div class="col-md-6"><input v-model="newEvent.event_date" type="date" class="form-control"></div>
                <div class="col-md-6"><input v-model="newEvent.event_time" type="time" class="form-control"></div>
                <div class="col-12"><textarea v-model="newEvent.description" class="form-control" rows="2" placeholder="Description"></textarea></div>
                <div class="col-12 d-flex gap-2 mt-3">
                  <button v-if="!editingEvent" class="btn btn-pink w-100" @click="addEvent">Create Event</button>
                  <button v-else class="btn btn-success w-100" @click="saveEventEdit">Save</button>
                  <button v-if="editingEvent" class="btn btn-secondary w-100" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header">
              <h5><i class="bi bi-calendar-event-fill me-2"></i>Manage Events</h5>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Event Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="event in events" :key="event.id">
                    <td class="fw-semibold">{{ event.title }}</td>
                    <td class="text-muted">{{ event.description || '-' }}</td>
                    <td>{{ event.event_date }}</td>
                    <td>{{ event.event_time }}</td>
                    <td class="text-end">
                      <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditEvent(event)">Edit</button>
                      <button class="btn btn-sm action-btn delete-btn" @click="deleteEvent(event.id)">Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- BOOKINGS TAB -->
    <div v-if="activeTab === 'bookings'" class="tab-pane">
      <h4 class="mb-4 fw-bold">Booking Management</h4>

      <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><button class="nav-link" :class="{active: bookingSubTab === 'books'}" @click="bookingSubTab = 'books'">Borrowed Books</button></li>
        <li class="nav-item"><button class="nav-link" :class="{active: bookingSubTab === 'rooms'}" @click="bookingSubTab = 'rooms'">Room Bookings</button></li>
      </ul>

      <!-- Borrowed Books -->
      <div v-if="bookingSubTab === 'books'">
        <div class="card elegant-card">
          <div class="card-header elegant-card-header"><h5><i class="bi bi-book me-2"></i>Borrowed Books</h5></div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Book</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="booking in activeBorrowings" :key="booking.id">
                  <td>{{ booking.user_name }}</td>
                  <td>{{ booking.book_title }}</td>
                  <td>{{ formatDate(booking.due_date) }}</td>
                  <td><span :class="getBookingStatusClass(booking.status)">{{ booking.status }}</span></td>
                  <td class="d-flex gap-1 flex-wrap">
                    <!-- Renew button (for Overdue books) -->
                    <button v-if="booking.status === 'Overdue'" 
                            class="btn btn-sm btn-warning" 
                            @click="renewBook(booking)">
                      Renew
                    </button>
                    
                    <!-- Mark as Returned button -->
                    <button class="btn btn-sm btn-success" 
                            @click="markBookReturned(booking)">
                      Mark Returned
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Room Bookings -->
      <div v-if="bookingSubTab === 'rooms'">
        <div class="card elegant-card">
          <div class="card-header elegant-card-header"><h5><i class="bi bi-door-open me-2"></i>Active Room Bookings</h5></div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Room</th>
                  <th>Time</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="booking in roomBookings" :key="booking.id">
                  <td>{{ booking.user_name }}</td>
                  <td>{{ booking.room_name }}</td>
                  <td>{{ formatDateTime(booking.start_time) }}</td>
                  <td><span :class="getBookingStatusClass(booking.status)">{{ booking.status }}</span></td>
                  <td>
                    <button v-if="canMarkRoomAvailable(booking)" class="btn btn-sm btn-success" @click="markRoomAvailable(booking)">Mark Available</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- USERS TAB -->
    <div v-if="activeTab === 'users'" class="tab-pane mt-4">
      <div class="card elegant-card">
        <div class="card-header elegant-card-header d-flex justify-content-between">
          <h5><i class="bi bi-people-fill me-2"></i>Manage Users</h5>
          <span class="badge bg-white text-dark">{{ users.length }} users</span>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead>
              <tr><th>Name</th><th>Email</th><th>Role</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td class="fw-semibold">{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td><span class="badge bg-pink text-dark">{{ user.role || 'User' }}</span></td>
                <td class="text-end">
                  <button class="btn btn-sm action-btn edit-btn me-2" @click="viewUser(user)">View</button>
                  <button class="btn btn-sm action-btn delete-btn" @click="deleteUser(user.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ANNOUNCEMENTS TAB -->
    <div v-if="activeTab === 'announcements'" class="tab-pane">
      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5><i class="bi bi-plus-circle-fill me-2"></i>{{ editingAnnouncement ? 'Edit' : 'Create' }} Announcement</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12"><input v-model="newAnnouncement.title" class="form-control" placeholder="Title"></div>
                <div class="col-12">
                  <select v-model="newAnnouncement.type" class="form-select">
                    <option value="Notice">Notice</option>
                    <option value="Event">Event</option>
                    <option value="Maintenance">Maintenance</option>
                  </select>
                </div>
                <div class="col-12"><textarea v-model="newAnnouncement.message" class="form-control" rows="3" placeholder="Message"></textarea></div>
                <div class="col-12 d-flex gap-2 mt-2">
                  <button v-if="!editingAnnouncement" class="btn btn-pink flex-fill" @click="addAnnouncement">Publish</button>
                  <button v-else class="btn btn-success flex-fill" @click="saveAnnouncementEdit">Save</button>
                  <button v-if="editingAnnouncement" class="btn btn-secondary flex-fill" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header d-flex justify-content-between">
              <h5><i class="bi bi-megaphone-fill me-2"></i>Manage Announcements</h5>
              <span class="badge bg-white text-dark">{{ announcements.length }} announcements</span>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead>
                  <tr><th>Title</th><th>Type</th><th>Date</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                  <tr v-for="ann in announcements" :key="ann.id">
                    <td class="fw-semibold">{{ ann.title }}</td>
                    <td><span class="badge bg-pink text-dark">{{ ann.type }}</span></td>
                    <td>{{ ann.created_at || ann.published_at }}</td>
                    <td class="text-end">
                      <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditAnnouncement(ann)">Edit</button>
                      <button class="btn btn-sm action-btn delete-btn" @click="deleteAnnouncement(ann.id)">Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Modal -->
    <div v-if="showUserModal" class="admin-modal-backdrop">
      <div class="admin-modal">
        <h5>User Details</h5>
        <p><strong>Name:</strong> {{ selectedUser?.name }}</p>
        <p><strong>Email:</strong> {{ selectedUser?.email }}</p>
        <button class="btn btn-secondary mt-3" @click="closeUserModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'
import axios from 'axios'

const activeTab = ref('books')

const tabs = [
  { key: 'books', label: 'Books', icon: 'bi bi-book-fill' },
  { key: 'rooms', label: 'Rooms', icon: 'bi bi-door-open-fill' },
  { key: 'events', label: 'Events', icon: 'bi bi-calendar-event-fill' },
  { key: 'bookings', label: 'Bookings', icon: 'bi bi-calendar-check-fill' },
  { key: 'users', label: 'Users', icon: 'bi bi-people-fill' },
  { key: 'announcements', label: 'Announcements', icon: 'bi bi-megaphone-fill' }
]

const books = ref([])
const rooms = ref([])
const events = ref([])
const users = ref([])
const selectedUser = ref(null)
const showUserModal = ref(false)

const announcements = ref([])
const newAnnouncement = ref({ title: '', type: 'Notice', message: '' })
const editingAnnouncement = ref(false)

const newBook = ref({ title: '', author: '', isbn: '', category: '', publication_year: new Date().getFullYear(), copies: 1, availability_status: 'Available' })
const newRoom = ref({ name: '', capacity: '', equipment: '' })
const newEvent = ref({ title: '', event_date: '', event_time: '', description: '' })

const editingBook = ref(false)
const editingRoom = ref(false)
const editingEvent = ref(false)

const bookingSubTab = ref('books')
const activeBorrowings = ref([])
const roomBookings = ref([])

const API_BASE = 'http://localhost/sweetheart-library-backend/api/'

// ==================== LOAD DATA ====================
const loadAllData = async () => {
  try {
    const [b, r, e] = await Promise.all([api.get('/books.php'), api.get('/rooms.php'), api.get('/events.php')])
    books.value = b.data || []
    rooms.value = r.data || []
    events.value = e.data || []
  } catch (err) { console.error(err) }
}

const loadAnnouncements = async () => {
  try {
    const res = await api.get('/announcements.php?action=get_all')
    announcements.value = res.data || []
  } catch (err) { console.error(err) }
}

const fetchBookings = async () => {
  try {
    const res = await axios.get(API_BASE + 'bookings.php?action=get')
    if (res.data?.success) {
      activeBorrowings.value = res.data.borrowed_books || []
      roomBookings.value = res.data.room_bookings || []
    }
  } catch (err) { console.error(err) }
}

// ==================== ANNOUNCEMENTS ====================
const addAnnouncement = async () => {
  if (!newAnnouncement.value.title || !newAnnouncement.value.message) return alert('Title and message required')
  try {
    await api.post('/announcements.php', newAnnouncement.value)
    newAnnouncement.value = { title: '', type: 'Notice', message: '' }
    loadAnnouncements()
  } catch (error) {
    console.error(error)
    alert('Failed to create announcement')
  }
}

const startEditAnnouncement = (ann) => {
  newAnnouncement.value = { ...ann }
  editingAnnouncement.value = true
}

const saveAnnouncementEdit = async () => {
  try {
    await api.post('/announcements.php?action=update', newAnnouncement.value)
    editingAnnouncement.value = false
    newAnnouncement.value = { title: '', type: 'Notice', message: '' }
    loadAnnouncements()
  } catch (error) {
    console.error(error)
    alert('Failed to update announcement')
  }
}

const deleteAnnouncement = async (id) => {
  if (confirm('Delete this announcement?')) {
    try {
      await api.delete(`/announcements.php?id=${id}`)
      loadAnnouncements()
    } catch (error) {
      console.error(error)
      alert('Failed to delete announcement')
    }
  }
}

// ==================== SEND REMINDER ====================
const sendReminder = async (booking) => {
  if (!booking?.id) return
  if (!confirm(`Send reminder for ${booking.book_title}?`)) return

  try {
    await api.post('/announcements.php', {
      title: 'Overdue Book Reminder',
      message: `You have an overdue book: "${booking.book_title}". Please return it soon.`,
      type: 'Overdue Reminder',
      due_date: booking.due_date,
      is_published: 1
    })
    alert('Reminder sent successfully!')
    fetchBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to send reminder')
  }
}

const markRoomAvailable = async (booking) => {
  if (!booking?.id) return
  if (!confirm(`Mark room as available?`)) return
  try {
    await axios.post(API_BASE + 'bookings.php?action=mark_room_available', { booking_id: booking.id })
    alert('Room marked as available!')
    fetchBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to update room')
  }
}

// ==================== BORROWED BOOKS ACTIONS ====================
// Renew book (sets status back to Borrowed, clears overdue)
const renewBook = async (booking) => {
  if (!booking?.id) return
  if (!confirm(`Renew this book for ${booking.user_name}?`)) return

  try {
    await axios.post(API_BASE + 'bookings.php?action=renew_book', { 
      booking_id: booking.id 
    })
    alert('Book renewed successfully! Status set to Borrowed.')
    fetchBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to renew book')
  }
}

// Mark book as returned (removes from active borrowed list)
const markBookReturned = async (booking) => {
  if (!booking?.id) return
  if (!confirm(`Mark "${booking.book_title}" as returned by ${booking.user_name}?`)) return

  try {
    await axios.post(API_BASE + 'bookings.php?action=mark_returned', { 
      booking_id: booking.id 
    })
    alert('Book marked as returned!')
    fetchBookings()
  } catch (error) {
    console.error(error)
    alert('Failed to mark book as returned')
  }
}

// ==================== CRUD ====================
const addBook = async () => { await api.post('/books.php', newBook.value); loadAllData() }
const startEditBook = (book) => { newBook.value = { ...book }; editingBook.value = true }
const saveBookEdit = async () => { await api.post('/books.php', newBook.value); editingBook.value = false; loadAllData() }
const deleteBook = async (id) => { if (confirm('Delete book?')) { await api.delete(`/books.php?id=${id}`); loadAllData() } }

const addRoom = async () => { await api.post('/rooms.php', newRoom.value); loadAllData() }
const startEditRoom = (room) => { newRoom.value = { ...room }; editingRoom.value = true }
const saveRoomEdit = async () => { await api.post('/rooms.php', newRoom.value); editingRoom.value = false; loadAllData() }
const deleteRoom = async (id) => { if (confirm('Delete room?')) { await api.delete(`/rooms.php?id=${id}`); loadAllData() } }

const addEvent = async () => { await api.post('/events.php', newEvent.value); loadAllData() }
const startEditEvent = (event) => { newEvent.value = { ...event }; editingEvent.value = true }
const saveEventEdit = async () => { await api.post('/events.php', newEvent.value); editingEvent.value = false; loadAllData() }
const deleteEvent = async (id) => { if (confirm('Delete event?')) { await api.delete(`/events.php?id=${id}`); loadAllData() } }

const loadUsers = async () => { try { const res = await api.get('/users.php'); users.value = res.data || [] } catch(error) { console.error(error) } }
const viewUser = (u) => { selectedUser.value = u; showUserModal.value = true }
const closeUserModal = () => { showUserModal.value = false }
const deleteUser = async (id) => { if (confirm('Delete user?')) { await api.delete(`/users.php?id=${id}`); loadUsers() } }

// Helpers
const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-MY') : '-'
const formatDateTime = (d) => d ? new Date(d).toLocaleString('en-MY') : '-'
const getBookingStatusClass = (s) => s === 'Overdue' ? 'badge bg-danger' : 'badge bg-success'

const canMarkRoomAvailable = (b) => {
  return b.status === 'Active' || !b.status || b.status === 'Pending'
}

onMounted(() => {
  loadAllData()
  loadUsers()
  loadAnnouncements()
  fetchBookings()
})
</script>

<style scoped>
/* ==================== TAB STYLING ==================== */
.elegant-admin-tabs .nav-link {
  color: #2C2C2C;
  font-weight: 600;
  padding: 10px 24px;
  border-radius: 50px;
  margin-right: 8px;
  border: 2px solid #E8B4B8;
  background: white;
  transition: all 0.3s ease;
}

.elegant-admin-tabs .nav-link.active {
  background-color: #E8B4B8 !important;
  color: #2C2C2C !important;
  border-color: #E8B4B8 !important;
  box-shadow: 0 4px 15px rgba(232, 180, 184, 0.4);
  font-weight: 700;
}

/* ==================== CARDS ==================== */
.elegant-card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.elegant-card-header {
  background: linear-gradient(#2C2C2C, #1F1F1F);
  color: #F8F4F0;
  padding: 16px 20px;
  font-weight: 600;
}

/* ==================== ACTION BUTTONS ==================== */
.action-btn {
  border-radius: 8px;
  padding: 6px 14px;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  border: none;
}

.edit-btn {
  background-color: #FFF3CD;
  color: #856404;
}
.edit-btn:hover {
  background-color: #ffe69c;
  color: #664d03;
}

.delete-btn {
  background-color: #F8D7DA;
  color: #842029;
}
.delete-btn:hover {
  background-color: #f5c2c7;
  color: #58151c;
}

/* ==================== CONSISTENT BADGE STYLING ==================== */
.badge {
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
}

/* Status badges in Bookings */
.badge.bg-success {
  background-color: #D1E7DD !important;
  color: #0f5132 !important;
}

.badge.bg-danger {
  background-color: #F8D7DA !important;
  color: #842029 !important;
}

/* Type / Role / Capacity badges - consistent soft pink */
.badge.bg-pink {
  background-color: #E8B4B8 !important;
  color: #2C2C2C !important;
}

/* Capacity in Rooms */
.badge.bg-secondary {
  background-color: #E8B4B8 !important;
  color: #2C2C2C !important;
}
</style>