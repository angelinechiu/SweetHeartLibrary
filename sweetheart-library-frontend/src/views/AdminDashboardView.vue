<template>
  <div class="admin-dashboard">
    <!-- Header -->
    <div class="admin-header mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <!-- Left Side: Title -->
        <div class="flex-grow-1">
          <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.4rem;">Admin Dashboard</h1>
          <p class="text-muted mb-0 fs-5">Manage Books • Rooms • Events • Bookings • Users • Announcements</p>
        </div>

        <!-- Right Side: ADMIN Badge + Mobile Dropdown -->
        <div class="d-flex align-items-center gap-3">
          <span class="badge px-4 py-2 fs-6"
                style="background-color: #E8B4B8; color: #2C2C2C; font-weight: 700; border-radius: 50px;">
            ADMIN
          </span>

          <!-- Mobile Dropdown (only shows on small screens) -->
          <div class="dropdown d-md-none">
            <button class="btn btn-dark dropdown-toggle px-3 py-2 d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i :class="currentTabIcon"></i>
              <span>{{ currentTabLabel }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li v-for="tab in tabs" :key="tab.key">
                <button class="dropdown-item d-flex align-items-center gap-2"
                        @click="activeTab = tab.key">
                  <i :class="tab.icon"></i>
                  <span>{{ tab.label }}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Tabs (hidden on mobile) -->
    <div class="mb-4 d-none d-md-block">
      <ul class="nav nav-pills elegant-admin-tabs">
        <li class="nav-item" v-for="tab in tabs" :key="tab.key">
          <button class="nav-link d-flex align-items-center gap-2"
                  :class="{ active: activeTab === tab.key }"
                  @click="activeTab = tab.key">
            <i :class="tab.icon"></i>
            <span>{{ tab.label }}</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- ==================== BOOKS ==================== -->
    <div v-if="activeTab === 'books'" class="tab-pane">
      <!-- Books content remains the same -->
      <div class="row g-4">
        <div class="col-xl-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ editingBook ? 'Edit Book' : 'Add New Book' }}
              </h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold small">Title</label>
                  <input v-model="newBook.title" class="form-control fw-semibold small" placeholder="Book Title">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Author</label>
                  <input v-model="newBook.author" class="form-control fw-semibold small" placeholder="Author">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">ISBN</label>
                  <input v-model="newBook.isbn" class="form-control fw-semibold small" placeholder="978-3-16-148410-0">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Category</label>
                  <input v-model="newBook.category" class="form-control fw-semibold small " placeholder="Fiction / Science">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Publication Year</label>
                  <input v-model="newBook.publication_year" type="number" class="form-control fw-semibold small" placeholder="2024">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Copies</label>
                  <input v-model.number="newBook.copies" type="number" class="form-control fw-semibold small" placeholder="3">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Availability Status</label>
                  <select v-model="newBook.availability_status" class="form-select fw-semibold small">
                    <option value="Available">Available</option>
                    <option value="Borrowed">Borrowed</option>
                    <option value="Reserved">Reserved</option>
                    <option value="Maintenance">Under Maintenance</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Description</label>
                  <textarea v-model="newBook.description" class="form-control fw-semibold small" rows="2" placeholder="Short description..."></textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                  <button v-if="!editingBook" class="btn btn-pink flex-fill py-2" @click="addBook">
                    <i class="bi bi-plus-lg me-2"></i> Add Book
                  </button>
                  <button v-else class="btn btn-success flex-fill py-2" @click="saveBookEdit">Save Changes</button>
                  <button v-if="editingBook" class="btn btn-secondary flex-fill py-2" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="bi bi-book-fill me-2"></i>Manage Books</h5>
              <span class="badge bg-light text-dark px-3 py-2">{{ books.length }} books</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Title</th><th>Author</th><th>ISBN</th><th>Category</th>
                      <th>Year</th><th>Copies</th><th>Status</th><th class="text-end pe-4">Actions</th>
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
                      <td class="text-end pe-4">
                        <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditBook(book)">
                          <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm action-btn delete-btn" @click="deleteBook(book.id)">
                          <i class="bi bi-trash3 me-1"></i> Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== ROOMS ==================== -->
    <div v-if="activeTab === 'rooms'" class="tab-pane">
      <!-- Rooms content remains -->
      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ editingRoom ? 'Edit Room' : 'Add New Room' }}
              </h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12"><input v-model="newRoom.name" class="form-control fw-semibold small" placeholder="Room Name"></div>
                <div class="col-12"><input v-model.number="newRoom.capacity" type="number" class="form-control fw-semibold small" placeholder="Capacity"></div>
                <div class="col-12"><input v-model="newRoom.equipment" class="form-control fw-semibold small" placeholder="Equipment"></div>
                <div class="col-12 d-flex gap-2 mt-2">
                  <button v-if="!editingRoom" class="btn btn-pink w-100 py-2" @click="addRoom">
                    <i class="bi bi-plus-lg me-2"></i> Add Room
                  </button>
                  <button v-else class="btn btn-success w-100 py-2" @click="saveRoomEdit">
                    <i class="bi bi-save me-2"></i> Save Changes
                  </button>
                </div>
                <div v-if="editingRoom" class="col-12">
                  <button class="btn btn-secondary w-100 py-2" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Manage Rooms</h5>
              <span class="badge bg-light text-dark px-3 py-2">{{ rooms.length }} rooms</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Capacity</th>
                      <th>Equipment</th>
                      <th class="text-end pe-4">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="room in rooms" :key="room.id">
                      <td class="fw-semibold">{{ room.name }}</td>
                      <td><span class="badge bg-secondary">{{ room.capacity }} seats</span></td>
                      <td>{{ room.equipment }}</td>
                      <td class="text-end pe-4">
                        <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditRoom(room)">
                          <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm action-btn delete-btn" @click="deleteRoom(room.id)">
                          <i class="bi bi-trash3 me-1"></i> Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== EVENTS ==================== -->
    <div v-if="activeTab === 'events'" class="tab-pane">
      <!-- Events content remains -->
      <div class="row g-3">
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Add New Event</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <input v-model="newEvent.title" class="form-control fw-semibold small" placeholder="Event Title">
                </div>
                <div class="col-md-6">
                  <input v-model="newEvent.event_date" type="date" class="form-control fw-semibold small">
                </div>
                <div class="col-md-6">
                  <input v-model="newEvent.event_time" type="time" class="form-control fw-semibold small">
                </div>
                <div class="col-12">
                  <textarea v-model="newEvent.description" class="form-control fw-semibold small" rows="2" placeholder="Description"></textarea>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                  <button v-if="!editingEvent" class="btn btn-pink w-100 py-2" @click="addEvent">Create Event</button>
                  <button v-else class="btn btn-success flex-fill py-2" @click="saveEventEdit">Save Changes</button>
                  <button v-if="editingEvent" class="btn btn-secondary flex-fill py-2" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0"><i class="bi bi-calendar-event-fill me-2"></i>Manage Events</h5>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Event Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th class="text-end pe-4">Actions</th>
                  </tr>
                  <thead>
                  <tr v-for="event in events" :key="event.id">
                    <td class="align-middle fw-semibold">{{ event.title }}</td>
                    <td class="align-middle text-muted">{{ event.description || 'No description provided' }}</td>
                    <td class="align-middle">{{ event.event_date }}</td>
                    <td class="align-middle">{{ event.event_time }}</td>
                    <td class="text-end pe-4 align-middle">
                      <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditEvent(event)">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                      </button>
                      <button class="btn btn-sm action-btn delete-btn" @click="deleteEvent(event.id)">
                        <i class="bi bi-trash3 me-1"></i> Delete
                      </button>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== BOOKINGS ==================== -->
    <div v-if="activeTab === 'bookings'" class="tab-pane">
      <h4 class="mb-4 fw-bold">Booking Management</h4>

      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <button class="nav-link" :class="{ active: bookingSubTab === 'books' }" @click="bookingSubTab = 'books'">
            Borrowed Books
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" :class="{ active: bookingSubTab === 'rooms' }" @click="bookingSubTab = 'rooms'">
            Room Bookings
          </button>
        </li>
      </ul>

      <div v-if="bookingSubTab === 'books'">
        <div class="card elegant-card">
          <div class="card-header elegant-card-header">
            <h5 class="mb-0"><i class="bi bi-book me-2"></i>Borrowed Books</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="booking in activeBorrowings" :key="booking.id">
                    <td>{{ booking.user_name }}</td>
                    <td>{{ booking.book_title }}</td>
                    <td><strong>{{ booking.author }}</strong></td>
                    <td>{{ formatDate(booking.borrow_date) }}</td>
                    <td>{{ formatDate(booking.due_date) }}</td>
                    <td>
                      <span :class="getBookingStatusClass(booking.status)">
                        {{ booking.status }}
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-warning" :disabled="booking.status !== 'Overdue'" @click="sendReminder(booking)">
                        Send Reminder
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div v-if="bookingSubTab === 'rooms'">
        <div class="card elegant-card">
          <div class="card-header elegant-card-header">
            <h5 class="mb-0"><i class="bi bi-door-open me-2"></i>Active Room Bookings</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Room</th>
                    <th>Booking Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="booking in roomBookings" :key="booking.id">
                    <td>{{ booking.user_name }}</td>
                    <td>{{ booking.room_name }}</td>
                    <td>{{ formatDateTime(booking.start_time) }}</td>
                    <td>{{ formatDateTime(booking.end_time) }}</td>
                    <td>
                      <span :class="getBookingStatusClass(booking.status)">
                        {{ booking.status }}
                      </span>
                    </td>
                    <td>
                      <button v-if="canMarkRoomAvailable(booking)" class="btn btn-sm btn-success" @click="markRoomAvailable(booking)">
                        Mark as Available
                      </button>
                      <span v-else class="text-muted small">Active</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== USERS ==================== -->
    <div v-if="activeTab === 'users'" class="tab-pane mt-4">
      <div class="card elegant-card">
        <div class="card-header elegant-card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Manage Users</h5>
          <span class="badge bg-light text-dark px-3 py-2">{{ users.length }} users</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td class="fw-semibold">{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.role || 'user' }}</td>
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
    </div>

    <!-- ==================== ANNOUNCEMENTS ==================== -->
    <div v-if="activeTab === 'announcements'" class="tab-pane">
      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ editingAnnouncement ? 'Edit Announcement' : 'Create New Announcement' }}
              </h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold small">Title</label>
                  <input v-model="newAnnouncement.title" class="form-control fw-semibold small" placeholder="Announcement Title">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Type</label>
                  <select v-model="newAnnouncement.type" class="form-select fw-semibold small">
                    <option value="Notice">Notice</option>
                    <option value="Reminder">Reminder</option>
                    <option value="Event">Event</option>
                    <option value="Maintenance">Maintenance</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Message</label>
                  <textarea v-model="newAnnouncement.message" class="form-control fw-semibold small" rows="4" placeholder="Announcement message..."></textarea>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                  <button v-if="!editingAnnouncement" class="btn btn-pink flex-fill py-2" @click="addAnnouncement">
                    <i class="bi bi-plus-lg me-2"></i> Publish Announcement
                  </button>
                  <button v-else class="btn btn-success flex-fill py-2" @click="saveAnnouncementEdit">Save Changes</button>
                  <button v-if="editingAnnouncement" class="btn btn-secondary flex-fill py-2" @click="cancelEdit">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card">
            <div class="card-header elegant-card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Manage Announcements</h5>
              <span class="badge bg-light text-dark px-3 py-2">{{ announcements.length }} announcements</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Type</th>
                      <th>Date</th>
                      <th>Message</th>
                      <th class="text-end pe-4">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="ann in announcements" :key="ann.id">
                      <td class="fw-semibold">{{ ann.title }}</td>
                      <td><span class="badge" style="background-color: #E8B4B8; color: #2C2C2C;">{{ ann.type }}</span></td>
                      <td>{{ ann.date || ann.created_at }}</td>
                      <td class="text-muted small" style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ ann.message }}</td>
                      <td class="text-end pe-4">
                        <button class="btn btn-sm action-btn edit-btn me-2" @click="startEditAnnouncement(ann)">
                          <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm action-btn delete-btn" @click="deleteAnnouncement(ann.id)">
                          <i class="bi bi-trash3 me-1"></i> Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User details modal -->
    <div v-if="showUserModal" class="admin-modal-backdrop">
      <div class="admin-modal">
        <h5>User details</h5>
        <div><strong>Name:</strong> {{ selectedUser.name }}</div>
        <div><strong>Email:</strong> {{ selectedUser.email }}</div>
        <div><strong>Role:</strong> {{ selectedUser.role || 'user' }}</div>
        <div class="mt-3 text-end">
          <button class="btn btn-secondary me-2" @click="closeUserModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
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

// Existing data refs
const books = ref([])
const rooms = ref([])
const events = ref([])
const users = ref([])
const selectedUser = ref(null)
const showUserModal = ref(false)

// Announcements
const announcements = ref([])
const newAnnouncement = ref({ title: '', type: 'Notice', message: '' })
const editingAnnouncement = ref(false)

// Existing form refs
const newBook = ref({ title: '', author: '', isbn: '', category: '', publication_year: new Date().getFullYear(), copies: 1, description: '', availability_status: 'Available' })
const newRoom = ref({ name: '', capacity: '', equipment: '' })
const newEvent = ref({ title: '', event_date: '', event_time: '', description: '' })

const editingBook = ref(false)
const editingRoom = ref(false)
const editingEvent = ref(false)

// ==================== LOAD DATA ====================
const loadAllData = async () => {
  try {
    const [b, r, e] = await Promise.all([
      api.get('/books.php'),
      api.get('/rooms.php'),
      api.get('/events.php')
    ])
    books.value = b.data || []
    rooms.value = r.data || []
    events.value = e.data || []
  } catch (error) {
    console.error(error)
  }
}

const loadAnnouncements = async () => {
  try {
    const res = await api.get('/announcements.php?action=get_all')
    announcements.value = res.data || []
  } catch (error) {
    console.error('Failed to load announcements', error)
  }
}

// ==================== ANNOUNCEMENTS CRUD ====================
const addAnnouncement = async () => {
  if (!newAnnouncement.value.title || !newAnnouncement.value.message) {
    return alert('Title and Message are required')
  }
  try {
    await api.post('/announcements.php', newAnnouncement.value)
    resetAnnouncementForm()
    loadAnnouncements()
  } catch (err) {
    alert('Failed to create announcement')
  }
}

const startEditAnnouncement = (ann) => {
  newAnnouncement.value = { ...ann }
  editingAnnouncement.value = true
}

const saveAnnouncementEdit = async () => {
  try {
    await api.post('/announcements.php', newAnnouncement.value)
    resetAnnouncementForm()
    loadAnnouncements()
  } catch (err) {
    alert('Failed to update announcement')
  }
}

const deleteAnnouncement = async (id) => {
  if (!confirm('Delete this announcement?')) return
  try {
    await api.delete(`/announcements.php?id=${id}`)
    loadAnnouncements()
  } catch (err) {
    alert('Failed to delete announcement')
  }
}

const resetAnnouncementForm = () => {
  newAnnouncement.value = { title: '', type: 'Notice', message: '' }
  editingAnnouncement.value = false
}

// ==================== EXISTING CRUD ====================
const currentTabLabel = computed(() => {
  const tab = tabs.find(t => t.key === activeTab.value)
  return tab ? tab.label : 'Menu'
})

const currentTabIcon = computed(() => {
  const tab = tabs.find(t => t.key === activeTab.value)
  return tab ? tab.icon : 'bi bi-list'
})

// Books, Rooms, Events CRUD (kept short for space)
const addBook = async () => { /* ... */ }
const startEditBook = (book) => { /* ... */ }
const saveBookEdit = async () => { /* ... */ }
const deleteBook = async (id) => { /* ... */ }

const addRoom = async () => { /* ... */ }
const startEditRoom = (room) => { /* ... */ }
const saveRoomEdit = async () => { /* ... */ }
const deleteRoom = async (id) => { /* ... */ }

const addEvent = async () => { /* ... */ }
const startEditEvent = (event) => { /* ... */ }
const saveEventEdit = async () => { /* ... */ }
const deleteEvent = async (id) => { /* ... */ }

// ==================== BOOKINGS + SEND REMINDER ====================
const bookingSubTab = ref('books')
const activeBorrowings = ref([])
const roomBookings = ref([])
const API_BASE = 'http://localhost/sweetheart-library-backend/api/'

const fetchBookings = async () => {
  try {
    const res = await axios.get(API_BASE + 'bookings.php?action=get')
    if (res && res.data && res.data.success) {
      activeBorrowings.value = res.data.borrowed_books || []
      roomBookings.value = res.data.room_bookings || []
    }
  } catch (error) {
    console.error('Failed to fetch bookings:', error)
  }
}

const sendReminder = async (booking) => {
  if (!booking?.id) return alert('Booking ID is missing')
  if (!confirm(`Send overdue reminder to ${booking.user_name} for "${booking.book_title}"?`)) return

  try {
    // 1. Create Overdue Reminder Announcement
    const reminderMessage = `You have an overdue book: "${booking.book_title}". Please return it as soon as possible to avoid penalties. Due date was ${formatDate(booking.due_date)}.`

    await api.post('/announcements.php', {
      title: 'Overdue Book Reminder',
      message: reminderMessage,
      type: 'Overdue Reminder',
      due_date: booking.due_date,
      is_published: 1
    })

    // 2. (Optional) Call backend to mark as reminded
    try {
      await axios.post(API_BASE + 'bookings.php?action=send_reminder', { booking_id: booking.id })
    } catch (e) {
      // Ignore if backend action doesn't exist yet
    }

    alert('Overdue reminder sent successfully! It will appear in the user\'s Announcements.')
    fetchBookings()

  } catch (err) {
    console.error(err)
    alert('Failed to send reminder. Please try again.')
  }
}

const markRoomAvailable = async (booking) => {
  if (!booking?.id) return alert('Booking ID is missing')
  if (!confirm(`Mark room "${booking.room_name}" as Available now?`)) return
  try {
    const res = await axios.post(API_BASE + 'bookings.php?action=mark_room_available', { booking_id: booking.id })
    if (res.data?.success) {
      alert(res.data.message || 'Room marked as available!')
      fetchBookings()
    }
  } catch (err) {
    alert('Failed to update room status')
  }
}

const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-MY') : '-'
const formatDateTime = (datetime) => datetime ? new Date(datetime).toLocaleString('en-MY', { hour12: false }) : '-'

const isRoomBookingEnded = (booking) => new Date(booking.end_time) < new Date()

const getBookingStatusClass = (statusOrBooking) => {
  const status = typeof statusOrBooking === 'string' ? statusOrBooking : (statusOrBooking && statusOrBooking.status)
  if (!status) return 'badge bg-secondary'
  return status === 'Overdue' ? 'badge bg-danger' : 'badge bg-success'
}

const canMarkRoomAvailable = (booking) => isRoomBookingEnded(booking) || booking.status === 'Completed'

// Reset helpers
const resetBookForm = () => { /* ... */ }
const resetRoomForm = () => { /* ... */ }

// User management
const loadUsers = async () => { /* ... */ }
const viewUser = (u) => { /* ... */ }
const closeUserModal = () => { /* ... */ }
const deleteUser = async (id) => { /* ... */ }

// ==================== LIFECYCLE ====================
onMounted(() => {
  loadAllData()
  loadUsers()
  loadAnnouncements()
  fetchBookings()
})
</script>

<style scoped>
/* Keep existing styles */
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
  background-color: #E8B4B8;
  color: #2C2C2C;
  border-color: #E8B4B8;
  box-shadow: 0 4px 15px rgba(232, 180, 184, 0.4);
}

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

.delete-btn {
  background-color: #F8D7DA;
  color: #842029;
}

.dropdown-menu {
  border-radius: 12px;
  padding: 8px 0;
}

.dropdown-item {
  padding: 10px 16px;
  font-weight: 500;
}

.admin-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.admin-modal {
  background: white;
  padding: 20px;
  border-radius: 12px;
  width: 320px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.2);
}
</style>