<template>
  <div class="admin-dashboard">
    <!-- Header -->
    <div class="admin-header mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <h1 class="fw-bold mb-1" style="color: #2C2C2C; font-size: 2.4rem;">Admin Dashboard</h1>
          <p class="text-muted mb-0 fs-5">Manage Books • Rooms • Events • Borrowings</p>
        </div>
        <span class="badge px-4 py-2 fs-6"
              style="background-color: #E8B4B8; color: #2C2C2C; font-weight: 700; border-radius: 50px;">
          ADMIN
        </span>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-4">
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
                  <input v-model="newBook.title" class="form-control form-control-lg" placeholder="Book Title">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Author</label>
                  <input v-model="newBook.author" class="form-control form-control-lg" placeholder="Author">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">ISBN</label>
                  <input v-model="newBook.isbn" class="form-control" placeholder="978-3-16-148410-0">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Category</label>
                  <input v-model="newBook.category" class="form-control" placeholder="Fiction / Science">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Publication Year</label>
                  <input v-model="newBook.publication_year" type="number" class="form-control" placeholder="2024">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Copies</label>
                  <input v-model.number="newBook.copies" type="number" class="form-control" placeholder="3">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Availability Status</label>
                  <select v-model="newBook.availability_status" class="form-select">
                    <option value="Available">Available</option>
                    <option value="Borrowed">Borrowed</option>
                    <option value="Reserved">Reserved</option>
                    <option value="Maintenance">Under Maintenance</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Description</label>
                  <textarea v-model="newBook.description" class="form-control" rows="2" placeholder="Short description..."></textarea>
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
                      <td class="fw-semibold">{{ book.title }}</td>
                      <td>{{ book.author }}</td>
                      <td><small>{{ book.isbn }}</small></td>
                      <td><span class="badge bg-secondary">{{ book.category }}</span></td>
                      <td>{{ book.publication_year }}</td>
                      <td><span class="badge bg-info text-dark">{{ book.copies }}</span></td>
                      <td>
                        <span class="badge" :class="{
                          'bg-success': book.availability_status === 'Available',
                          'bg-warning text-dark': book.availability_status === 'Borrowed',
                          'bg-info': book.availability_status === 'Reserved'
                        }">
                          {{ book.availability_status }}
                        </span>
                      </td>
                      <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-warning me-2 px-3" @click="startEditBook(book)">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger px-3" @click="deleteBook(book.id)">
                          <i class="bi bi-trash3"></i>
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
              <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Add New Room</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12"><input v-model="newRoom.name" class="form-control form-control-lg" placeholder="Room Name"></div>
                <div class="col-12"><input v-model.number="newRoom.capacity" type="number" class="form-control form-control-lg" placeholder="Capacity"></div>
                <div class="col-12"><input v-model="newRoom.equipment" class="form-control" placeholder="Equipment"></div>
                <div class="col-12"><button class="btn btn-pink w-100 py-2" @click="addRoom">Add Room</button></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card elegant-card mb-4" v-if="editingRoom">
            <div class="card-header elegant-card-header"><h5 class="mb-0">Edit Room</h5></div>
            <div class="card-body">
              <div class="row g-2">
                <div class="col-md-4"><input v-model="newRoom.name" class="form-control"></div>
                <div class="col-md-3"><input v-model.number="newRoom.capacity" type="number" class="form-control"></div>
                <div class="col-md-5"><input v-model="newRoom.equipment" class="form-control"></div>
              </div>
              <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success" @click="saveRoomEdit">Save</button>
                <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
              </div>
            </div>
          </div>

          <div class="card elegant-card">
            <div class="card-header elegant-card-header"><h5 class="mb-0">Manage Rooms</h5></div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Capacity</th><th>Equipment</th><th class="text-end pe-4">Actions</th></tr></thead>
                <tbody>
                  <tr v-for="room in rooms" :key="room.id">
                    <td class="fw-semibold">{{ room.name }}</td>
                    <td><span class="badge bg-secondary">{{ room.capacity }} seats</span></td>
                    <td>{{ room.equipment }}</td>
                    <td class="text-end pe-4">
                      <button class="btn btn-sm btn-outline-warning me-2 px-3" @click="startEditRoom(room)"><i class="bi bi-pencil-square"></i></button>
                      <button class="btn btn-sm btn-outline-danger px-3" @click="deleteRoom(room.id)"><i class="bi bi-trash3"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== EVENTS (Simple CRUD - No Attendance) ==================== -->
    <div v-if="activeTab === 'events'" class="tab-pane">
      <div class="row g-4">
        <!-- Add Event Form -->
        <div class="col-lg-5">
          <div class="card elegant-card h-100">
            <div class="card-header elegant-card-header">
              <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Add New Event</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <input v-model="newEvent.title" class="form-control form-control-lg" placeholder="Event Title">
                </div>
                <div class="col-md-6">
                  <input v-model="newEvent.event_date" type="date" class="form-control">
                </div>
                <div class="col-md-6">
                  <input v-model="newEvent.event_time" type="time" class="form-control">
                </div>
                <div class="col-12">
                  <textarea v-model="newEvent.description" class="form-control" rows="2" placeholder="Description"></textarea>
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
                      <button class="btn btn-sm btn-outline-warning me-2 px-3" @click="startEditEvent(event)">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger px-3" @click="deleteEvent(event.id)">
                        <i class="bi bi-trash3"></i>
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
                  <button class="btn btn-sm btn-outline-primary me-2 px-3" @click="sendNotification(b)">Notify User</button>
                  <button class="btn btn-sm btn-success px-3" @click="markAsReturned(b)">Mark Returned</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const activeTab = ref('books')

const tabs = [
  { key: 'books', label: 'Books', icon: 'bi bi-book-fill' },
  { key: 'rooms', label: 'Rooms', icon: 'bi bi-door-open-fill' },
  { key: 'events', label: 'Events', icon: 'bi bi-calendar-event-fill' },
  { key: 'bookings', label: 'Bookings', icon: 'bi bi-calendar-check-fill' }
]

const books = ref([])
const rooms = ref([])
const events = ref([])
const activeBorrowings = ref([])

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
</style>
