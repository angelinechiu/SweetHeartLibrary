<template>
  <div class="admin-dashboard p-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.5rem;">Admin Dashboard</h1>
        <p class="text-muted mb-0 fs-5">Manage your SweetHeart Library</p>
      </div>
      <span class="badge px-4 py-2 fs-6" style="background-color: #E8B4B8; color: #2C2C2C; font-weight: 700; border-radius: 50px;">
        <i class="bi bi-shield-check me-1"></i> ADMIN
      </span>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-icon bg-pink"><i class="bi bi-book-fill"></i></div>
          <div class="stat-info"><div class="stat-value">{{ stats.totalBooks }}</div><div class="stat-label">Books</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-icon bg-info"><i class="bi bi-door-open-fill"></i></div>
          <div class="stat-info"><div class="stat-value">{{ stats.totalRooms }}</div><div class="stat-label">Rooms</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-icon bg-success"><i class="bi bi-people-fill"></i></div>
          <div class="stat-info"><div class="stat-value">{{ stats.totalUsers }}</div><div class="stat-label">Users</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-icon bg-warning"><i class="bi bi-calendar-check-fill"></i></div>
          <div class="stat-info"><div class="stat-value">{{ stats.activeBookings }}</div><div class="stat-label">Active Bookings</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-icon bg-purple"><i class="bi bi-megaphone-fill"></i></div>
          <div class="stat-info"><div class="stat-value">{{ stats.totalAnnouncements }}</div><div class="stat-label">Announcements</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
          <div class="stat-info"><div class="stat-value">{{ stats.overdueBooks }}</div><div class="stat-label">Overdue</div></div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-4">
      <ul class="nav nav-pills elegant-admin-tabs flex-wrap">
        <li class="nav-item" v-for="tab in tabs" :key="tab.key">
          <button class="nav-link d-flex align-items-center gap-2" :class="{ active: activeTab === tab.key }" @click="activeTab = tab.key">
            <i :class="tab.icon"></i>
            <span class="d-none d-sm-inline">{{ tab.label }}</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- ==================== BOOKS TAB ==================== -->
    <div v-if="activeTab === 'books'" class="tab-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-book me-2"></i>Books Management</h4>
        <button class="btn btn-pink" @click="openBookModal()"><i class="bi bi-plus-lg me-1"></i> Add New Book</button>
      </div>
      <div class="mb-3">
        <input v-model="bookSearch" type="text" class="form-control" placeholder="Search books by title, author, ISBN...">
      </div>
      <div class="card elegant-card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Year</th><th>Copies</th><th>Status</th><th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="book in filteredBooks" :key="book.id">
                  <td class="fw-semibold">{{ book.title }}</td>
                  <td>{{ book.author }}</td>
                  <td><code>{{ book.isbn }}</code></td>
                  <td><span class="badge bg-light text-dark">{{ book.category }}</span></td>
                  <td>{{ book.publication_year }}</td>
                  <td>{{ book.copies }}</td>
                  <td><span class="badge" :class="getStatusBadge(book.availability_status)">{{ book.availability_status }}</span></td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-warning me-1" @click="openBookModal(book)"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteBook(book.id)"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== ROOMS TAB ==================== -->
    <div v-if="activeTab === 'rooms'" class="tab-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-door-open me-2"></i>Rooms Management</h4>
        <button class="btn btn-pink" @click="openRoomModal()"><i class="bi bi-plus-lg me-1"></i> Add New Room</button>
      </div>
      <div class="mb-3">
        <input v-model="roomSearch" type="text" class="form-control" placeholder="Search rooms...">
      </div>
      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr><th>Room Name</th><th>Capacity</th><th>Equipment</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="room in filteredRooms" :key="room.id">
                <td class="fw-semibold">{{ room.name }}</td>
                <td><span class="badge bg-pink text-dark">{{ room.capacity }} seats</span></td>
                <td>{{ room.equipment || '-' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openRoomModal(room)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteRoom(room.id)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== EVENTS TAB ==================== -->
    <div v-if="activeTab === 'events'" class="tab-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Events Management</h4>
        <button class="btn btn-pink" @click="openEventModal()"><i class="bi bi-plus-lg me-1"></i> Create Event</button>
      </div>
      <div class="mb-3">
        <input v-model="eventSearch" type="text" class="form-control" placeholder="Search events...">
      </div>
      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr><th>Title</th><th>Date</th><th>Time</th><th>Description</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="event in filteredEvents" :key="event.id">
                <td class="fw-semibold">{{ event.title }}</td>
                <td>{{ event.event_date }}</td>
                <td>{{ event.event_time }}</td>
                <td class="text-muted small">{{ event.description || '-' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openEventModal(event)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteEvent(event.id)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== BOOKINGS TAB ==================== -->
    <div v-if="activeTab === 'bookings'" class="tab-content">
      <h4 class="mb-3"><i class="bi bi-calendar-check me-2"></i>Bookings Management</h4>
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><button class="nav-link" :class="{ active: bookingSubTab === 'books' }" @click="bookingSubTab = 'books'">Borrowed Books</button></li>
        <li class="nav-item"><button class="nav-link" :class="{ active: bookingSubTab === 'rooms' }" @click="bookingSubTab = 'rooms'">Room Bookings</button></li>
      </ul>

      <!-- Borrowed Books -->
      <div v-if="bookingSubTab === 'books'">
        <div class="mb-3"><input v-model="borrowedSearch" type="text" class="form-control" placeholder="Search by user or book..."></div>
        <div class="card elegant-card">
          <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
              <thead><tr><th>User</th><th>Book</th><th>Due Date</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
              <tbody>
                <tr v-for="b in filteredBorrowedBooks" :key="b.id">
                  <td>{{ b.user_name }}</td>
                  <td class="fw-semibold">{{ b.book_title }}</td>
                  <td class="text-muted small">{{ formatDate(b.due_date) }}</td>
                  <td><span class="badge" :class="getBookingStatusClass(b.status)">{{ b.status }}</span></td>
                  <td class="text-end">
                    <button v-if="b.status !== 'Returned'" class="btn btn-sm btn-success me-1" @click="markBookReturned(b)">Mark Returned</button>
                    <button v-if="b.status === 'Overdue'" class="btn btn-sm btn-warning" @click="sendReminder(b)">Send Reminder</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Room Bookings -->
      <div v-if="bookingSubTab === 'rooms'">
        <div class="mb-3"><input v-model="roomBookingSearch" type="text" class="form-control" placeholder="Search room bookings..."></div>
        <div class="card elegant-card">
          <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
              <thead><tr><th>User</th><th>Room</th><th>Start Time</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
              <tbody>
                <tr v-for="rb in filteredRoomBookings" :key="rb.id">
                  <td>{{ rb.user_name }}</td>
                  <td class="fw-semibold">{{ rb.room_name }}</td>
                  <td class="text-muted small">{{ formatDateTime(rb.start_time) }}</td>
                  <td><span class="badge" :class="getBookingStatusClass(rb.status)">{{ rb.status }}</span></td>
                  <td class="text-end">
                    <button v-if="canMarkRoomAvailable(rb)" class="btn btn-sm btn-success" @click="markRoomAvailable(rb)">Mark Available</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== USERS TAB ==================== -->
    <div v-if="activeTab === 'users'" class="tab-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-people me-2"></i>Users Management</h4>
      </div>
      <div class="mb-3"><input v-model="userSearch" type="text" class="form-control" placeholder="Search users..."></div>
      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id">
                <td class="fw-semibold">{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td><span class="badge bg-pink text-dark">{{ user.role || 'User' }}</span></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" @click="viewUser(user)">View</button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteUser(user.id)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== ANNOUNCEMENTS TAB ==================== -->
    <div v-if="activeTab === 'announcements'" class="tab-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-megaphone me-2"></i>Announcements</h4>
        <button class="btn btn-pink" @click="openAnnouncementModal()"><i class="bi bi-plus-lg me-1"></i> New Announcement</button>
      </div>
      <div class="mb-3"><input v-model="announcementSearch" type="text" class="form-control" placeholder="Search announcements..."></div>
      <div class="card elegant-card">
        <div class="card-body p-0">
          <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Title</th><th>Type</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr v-for="ann in filteredAnnouncements" :key="ann.id">
                <td class="fw-semibold">{{ ann.title }}</td>
                <td><span class="badge bg-pink text-dark">{{ ann.type }}</span></td>
                <td class="text-muted small">{{ formatDate(ann.created_at) }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-warning me-1" @click="openAnnouncementModal(ann)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteAnnouncement(ann.id)"><i class="bi bi-trash"></i></button>
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
              <div class="col-md-6"><label class="form-label">Title</label><input v-model="currentBook.title" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Author</label><input v-model="currentBook.author" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">ISBN</label><input v-model="currentBook.isbn" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Category</label><input v-model="currentBook.category" class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Year</label><input v-model.number="currentBook.publication_year" type="number" class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Copies</label><input v-model.number="currentBook.copies" type="number" class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Status</label>
                <select v-model="currentBook.availability_status" class="form-select">
                  <option value="Available">Available</option><option value="Borrowed">Borrowed</option><option value="Reserved">Reserved</option>
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
            <div class="mb-3"><label class="form-label">Room Name</label><input v-model="currentRoom.name" class="form-control" placeholder="Meeting Room A"></div>
            <div class="mb-3"><label class="form-label">Capacity</label><input v-model.number="currentRoom.capacity" type="number" class="form-control" placeholder="10"></div>
            <div class="mb-3"><label class="form-label">Equipment</label><input v-model="currentRoom.equipment" class="form-control" placeholder="Projector, Whiteboard"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveRoom">{{ editingRoom ? 'Save Changes' : 'Add Room' }}</button>
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
            <div class="mb-3"><label class="form-label">Event Title</label><input v-model="currentEvent.title" class="form-control"></div>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Date</label><input v-model="currentEvent.event_date" type="date" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Time</label><input v-model="currentEvent.event_time" type="time" class="form-control"></div>
            </div>
            <div class="mt-3"><label class="form-label">Description</label><textarea v-model="currentEvent.description" class="form-control" rows="3"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveEvent">{{ editingEvent ? 'Save Changes' : 'Create Event' }}</button>
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
            <div class="mb-3"><label class="form-label">Title</label><input v-model="currentAnnouncement.title" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Type</label>
              <select v-model="currentAnnouncement.type" class="form-select">
                <option value="Notice">Notice</option>
                <option value="Event">Event</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>
            <div class="mb-3"><label class="form-label">Message</label><textarea v-model="currentAnnouncement.message" class="form-control" rows="4"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-pink" @click="saveAnnouncement">{{ editingAnnouncement ? 'Save Changes' : 'Publish' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- User View Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" ref="userModalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">User Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body" v-if="selectedUser">
            <p><strong>Name:</strong> {{ selectedUser.name }}</p>
            <p><strong>Email:</strong> {{ selectedUser.email }}</p>
            <p><strong>Role:</strong> {{ selectedUser.role || 'User' }}</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import api from '../services/api.js'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import * as bootstrap from 'bootstrap'

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
const announcements = ref([])
const activeBorrowings = ref([])
const roomBookings = ref([])

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

const editingAnnouncement = ref(false)
const currentAnnouncement = ref({})
const announcementModalRef = ref(null)
let announcementModalInstance = null

const selectedUser = ref(null)
const userModalRef = ref(null)
let userModalInstance = null

const stats = ref({
  totalBooks: 0, totalRooms: 0, totalUsers: 0,
  activeBookings: 0, totalAnnouncements: 0, overdueBooks: 0
})

// ==================== COMPUTED ====================
const filteredBooks = computed(() => {
  if (!bookSearch.value) return books.value
  const q = bookSearch.value.toLowerCase()
  return books.value.filter(b =>
    (b.title || '').toLowerCase().includes(q) ||
    (b.author || '').toLowerCase().includes(q) ||
    (b.isbn || '').toLowerCase().includes(q)
  )
})

const filteredRooms = computed(() => roomSearch.value ? rooms.value.filter(r => (r.name || '').toLowerCase().includes(roomSearch.value.toLowerCase())) : rooms.value)
const filteredEvents = computed(() => eventSearch.value ? events.value.filter(e => (e.title || '').toLowerCase().includes(eventSearch.value.toLowerCase())) : events.value)
const filteredUsers = computed(() => userSearch.value ? users.value.filter(u => (u.name || '').toLowerCase().includes(userSearch.value.toLowerCase()) || (u.email || '').toLowerCase().includes(userSearch.value.toLowerCase())) : users.value)
const filteredAnnouncements = computed(() => announcementSearch.value ? announcements.value.filter(a => (a.title || '').toLowerCase().includes(announcementSearch.value.toLowerCase())) : announcements.value)
const filteredBorrowedBooks = computed(() => borrowedSearch.value ? activeBorrowings.value.filter(b => (b.user_name || '').toLowerCase().includes(borrowedSearch.value.toLowerCase()) || (b.book_title || '').toLowerCase().includes(borrowedSearch.value.toLowerCase())) : activeBorrowings.value)
const filteredRoomBookings = computed(() => roomBookingSearch.value ? roomBookings.value.filter(rb => (rb.user_name || '').toLowerCase().includes(roomBookingSearch.value.toLowerCase()) || (rb.room_name || '').toLowerCase().includes(roomBookingSearch.value.toLowerCase())) : roomBookings.value)

// ==================== HELPERS ====================
const getStatusBadge = (status) => status === 'Available' ? 'bg-success text-white' : status === 'Borrowed' ? 'bg-warning text-dark' : 'bg-secondary text-white'
const getBookingStatusClass = (status) => status === 'Overdue' ? 'bg-danger text-white' : (status === 'Active' || status === 'Borrowed') ? 'bg-success text-white' : 'bg-secondary text-white'
const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-MY') : '-'
const formatDateTime = (d) => d ? new Date(d).toLocaleString('en-MY') : '-'
const canMarkRoomAvailable = (b) => ['Active', 'Pending', null, undefined].includes(b.status)

// ==================== LOAD DATA ====================
const loadAllData = async () => {
  try {
    const [b, r, e, u, a] = await Promise.all([
      api.get('/books.php'),
      api.get('/rooms.php'),
      api.get('/events.php'),
      api.get('/users.php'),
      api.get('/announcements.php?action=get_all')
    ])
    books.value = b.data || []
    rooms.value = r.data || []
    events.value = e.data || []
    users.value = u.data || []
    announcements.value = a.data || []

    stats.value.totalBooks = books.value.length
    stats.value.totalRooms = rooms.value.length
    stats.value.totalUsers = users.value.length
    stats.value.totalAnnouncements = announcements.value.length
  } catch (err) {
    console.error('Error loading data:', err)
    toast.error('Failed to load data')
  }
}

const fetchBookings = async () => {
  try {
    const res = await api.get('/bookings.php?action=get')
    if (res.data?.success) {
      activeBorrowings.value = res.data.borrowed_books || []
      roomBookings.value = res.data.room_bookings || []
      stats.value.overdueBooks = activeBorrowings.value.filter(b => b.status === 'Overdue').length
      stats.value.activeBookings = activeBorrowings.value.length + roomBookings.value.length
    }
  } catch (err) {
    console.error('Error fetching bookings:', err)
    toast.error('Failed to load bookings')
  }
}

// ==================== BOOK MODAL ====================
const openBookModal = (book = null) => {
  if (book) {
    currentBook.value = { ...book }
    editingBook.value = true
  } else {
    currentBook.value = { title: '', author: '', isbn: '', category: '', publication_year: new Date().getFullYear(), copies: 1, availability_status: 'Available' }
    editingBook.value = false
  }
  nextTick(() => {
    if (!bookModalInstance && bookModalRef.value) bookModalInstance = new bootstrap.Modal(bookModalRef.value)
    bookModalInstance?.show()
  })
}
const saveBook = async () => {
  try {
    await api.post('/books.php', currentBook.value)
    toast.success(editingBook.value ? 'Book updated!' : 'Book added!')
    bookModalInstance?.hide()
    await loadAllData()
  } catch (e) {
    console.error('Error saving book:', e)
    toast.error('Failed to save book')
  }
}
const deleteBook = async (id) => {
  if (!confirm('Delete this book?')) return
  try { await api.delete(`/books.php?id=${id}`); toast.success('Book deleted'); await loadAllData() } catch (e) {
    console.error('Error deleting book:', e)
    toast.error('Delete failed')
  }
}

// ==================== ROOM MODAL ====================
const openRoomModal = (room = null) => {
  if (room) {
    currentRoom.value = { ...room }
    editingRoom.value = true
  } else {
    currentRoom.value = { name: '', capacity: 10, equipment: '' }
    editingRoom.value = false
  }
  nextTick(() => {
    if (!roomModalInstance && roomModalRef.value) roomModalInstance = new bootstrap.Modal(roomModalRef.value)
    roomModalInstance?.show()
  })
}
const saveRoom = async () => {
  try {
    await api.post('/rooms.php', currentRoom.value)
    toast.success(editingRoom.value ? 'Room updated!' : 'Room added!')
    roomModalInstance?.hide()
    await loadAllData()
  } catch (e) {
    console.error('Error saving room:', e)

    toast.error('Failed to save room')
  }
}
const deleteRoom = async (id) => {
  if (!confirm('Delete this room?')) return
  try { await api.delete(`/rooms.php?id=${id}`); toast.success('Room deleted'); await loadAllData() } catch (e) {
    console.error('Error deleting room:', e)
    toast.error('Delete failed')
  }
}

// ==================== EVENT MODAL ====================
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
  } catch (e) {
    console.error('Error saving event:', e)
    toast.error('Failed to save event')
  }
}
const deleteEvent = async (id) => {
  if (!confirm('Delete this event?')) return
  try { await api.delete(`/events.php?id=${id}`); toast.success('Event deleted'); await loadAllData() } catch (e) {
    console.error('Error deleting event:', e)
    toast.error('Delete failed')
  }
}

// ==================== ANNOUNCEMENT MODAL ====================
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
  } catch (e) {
    console.error('Error saving announcement:', e)
    toast.error('Failed to save announcement')
  }
}
const deleteAnnouncement = async (id) => {
  if (!confirm('Delete this announcement?')) return
  try { await api.delete(`/announcements.php?id=${id}`); toast.success('Announcement deleted'); await loadAllData() } catch (e) {
    console.error('Error deleting announcement:', e)
    toast.error('Delete failed')
  }
}

// ==================== BOOKINGS ACTIONS ====================
const markBookReturned = async (booking) => {
  if (!confirm(`Mark "${booking.book_title}" as returned?`)) return
  try { await api.post('/bookings.php?action=mark_returned', { booking_id: booking.id }); toast.success('Book returned!'); await fetchBookings() } catch (e) {
    console.error('Error marking book as returned:', e)
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
    await fetchBookings()
  } catch (e) {
    console.error('Error sending reminder:', e)
    toast.error('Failed to send reminder')
  }
}
const markRoomAvailable = async (booking) => {
  if (!confirm('Mark room booking as completed?')) return
  try { await api.post('/bookings.php?action=mark_room_available', { booking_id: booking.id }); toast.success('Room marked available!'); await fetchBookings() } catch (e) {
    console.error('Error marking room as available:', e)
    toast.error('Failed')
  }
}

// ==================== USERS ====================
const viewUser = (user) => {
  selectedUser.value = user
  nextTick(() => {
    if (!userModalInstance && userModalRef.value) userModalInstance = new bootstrap.Modal(userModalRef.value)
    userModalInstance?.show()
  })
}
const deleteUser = async (id) => {
  if (!confirm('Delete this user?')) return
  try { await api.delete(`/users.php?id=${id}`); toast.success('User deleted'); await loadAllData() } catch (e) {
    console.error('Error deleting user:', e)
    toast.error('Delete failed')
  }
}

// ==================== INIT ====================
onMounted(async () => {
  await loadAllData()
  await fetchBookings()
})
</script>

<style scoped>
/* Same elegant styles as before */
.elegant-admin-tabs .nav-link { color: #2C2C2C; font-weight: 600; padding: 10px 20px; border-radius: 50px; margin-right: 8px; border: 2px solid #E8B4B8; background: white; transition: all 0.3s ease; }
.elegant-admin-tabs .nav-link.active { background-color: #E8B4B8 !important; color: #2C2C2C !important; border-color: #E8B4B8 !important; box-shadow: 0 4px 15px rgba(232, 180, 184, 0.4); }
.elegant-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
.stat-card { background: white; border-radius: 16px; padding: 16px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 16px; }
.stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white; }
.bg-pink { background-color: #E8B4B8; } .bg-purple { background-color: #9B7EBD; }
.stat-value { font-size: 1.8rem; font-weight: 700; color: #2C2C2C; line-height: 1; }
.stat-label { font-size: 0.875rem; color: #666; margin-top: 4px; }
.btn-pink { background-color: #E8B4B8; border-color: #E8B4B8; color: #2C2C2C; font-weight: 600; }
.btn-pink:hover { background-color: #d89ca0; border-color: #d89ca0; color: #2C2C2C; }
.table th { background-color: #fafafa; font-weight: 600; color: #555; }
</style>
