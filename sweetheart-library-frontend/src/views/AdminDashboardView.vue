<template>
  <div class="admin-dashboard">
    <!-- Header -->
    <div class="admin-header mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <!-- Left Side: Title -->
        <div class="flex-grow-1">
          <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.4rem;">Admin Dashboard</h1>
          <p class="text-muted mb-0 fs-5">Manage Books • Rooms • Events • Bookings • Users Details </p>
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
      <div class="row g-4">
        <!-- Add/Edit Book Form -->
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

        <!-- Books Table -->
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
                        <!-- UPDATED: Colored Box Buttons -->
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

    <!-- ==================== EVENTS (Simple CRUD - No Attendance) ==================== -->
    <div v-if="activeTab === 'events'" class="tab-pane">
      <div class="row g-3">
        <!-- Add Event Form -->
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

        <!-- Events List -->
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
                    <th>Date</th>
                    <th>Time</th>
                    <th class="text-end pe-4">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="event in events" :key="event.id">
                    <td>
                      <div class="fw-semibold">{{ event.title }}</div>
                      <small class="text-muted">{{ event.description }}</small>
                    </td>
                    <td>{{ event.event_date }}</td>
                    <td>{{ event.event_time }}</td>
                    <td class="text-end pe-4">
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

    <!-- ==================== BOOKINGS MANAGEMENT ==================== -->
    <div v-if="activeTab === 'bookings'" class="tab-pane">
      <div class="card elegant-card">
        <div class="card-header elegant-card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-calendar-check-fill me-2"></i>Active Book Borrowings</h5>
          <span class="badge bg-light text-dark px-3 py-2">{{ activeBorrowings.length }} active</span>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Book</th>
                <th>User</th>
                <th>Borrowed Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th class="text-end pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="b in activeBorrowings" :key="b.id">
                <td class="fw-semibold">{{ b.book_title }}</td>
                <td>{{ b.user_name }}</td>
                <td>{{ b.borrowed_date }}</td>
                <td>{{ b.due_date }}</td>
                <td><span class="badge bg-warning text-dark">{{ b.status }}</span></td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm action-btn notify-btn me-2" @click="sendNotification(b)">
                    <i class="bi bi-bell me-1"></i> Notify
                  </button>
                  <button class="btn btn-sm action-btn success-btn" @click="markAsReturned(b)">
                    <i class="bi bi-check2-circle me-1"></i> Returned
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api.js'

const activeTab = ref('books')

const tabs = [
  { key: 'books', label: 'Books', icon: 'bi bi-book-fill' },
  { key: 'rooms', label: 'Rooms', icon: 'bi bi-door-open-fill' },
  { key: 'events', label: 'Events', icon: 'bi bi-calendar-event-fill' },
  { key: 'bookings', label: 'Bookings', icon: 'bi bi-calendar-check-fill' },
  { key: 'users', label: 'Users', icon: 'bi bi-people-fill' }
]

const books = ref([])
const rooms = ref([])
const events = ref([])
const activeBorrowings = ref([])
const users = ref([])
const selectedUser = ref(null)
const showUserModal = ref(false)

const newBook = ref({
  title: '', author: '', isbn: '', category: '', publication_year: new Date().getFullYear(),
  copies: 1, description: '', availability_status: 'Available'
})
const newRoom = ref({ name: '', capacity: '', equipment: '' })
const newEvent = ref({ title: '', event_date: '', event_time: '', description: '' })

const editingBook = ref(false)
const editingRoom = ref(false)
const editingEvent = ref(false)

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

// ==================== BOOKS CRUD ====================
const currentTabLabel = computed(() => {
  const tab = tabs.find(t => t.key === activeTab.value)
  return tab ? tab.label : 'Menu'
})

const currentTabIcon = computed(() => {
  const tab = tabs.find(t => t.key === activeTab.value)
  return tab ? tab.icon : 'bi bi-list'
})

const addBook = async () => {
  if (!newBook.value.title) return alert('Title is required')
  await api.post('/books.php', newBook.value)
  resetBookForm()
  loadAllData()
}

const startEditBook = (book) => {
  newBook.value = { ...book }
  editingBook.value = true
}

const saveBookEdit = async () => {
  await api.post('/books.php', newBook.value)
  resetBookForm()
  loadAllData()
}

const deleteBook = async (id) => {
  if (confirm('Delete this book?')) {
    await api.delete(`/books.php?id=${id}`)
    loadAllData()
  }
}

// ==================== ROOMS CRUD ====================
const addRoom = async () => {
  if (!newRoom.value.name) return alert('Room name is required')
  await api.post('/rooms.php', newRoom.value)
  resetRoomForm()
  loadAllData()
}

const startEditRoom = (room) => {
  newRoom.value = { ...room }
  editingRoom.value = true
}

const saveRoomEdit = async () => {
  await api.post('/rooms.php', newRoom.value)
  resetRoomForm()
  loadAllData()
}

const deleteRoom = async (id) => {
  if (confirm('Delete this room?')) {
    await api.delete(`/rooms.php?id=${id}`)
    loadAllData()
  }
}

// ==================== EVENTS CRUD (Simple - No Attendance) ====================
const addEvent = async () => {
  if (!newEvent.value.title) return alert('Event title is required')
  await api.post('/events.php', newEvent.value)
  newEvent.value = { title: '', event_date: '', event_time: '', description: '' }
  loadAllData()
}

const startEditEvent = (event) => {
  newEvent.value = { ...event }
  editingEvent.value = true
}

const saveEventEdit = async () => {
  await api.post('/events.php', newEvent.value)
  newEvent.value = { title: '', event_date: '', event_time: '', description: '' }
  editingEvent.value = false
  loadAllData()
}

const deleteEvent = async (id) => {
  if (confirm('Delete this event?')) {
    await api.delete(`/events.php?id=${id}`)
    loadAllData()
  }
}

// ==================== BOOKINGS ====================
const sendNotification = (borrowing) => {
  const message = prompt(`Send message to ${borrowing.user_name}:`)
  if (message) alert(`Notification sent to ${borrowing.user_email}:\n\n${message}`)
}

const markAsReturned = async (borrowing) => {
  if (!confirm(`Mark "${borrowing.book_title}" as returned?`)) return

  await api.post('/books.php', {
    id: borrowing.book_id,
    availability_status: 'Available'
  })
  activeBorrowings.value = activeBorrowings.value.filter(b => b.id !== borrowing.id)
  loadAllData()
  alert('Book marked as returned and is now available.')
}

const resetBookForm = () => {
  newBook.value = {
    title: '', author: '', isbn: '', category: '', publication_year: new Date().getFullYear(),
    copies: 1, description: '', availability_status: 'Available'
  }
  editingBook.value = false
}

const resetRoomForm = () => {
  newRoom.value = { name: '', capacity: '', equipment: '' }
  editingRoom.value = false
}

const cancelEdit = () => {
  resetBookForm()
  resetRoomForm()
  newEvent.value = { title: '', event_date: '', event_time: '', description: '' }
  editingEvent.value = false
}

onMounted(loadAllData)

const loadUsers = async () => {
  try {
    const res = await api.get('/users.php')
    users.value = res.data || []
  } catch (err) {
    console.error('Failed loading users', err)
  }
}

const viewUser = (u) => {
  selectedUser.value = { ...u }
  // explicitly omit sensitive fields
  if (selectedUser.value.password) delete selectedUser.value.password
  showUserModal.value = true
}

const closeUserModal = () => {
  showUserModal.value = false
  selectedUser.value = null
}

const deleteUser = async (id) => {
  if (!confirm('Delete this user?')) return
  try {
    await api.delete(`/users.php?id=${id}`)
    await loadUsers()
  } catch (err) {
    console.error('Failed to delete user', err)
  }
}

onMounted(() => {
  loadAllData()
  loadUsers()
})
</script>

<style scoped>
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
.table th, .table td {
  padding: 10px;
}

div
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

.notify-btn {
  background-color: #CFE2FF;
  color: #084298;
}
.notify-btn:hover {
  background-color: #b6d4fe;
}

.success-btn {
  background-color: #D1E7DD;
  color: #0f5132;
}
.success-btn:hover {
  background-color: #badbcc;
}

/* Make sure dropdown looks good */
.dropdown-menu {
  border-radius: 12px;
  padding: 8px 0;
}

.dropdown-item {
  padding: 10px 16px;
  font-weight: 500;
}

/* Simple modal for user details */
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
