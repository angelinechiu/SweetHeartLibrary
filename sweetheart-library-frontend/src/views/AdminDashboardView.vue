<template>
  <div style="background-color: #F8F4F0;" class="py-5">
    <div class="container">
      <h1 class="fw-bold mb-5" style="color: #2C2C2C;">Admin Dashboard</h1>

      <!-- Tabs -->
      <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><button class="nav-link" :class="{ active: activeTab === 'books' }" @click="activeTab = 'books'">Books</button></li>
        <li class="nav-item"><button class="nav-link" :class="{ active: activeTab === 'rooms' }" @click="activeTab = 'rooms'">Rooms</button></li>
        <li class="nav-item"><button class="nav-link" :class="{ active: activeTab === 'events' }" @click="activeTab = 'events'">Events</button></li>
        <li class="nav-item"><button class="nav-link" :class="{ active: activeTab === 'bookings' }" @click="activeTab = 'bookings'">Bookings</button></li>
        <li class="nav-item"><button class="nav-link" :class="{ active: activeTab === 'users' }" @click="activeTab = 'users'">Users</button></li>
      </ul>

      <!-- ==================== BOOKS ==================== -->
      <div v-if="activeTab === 'books'">
        <!-- Add Book Form -->
        <div class="card mb-4">
          <div class="card-body">
            <h5>Add New Book</h5>
            <div class="row g-2">
              <div class="col-md-3"><input v-model="newBook.title" class="form-control" placeholder="Title"></div>
              <div class="col-md-3"><input v-model="newBook.author" class="form-control" placeholder="Author"></div>
              <div class="col-md-4"><input v-model="newBook.description" class="form-control" placeholder="Description"></div>
              <div class="col-md-2"><button class="btn btn-pink w-100" @click="addBook">Add Book</button></div>
            </div>
          </div>
        </div>

        <!-- Edit Book Form -->
        <div class="card mb-4" v-if="editingBook">
          <div class="card-body">
            <h5>Edit Book</h5>
            <div class="row g-2">
              <div class="col-md-3"><input v-model="editingBook.title" class="form-control"></div>
              <div class="col-md-3"><input v-model="editingBook.author" class="form-control"></div>
              <div class="col-md-4"><input v-model="editingBook.description" class="form-control"></div>
              <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success" @click="saveBookEdit">Save</button>
                <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Books Table -->
        <div class="card">
          <div class="card-body">
            <h5>Manage Books</h5>
            <table class="table table-hover">
              <thead style="background-color: #2C2C2C; color: #F8F4F0;">
                <tr><th>Title</th><th>Author</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <tr v-for="book in books" :key="book.id">
                  <td>{{ book.title }}</td>
                  <td>{{ book.author }}</td>
                  <td>
                    <button class="btn btn-sm btn-warning me-2" @click="startEditBook(book)">Edit</button>
                    <button class="btn btn-sm btn-danger" @click="deleteBook(book.id)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== ROOMS ==================== -->
      <div v-if="activeTab === 'rooms'">
        <!-- Similar structure as Books -->
        <div class="card mb-4">
          <div class="card-body">
            <h5>Add New Room</h5>
            <div class="row g-2">
              <div class="col-md-3"><input v-model="newRoom.name" class="form-control" placeholder="Name"></div>
              <div class="col-md-2"><input v-model.number="newRoom.capacity" type="number" class="form-control" placeholder="Capacity"></div>
              <div class="col-md-5"><input v-model="newRoom.equipment" class="form-control" placeholder="Equipment"></div>
              <div class="col-md-2"><button class="btn btn-pink w-100" @click="addRoom">Add</button></div>
            </div>
          </div>
        </div>

        <div class="card mb-4" v-if="editingRoom">
          <div class="card-body">
            <h5>Edit Room</h5>
            <div class="row g-2">
              <div class="col-md-3"><input v-model="editingRoom.name" class="form-control"></div>
              <div class="col-md-2"><input v-model.number="editingRoom.capacity" type="number" class="form-control"></div>
              <div class="col-md-5"><input v-model="editingRoom.equipment" class="form-control"></div>
              <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success" @click="saveRoomEdit">Save</button>
                <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5>Manage Rooms</h5>
            <table class="table table-hover">
              <thead style="background-color: #2C2C2C; color: #F8F4F0;">
                <tr><th>Name</th><th>Capacity</th><th>Equipment</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <tr v-for="room in rooms" :key="room.id">
                  <td>{{ room.name }}</td>
                  <td>{{ room.capacity }}</td>
                  <td>{{ room.equipment }}</td>
                  <td>
                    <button class="btn btn-sm btn-warning me-2" @click="startEditRoom(room)">Edit</button>
                    <button class="btn btn-sm btn-danger" @click="deleteRoom(room.id)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== EVENTS ==================== -->
      <div v-if="activeTab === 'events'">
        <!-- Similar structure -->
        <div class="card mb-4">
          <div class="card-body">
            <h5>Add New Event</h5>
            <div class="row g-2">
              <div class="col-md-5"><input v-model="newEvent.title" class="form-control" placeholder="Title"></div>
              <div class="col-md-2"><input v-model="newEvent.event_date" type="date" class="form-control"></div>
              <div class="col-md-2"><input v-model="newEvent.event_time" type="time" class="form-control"></div>
              <div class="col-md-3"><button class="btn btn-pink w-100" @click="addEvent">Add</button></div>
            </div>
          </div>
        </div>

        <div class="card mb-4" v-if="editingEvent">
          <div class="card-body">
            <h5>Edit Event</h5>
            <div class="row g-2">
              <div class="col-md-5"><input v-model="editingEvent.title" class="form-control"></div>
              <div class="col-md-2"><input v-model="editingEvent.event_date" type="date" class="form-control"></div>
              <div class="col-md-2"><input v-model="editingEvent.event_time" type="time" class="form-control"></div>
              <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-success" @click="saveEventEdit">Save</button>
                <button class="btn btn-secondary" @click="cancelEdit">Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5>Manage Events</h5>
            <table class="table table-hover">
              <thead style="background-color: #2C2C2C; color: #F8F4F0;">
                <tr><th>Title</th><th>Date</th><th>Time</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <tr v-for="event in events" :key="event.id">
                  <td>{{ event.title }}</td>
                  <td>{{ event.event_date }}</td>
                  <td>{{ event.event_time }}</td>
                  <td>
                    <button class="btn btn-sm btn-warning me-2" @click="startEditEvent(event)">Edit</button>
                    <button class="btn btn-sm btn-danger" @click="deleteEvent(event.id)">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== BOOKINGS & USERS (Simplified) ==================== -->
      <div v-if="activeTab === 'bookings' || activeTab === 'users'">
        <div class="card">
          <div class="card-body">
            <h5>{{ activeTab }}</h5>
            <p class="text-muted">Data loaded from database. Full CRUD available in MyBookings and Users sections.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const activeTab = ref('books')

const books = ref([])
const rooms = ref([])
const events = ref([])

const newBook = ref({ title: '', author: '', description: '' })
const newRoom = ref({ name: '', capacity: '', equipment: '' })
const newEvent = ref({ title: '', event_date: '', event_time: '' })

const editingBook = ref(null)
const editingRoom = ref(null)
const editingEvent = ref(null)

const loadAllData = async () => {
  try {
    const [b, r, e] = await Promise.all([
      api.get('/books.php'),
      api.get('/rooms.php'),
      api.get('/events.php')
    ])
    books.value = b.data
    rooms.value = r.data
    events.value = e.data
  } catch (error) {
    console.error(error)
  }
}

// ==================== BOOKS CRUD ====================
const addBook = async () => {
  await api.post('/books.php', newBook.value)
  newBook.value = { title: '', author: '', description: '' }
  loadAllData()
}

const startEditBook = (book) => {
  editingBook.value = { ...book }
  editingRoom.value = null
  editingEvent.value = null
}

const saveBookEdit = async () => {
  await api.post('/books.php', editingBook.value)
  editingBook.value = null
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
  await api.post('/rooms.php', newRoom.value)
  newRoom.value = { name: '', capacity: '', equipment: '' }
  loadAllData()
}

const startEditRoom = (room) => {
  editingRoom.value = { ...room }
  editingBook.value = null
  editingEvent.value = null
}

const saveRoomEdit = async () => {
  await api.post('/rooms.php', editingRoom.value)
  editingRoom.value = null
  loadAllData()
}

const deleteRoom = async (id) => {
  if (confirm('Delete this room?')) {
    await api.delete(`/rooms.php?id=${id}`)
    loadAllData()
  }
}

// ==================== EVENTS CRUD ====================
const addEvent = async () => {
  await api.post('/events.php', newEvent.value)
  newEvent.value = { title: '', event_date: '', event_time: '' }
  loadAllData()
}

const startEditEvent = (event) => {
  editingEvent.value = { ...event }
  editingBook.value = null
  editingRoom.value = null
}

const saveEventEdit = async () => {
  await api.post('/events.php', editingEvent.value)
  editingEvent.value = null
  loadAllData()
}

const deleteEvent = async (id) => {
  if (confirm('Delete this event?')) {
    await api.delete(`/events.php?id=${id}`)
    loadAllData()
  }
}

const cancelEdit = () => {
  editingBook.value = null
  editingRoom.value = null
  editingEvent.value = null
}

onMounted(loadAllData)
</script>
