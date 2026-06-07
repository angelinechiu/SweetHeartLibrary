<template>
  <div class="admin-dashboard p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fw-bold">Admin Dashboard</h1>
        <p class="text-muted mb-0">SweetHeart Library Management</p>
      </div>
      <span class="badge px-4 py-2" style="background-color: #E8B4B8; color: #2C2C2C;">ADMIN</span>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills elegant-tabs mb-4">
      <li class="nav-item" v-for="tab in tabs" :key="tab.key">
        <button class="nav-link" :class="{ active: activeTab === tab.key }" @click="activeTab = tab.key">
          {{ tab.label }}
        </button>
      </li>
    </ul>

    <!-- ==================== BOOKS TAB ==================== -->
    <div v-if="activeTab === 'books'">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Books Management</h4>
        <button class="btn btn-pink" @click="openBookModal()">+ Add New Book</button>
      </div>

      <!-- Search -->
      <div class="mb-3">
        <input v-model="bookSearch" type="text" class="form-control" placeholder="Search books by title, author, or ISBN...">
      </div>

      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Category</th>
                <th>Year</th>
                <th>Copies</th>
                <th>Featured</th>
                <th>Popular</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="book in filteredBooks" :key="book.id">
                <td class="fw-semibold">{{ book.title }}</td>
                <td>{{ book.author }}</td>
                <td><code>{{ book.isbn }}</code></td>
                <td>{{ book.category }}</td>
                <td>{{ book.year }}</td>                    <!-- Fixed -->
                <td>{{ book.available_copies }} / {{ book.total_copies }}</td> <!-- Fixed -->
                <td>
                  <span class="badge" :class="book.is_featured ? 'bg-success' : 'bg-secondary'">
                    {{ book.is_featured ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td>
                  <span class="badge" :class="book.is_popular ? 'bg-warning text-dark' : 'bg-secondary'">
                    {{ book.is_popular ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openBookModal(book)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteBook(book.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== ROOMS TAB ==================== -->
    <div v-if="activeTab === 'rooms'">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Rooms Management</h4>
        <button class="btn btn-pink" @click="openRoomModal()">+ Add New Room</button>
      </div>

      <div class="mb-3">
        <input v-model="roomSearch" type="text" class="form-control" placeholder="Search rooms...">
      </div>

      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Room Name</th>
                <th>Capacity</th>
                <th>Equipment</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="room in filteredRooms" :key="room.id">
                <td class="fw-semibold">{{ room.name }}</td>
                <td>
                  <span class="badge bg-pink text-dark">
                    {{ room.capacity }} seats
                  </span>
                </td>
                <td>{{ room.facilities || room.equipment || '-' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openRoomModal(room)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteRoom(room.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== EVENTS TAB ==================== -->
    <div v-if="activeTab === 'events'">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Events Management</h4>
        <button class="btn btn-pink" @click="openEventModal()">+ Create Event</button>
      </div>

      <div class="mb-3">
        <input v-model="eventSearch" type="text" class="form-control" placeholder="Search events...">
      </div>

      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in filteredEvents" :key="event.id">
                <td class="fw-semibold">{{ event.title }}</td>
                <td>{{ event.event_date }}</td>
                <td>{{ event.event_time }}</td>
                <td class="text-muted">{{ event.description || '-' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openEventModal(event)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteEvent(event.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== BOOKINGS TAB ==================== -->
    <div v-if="activeTab === 'bookings'">
      <h4 class="mb-3">Bookings Management</h4>

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

      <!-- Borrowed Books -->
      <div v-if="bookingSubTab === 'books'">
        <div class="mb-3">
          <input v-model="borrowedSearch" type="text" class="form-control" placeholder="Search borrowed books...">
        </div>
        <div class="card elegant-card">
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Book</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="b in filteredBorrowed" :key="b.id">
                  <td>{{ b.user_name || 'N/A' }}</td>
                  <td class="fw-semibold">{{ b.book_title }}</td>
                  <td>{{ formatDate(b.due_date) }}</td>
                  <td>
                    <span class="badge" :class="getStatusClass(b.status)">{{ b.status }}</span>
                  </td>
                  <td class="text-end">
                    <button v-if="b.status !== 'Returned'" class="btn btn-sm btn-success me-1" @click="markBookReturned(b)">
                      Mark Returned
                    </button>
                    <button v-if="b.status === 'Overdue'" class="btn btn-sm btn-warning" @click="sendReminder(b)">
                      Send Reminder
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
        <div class="mb-3">
          <input v-model="roomBookingSearch" type="text" class="form-control" placeholder="Search room bookings...">
        </div>
        <div class="card elegant-card">
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Room</th>
                  <th>Start Time</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="rb in filteredRoomBookings" :key="rb.id">
                  <td>{{ rb.user_name || 'N/A' }}</td>
                  <td class="fw-semibold">{{ rb.room_name }}</td>
                  <td>{{ formatDateTime(rb.start_time) }}</td>
                  <td><span class="badge" :class="getStatusClass(rb.status)">{{ rb.status }}</span></td>
                  <td class="text-end">
                    <button v-if="canMarkAvailable(rb)" class="btn btn-sm btn-success" @click="markRoomAvailable(rb)">
                      Mark Completed
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== USERS TAB ==================== -->
    <div v-if="activeTab === 'users'">
      <h4 class="mb-3">Users Management</h4>
      <div class="mb-3">
        <input v-model="userSearch" type="text" class="form-control" placeholder="Search users...">
      </div>
      <div class="card elegant-card">
        <div class="card-body p-0">
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
              <tr v-for="user in filteredUsers" :key="user.id">
                <td class="fw-semibold">{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <span class="badge" :class="getRoleBadgeClass(user.role)">
                    {{ user.role || 'User' }}
                  </span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" @click="viewUser(user)">View</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== ANNOUNCEMENTS TAB ==================== -->
    <div v-if="activeTab === 'announcements'">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Announcements</h4>
        <button class="btn btn-pink" @click="openAnnouncementModal()">+ New Announcement</button>
      </div>

      <div class="mb-3">
        <input v-model="announcementSearch" type="text" class="form-control" placeholder="Search announcements...">
      </div>

      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Date</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ann in filteredAnnouncements" :key="ann.id">
                <td class="fw-semibold">{{ ann.title }}</td>
                <td>
                  <span class="badge" :class="getTypeBadgeClass(ann.type)">
                    {{ ann.type }}
                  </span>
                </td>
                <td class="text-muted">{{ formatDate(ann.created_at) }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openAnnouncementModal(ann)">Edit</button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteAnnouncement(ann.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Book Modal -->
    <div class="modal fade" id="bookModal" tabindex="-1" ref="bookModalRef">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingBook ? 'Edit Book' : 'Add New Book' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6"><label>Title</label><input v-model="currentBook.title" class="form-control"></div>
              <div class="col-md-6"><label>Author</label><input v-model="currentBook.author" class="form-control"></div>
              <div class="col-md-6"><label>ISBN</label><input v-model="currentBook.isbn" class="form-control"></div>
              <div class="col-md-6"><label>Category</label><input v-model="currentBook.category" class="form-control"></div>

              <!-- Fixed: year -->
              <div class="col-md-4">
                <label>Year</label>
                <input v-model.number="currentBook.year" type="number" class="form-control">
              </div>

              <!-- Fixed: available_copies -->
              <div class="col-md-4">
                <label>Available Copies</label>
                <input v-model.number="currentBook.available_copies" type="number" class="form-control">
              </div>

              <!-- Fixed: total_copies -->
              <div class="col-md-4">
                <label>Total Copies</label>
                <input v-model.number="currentBook.total_copies" type="number" class="form-control">
              </div>

              <div class="col-md-4">
                <label>Featured</label>
                <select v-model="currentBook.is_featured" class="form-select">
                  <option :value="1">Yes</option>
                  <option :value="0">No</option>
                </select>
              </div>
              <div class="col-md-4">
                <label>Popular</label>
                <select v-model="currentBook.is_popular" class="form-select">
                  <option :value="1">Yes</option>
                  <option :value="0">No</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveBook">{{ editingBook ? 'Save Changes' : 'Add Book' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Room Modal -->
    <div class="modal fade" id="roomModal" tabindex="-1" ref="roomModalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingRoom ? 'Edit Room' : 'Add New Room' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3"><label>Room Name</label><input v-model="currentRoom.name" class="form-control"></div>
            <div class="mb-3"><label>Capacity</label><input v-model.number="currentRoom.capacity" type="number" class="form-control"></div>
            <div class="mb-3"><label>Facilities</label><textarea v-model="currentRoom.facilities" class="form-control" rows="3"
                placeholder="Projector, Whiteboard, Air Conditioner, etc."></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveRoom">{{ editingRoom ? 'Save' : 'Add Room' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" ref="eventModalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingEvent ? 'Edit Event' : 'Create New Event' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3"><label>Event Title</label><input v-model="currentEvent.title" class="form-control"></div>
            <div class="row g-3">
              <div class="col-md-6"><label>Date</label><input v-model="currentEvent.event_date" type="date" class="form-control"></div>
              <div class="col-md-6"><label>Time</label><input v-model="currentEvent.event_time" type="time" class="form-control"></div>
            </div>
            <div class="mt-3"><label>Description</label><textarea v-model="currentEvent.description" class="form-control" rows="3"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveEvent">{{ editingEvent ? 'Save Changes' : 'Create Event' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- User View Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" ref="userModalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">User Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" v-if="selectedUser">
            <p><strong>Name:</strong> {{ selectedUser.name }}</p>
            <p><strong>Email:</strong> {{ selectedUser.email }}</p>
            <p><strong>Role:</strong> {{ selectedUser.role || 'User' }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Announcement Modal -->
    <div class="modal fade" id="announcementModal" tabindex="-1" ref="announcementModalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingAnnouncement ? 'Edit Announcement' : 'Create New Announcement' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3"><label>Title</label><input v-model="currentAnnouncement.title" class="form-control"></div>
            <div class="mb-3"><label>Type</label>
              <select v-model="currentAnnouncement.type" class="form-select">
                <option value="Notice">Notice</option>
                <option value="Event">Event</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>
            <div class="mb-3"><label>Message</label><textarea v-model="currentAnnouncement.message" class="form-control" rows="4"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveAnnouncement">{{ editingAnnouncement ? 'Save Changes' : 'Publish' }}</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import api from '../services/api.js'
import { toast } from 'vue3-toastify'
import * as bootstrap from 'bootstrap'

const activeTab = ref('books')
const tabs = [
  { key: 'books', label: 'Books' },
  { key: 'rooms', label: 'Rooms' },
  { key: 'events', label: 'Events' },
  { key: 'bookings', label: 'Bookings' },
  { key: 'users', label: 'Users' },
  { key: 'announcements', label: 'Announcements' }
]

const books = ref([])
const rooms = ref([])
const events = ref([])
const users = ref([])
const announcements = ref([])
const activeBorrowings = ref([])
const roomBookings = ref([])

// Search states
const bookSearch = ref('')
const roomSearch = ref('')
const eventSearch = ref('')
const userSearch = ref('')
const announcementSearch = ref('')
const borrowedSearch = ref('')
const roomBookingSearch = ref('')

const bookingSubTab = ref('books')

// Modal states
const editingBook = ref(false)
const currentBook = ref({})
const bookModalRef = ref(null)
let bookModalInstance = null

const editingRoom = ref(false)
const currentRoom = ref({})
const roomModalRef = ref(null)
let roomModalInstance = null

const editingEvent = ref(false)
const currentEvent = ref({})
const eventModalRef = ref(null)
let eventModalInstance = null

const selectedUser = ref(null)
const userModalRef = ref(null)
let userModalInstance = null

const editingAnnouncement = ref(false)
const currentAnnouncement = ref({})
const announcementModalRef = ref(null)
let announcementModalInstance = null

// Computed filtered lists
const filteredBooks = computed(() => {
  if (!bookSearch.value) return books.value
  const q = bookSearch.value.toLowerCase()
  return books.value.filter(b =>
    (b.title || '').toLowerCase().includes(q) ||
    (b.author || '').toLowerCase().includes(q) ||
    (b.isbn || '').toLowerCase().includes(q)
  )
})

const getTypeBadgeClass = (type) => {
  if (type === 'Notice') return 'bg-info text-white'
  if (type === 'Event') return 'bg-success text-white'
  if (type === 'Maintenance') return 'bg-warning text-dark'
  return 'bg-secondary text-white'
}

const getRoleBadgeClass = (role) => {
  if (role === 'admin') return 'bg-danger text-white'
  if (role === 'staff') return 'bg-warning text-dark'
  return 'bg-pink text-dark'   // default for normal users
}

const filteredRooms = computed(() => roomSearch.value
  ? rooms.value.filter(r => (r.name || '').toLowerCase().includes(roomSearch.value.toLowerCase()))
  : rooms.value)

const filteredEvents = computed(() => eventSearch.value
  ? events.value.filter(e => (e.title || '').toLowerCase().includes(eventSearch.value.toLowerCase()))
  : events.value)

const filteredUsers = computed(() => userSearch.value
  ? users.value.filter(u => (u.name || '').toLowerCase().includes(userSearch.value.toLowerCase()) || (u.email || '').toLowerCase().includes(userSearch.value.toLowerCase()))
  : users.value)

const filteredAnnouncements = computed(() => announcementSearch.value
  ? announcements.value.filter(a => (a.title || '').toLowerCase().includes(announcementSearch.value.toLowerCase()))
  : announcements.value)

const filteredBorrowed = computed(() => borrowedSearch.value
  ? activeBorrowings.value.filter(b =>
      (b.book_title || '').toLowerCase().includes(borrowedSearch.value.toLowerCase()) ||
      (b.user_name || '').toLowerCase().includes(borrowedSearch.value.toLowerCase()))
  : activeBorrowings.value)

const filteredRoomBookings = computed(() => roomBookingSearch.value
  ? roomBookings.value.filter(rb =>
      (rb.room_name || '').toLowerCase().includes(roomBookingSearch.value.toLowerCase()) ||
      (rb.user_name || '').toLowerCase().includes(roomBookingSearch.value.toLowerCase()))
  : roomBookings.value)

// Load all data
const loadAllData = async () => {
  try {
    const [b, r, e, u, a, bookingsRes] = await Promise.all([
      api.get('/books.php'),
      api.get('/rooms.php'),
      api.get('/events.php'),
      api.get('/users.php'),
      api.get('/announcements.php?action=get_all'),
      api.get('/bookings.php?action=get')
    ])

    books.value = Array.isArray(b.data) ? b.data : (b.data?.data || [])
    rooms.value = Array.isArray(r.data) ? r.data : (r.data?.data || [])
    events.value = Array.isArray(e.data) ? e.data : (e.data?.data || [])
    users.value = Array.isArray(u.data) ? u.data : (u.data?.data || [])
    announcements.value = Array.isArray(a.data) ? a.data : (a.data?.data || [])

    if (bookingsRes.data?.success) {
      activeBorrowings.value = bookingsRes.data.borrowed_books || []
      roomBookings.value = bookingsRes.data.room_bookings || []
    }
  } catch (error) {
    console.error('Error loading data:', error)
    toast.error('Failed to load data from database')
  }
}

// ==================== BOOK ====================
const openBookModal = (book = null) => {
  if (book) {
    currentBook.value = {
      id: book.id,
      title: book.title || '',
      author: book.author || '',
      isbn: book.isbn || '',
      category: book.category || '',
      year: book.year || new Date().getFullYear(),
      available_copies: book.available_copies || 0,
      total_copies: book.total_copies || 0,
      is_featured: book.is_featured || 0,
      is_popular: book.is_popular || 0
    }
    editingBook.value = true
  } else {
    currentBook.value = {
      title: '',
      author: '',
      isbn: '',
      category: '',
      year: new Date().getFullYear(),
      available_copies: 3,
      total_copies: 3,
      is_featured: 0,
      is_popular: 0
    }
    editingBook.value = false
  }

  nextTick(() => {
    if (!bookModalInstance && bookModalRef.value) {
      bookModalInstance = new bootstrap.Modal(bookModalRef.value)
    }
    bookModalInstance?.show()
  })
}

const saveBook = async () => {
  try {
    const response = await api.post('/books.php', currentBook.value)
    console.log('Book save response:', response.data)

    if (response.data?.success) {
      toast.success(editingBook.value ? 'Book updated!' : 'Book added!')
      bookModalInstance?.hide()
      await loadAllData()
    } else {
      toast.error(response.data?.message || 'Failed to save book')
    }
  } catch (error) {
    console.error('Book save error:', error)
    toast.error('Failed to save book. Check console for details.')
  }
}

const deleteBook = async (id) => {
  if (!confirm('Delete this book?')) return
  try {
    await api.delete(`/books.php?id=${id}`)
    toast.success('Book deleted')
    await loadAllData()
  } catch (error) {
    console.error('Error deleting book:', error)
    toast.error('Delete failed')
  }
}

// ==================== ROOM ====================
const openRoomModal = (room = null) => {
  if (room) {
    currentRoom.value = {
      id: room.id,
      name: room.name || '',
      capacity: room.capacity || 4,
      facilities: room.facilities || room.equipment || ''
    }
    editingRoom.value = true
  } else {
    currentRoom.value = {
      name: '',
      capacity: 4,
      facilities: ''
    }
    editingRoom.value = false
  }

  nextTick(() => {
    if (!roomModalInstance && roomModalRef.value) {
      roomModalInstance = new bootstrap.Modal(roomModalRef.value)
    }
    roomModalInstance?.show()
  })
}

const saveRoom = async () => {
  try {
    const response = await api.post('/rooms.php', currentRoom.value)
    console.log('Room save response:', response.data)

    if (response.data?.success) {
      toast.success(editingRoom.value ? 'Room updated!' : 'Room added!')
      roomModalInstance?.hide()
      await loadAllData()
    } else {
      toast.error(response.data?.message || 'Failed to save room')
    }
  } catch (error) {
    console.error('Room save error:', error)
    toast.error('Failed to save room')
  }
}

const deleteRoom = async (id) => {
  if (!confirm('Delete this room?')) return
  try {
    await api.delete(`/rooms.php?id=${id}`)
    toast.success('Room deleted')
    await loadAllData()
  } catch (error) {
    console.error('Error deleting room:', error)
    toast.error('Delete failed')
  }
}

// ==================== EVENT ====================
const openEventModal = (eventItem = null) => {
  if (eventItem) {
    currentEvent.value = { ...eventItem }
    editingEvent.value = true
  } else {
    currentEvent.value = { title: '', event_date: '', event_time: '', description: '' }
    editingEvent.value = false
  }
  nextTick(() => {
    if (!eventModalInstance && eventModalRef.value) eventModalInstance = new bootstrap.Modal(eventModalRef.value)
    eventModalInstance?.show()
  })
}

const saveEvent = async () => {
  try {
    await api.post('/events.php', currentEvent.value)
    toast.success(editingEvent.value ? 'Event updated!' : 'Event created!')
    eventModalInstance?.hide()
    await loadAllData()
  } catch (error) {
    console.error('Error saving event:', error)
    toast.error('Failed to save event')
  }
}

const deleteEvent = async (id) => {
  if (!confirm('Delete this event?')) return
  try {
    await api.delete(`/events.php?id=${id}`)
    toast.success('Event deleted')
    await loadAllData()
  } catch (error) {
    console.error('Error deleting event:', error)
    toast.error('Delete failed')
  }
}

const viewUser = (user) => {
  selectedUser.value = user

  nextTick(() => {
    if (!userModalInstance && userModalRef.value) {
      userModalInstance = new bootstrap.Modal(userModalRef.value)
    }
    userModalInstance?.show()
  })
}

// ==================== ANNOUNCEMENT ====================
const openAnnouncementModal = (ann = null) => {
  if (ann) {
    currentAnnouncement.value = { ...ann }
    editingAnnouncement.value = true
  } else {
    currentAnnouncement.value = { title: '', type: 'Notice', message: '' }
    editingAnnouncement.value = false
  }
  nextTick(() => {
    if (!announcementModalInstance && announcementModalRef.value) announcementModalInstance = new bootstrap.Modal(announcementModalRef.value)
    announcementModalInstance?.show()
  })
}

const saveAnnouncement = async () => {
  try {
    const payload = { ...currentAnnouncement.value }
    if (editingAnnouncement.value) payload.action = 'update'
    await api.post('/announcements.php', payload)
    toast.success(editingAnnouncement.value ? 'Announcement updated!' : 'Announcement published!')
    announcementModalInstance?.hide()
    await loadAllData()
  } catch (error) {
    console.error('Error saving announcement:', error)
    toast.error('Failed to save announcement')
  }
}

const deleteAnnouncement = async (id) => {
  if (!confirm('Delete this announcement?')) return
  try {
    await api.delete(`/announcements.php?id=${id}`)
    toast.success('Announcement deleted')
    await loadAllData()
  } catch (error) {
    console.error('Error deleting announcement:', error)
    toast.error('Delete failed')
  }
}

// ==================== BOOKINGS ACTIONS ====================
const markBookReturned = async (booking) => {
  if (!confirm(`Mark "${booking.book_title}" as returned?`)) return
  try {
    await api.post('/bookings.php?action=mark_returned', { booking_id: booking.id })
    toast.success('Book marked as returned!')
    await loadAllData()
  } catch (error) {
    console.error('Error marking book as returned:', error)
    toast.error('Failed')
  }
}

const sendReminder = async (booking) => {
  if (!confirm(`Send reminder for "${booking.book_title}"?`)) return
  try {
    await api.post('/announcements.php', {
      title: 'Overdue Book Reminder',
      message: `You have an overdue book: "${booking.book_title}". Please return it soon.`,
      type: 'Reminder'
    })
    toast.success('Reminder sent!')
    await loadAllData()
  } catch (error) {
    console.error('Error sending reminder:', error)
    toast.error('Failed to send reminder')
  }
}

const markRoomAvailable = async (booking) => {
  if (!confirm('Mark this room booking as completed?')) return
  try {
    await api.post('/bookings.php?action=mark_room_available', { booking_id: booking.id })
    toast.success('Room marked as completed!')
    await loadAllData()
  } catch (error) {
    console.error('Error marking room as available:', error)
    toast.error('Failed')
  }
}

const canMarkAvailable = (b) => ['Active', 'Pending'].includes(b.status)

// ==================== HELPERS ====================
const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-MY') : '-'
const formatDateTime = (d) => d ? new Date(d).toLocaleString('en-MY') : '-'
const getStatusClass = (status) => {
  if (status === 'Overdue') return 'bg-danger text-white'
  if (status === 'Returned' || status === 'Completed') return 'bg-success text-white'
  return 'bg-warning text-dark'
}

onMounted(() => {
  loadAllData()
})

</script>

<style scoped>
.badge {
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
}
.elegant-tabs .nav-link {
  color: #2C2C2C;
  font-weight: 600;
  border-radius: 50px;
  border: 2px solid #E8B4B8;
}
.elegant-tabs .nav-link.active {
  background-color: #E8B4B8 !important;
  color: #2C2C2C !important;
  border-color: #E8B4B8;
}
.elegant-card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  border: none;
  font-weight: 600;
}
</style>
