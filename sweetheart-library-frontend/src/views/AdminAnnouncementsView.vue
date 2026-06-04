<template>
  <div class="admin-announcements py-4">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold mb-1" style="color: #2C2C2C;">Manage Announcements</h2>
          <p class="text-muted mb-0">Create and manage library announcements</p>
        </div>
        <button @click="showCreateForm = true" class="btn btn-pink">
          <i class="bi bi-plus-lg me-2"></i> New Announcement
        </button>
      </div>

      <!-- Create/Edit Form -->
      <div v-if="showCreateForm" class="card border-0 shadow-sm mb-4" style="background-color: #fff; border-radius: 16px;">
        <div class="card-body p-4">
          <h5 class="fw-semibold mb-3">{{ editingId ? 'Edit Announcement' : 'Create New Announcement' }}</h5>

          <form @submit.prevent="saveAnnouncement">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Title</label>
                <input v-model="form.title" type="text" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Type</label>
                <select v-model="form.type" class="form-select">
                  <option value="Notice">Notice</option>
                  <option value="Event">Event</option>
                  <option value="Maintenance">Maintenance</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Message</label>
                <textarea v-model="form.message" class="form-control" rows="4" required></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Due Date (optional)</label>
                <input v-model="form.due_date" type="date" class="form-control">
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                  <input v-model="form.is_published" class="form-check-input" type="checkbox" id="publishCheck">
                  <label class="form-check-label" for="publishCheck">
                    Publish immediately
                  </label>
                </div>
              </div>
            </div>

            <div class="mt-4 d-flex gap-2">
              <button type="submit" class="btn btn-pink px-4">{{ editingId ? 'Update' : 'Create' }} Announcement</button>
              <button type="button" @click="cancelForm" class="btn btn-outline-secondary">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Announcements Table -->
      <div class="card border-0 shadow-sm" style="background-color: #fff; border-radius: 16px;">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead class="table-light">
                <tr>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ann in announcements" :key="ann.id">
                  <td>
                    <strong>{{ ann.title }}</strong><br>
                    <small class="text-muted">{{ ann.message.substring(0, 80) }}...</small>
                  </td>
                  <td><span class="badge bg-secondary">{{ ann.type }}</span></td>
                  <td>
                    <span class="badge" :class="ann.is_published ? 'bg-success' : 'bg-warning text-dark'">
                      {{ ann.is_published ? 'Published' : 'Draft' }}
                    </span>
                  </td>
                  <td>{{ ann.created_at }}</td>
                  <td class="text-end">
                    <button @click="editAnnouncement(ann)" class="btn btn-sm btn-outline-primary me-1">Edit</button>
                    <button @click="togglePublish(ann)" class="btn btn-sm btn-outline-secondary me-1">
                      {{ ann.is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                    <button @click="deleteAnnouncement(ann)" class="btn btn-sm btn-outline-danger">Delete</button>
                  </td>
                </tr>
                <tr v-if="announcements.length === 0">
                  <td colspan="5" class="text-center py-4 text-muted">No announcements yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const announcements = ref([])
const showCreateForm = ref(false)
const editingId = ref(null)

const form = ref({
  title: '',
  message: '',
  type: 'Notice',
  due_date: '',
  is_published: true
})

const loadAnnouncements = async () => {
  try {
    const res = await api.get('/announcements.php?action=get_all')
    announcements.value = res.data
  } catch (error) {
    console.error('Failed to load announcements:', error)
  }
}

const saveAnnouncement = async () => {
  try {
    const payload = { ...form.value }
    if (editingId.value) {
      payload.id = editingId.value
      await api.put('/announcements.php', payload)
    } else {
      await api.post('/announcements.php', payload)
    }
    cancelForm()
    await loadAnnouncements()
  } catch (error) {
    alert('Failed to save announcement')
    console.error(error)
  }
}

const editAnnouncement = (ann) => {
  editingId.value = ann.id
  form.value = {
    title: ann.title,
    message: ann.message,
    type: ann.type || 'Notice',
    due_date: ann.due_date || '',
    is_published: !!ann.is_published
  }
  showCreateForm.value = true
}

const togglePublish = async (ann) => {
  try {
    await api.put('/announcements.php', {
      id: ann.id,
      title: ann.title,
      message: ann.message,
      type: ann.type,
      due_date: ann.due_date,
      is_published: !ann.is_published
    })
    await loadAnnouncements()
  } catch (error) {
    console.error('Failed to update publish status:', error)
    alert('Failed to update status')
  }
}

const deleteAnnouncement = async (ann) => {
  if (!confirm(`Delete "${ann.title}"?`)) return
  try {
    await api.delete(`/announcements.php?id=${ann.id}`)
    await loadAnnouncements()
  } catch (error) {
    console.error('Failed to delete announcement:', error)
    alert('Failed to delete announcement')
  }
}

const cancelForm = () => {
  showCreateForm.value = false
  editingId.value = null
  form.value = {
    title: '', message: '', type: 'Notice', due_date: '', is_published: true
  }
}

onMounted(loadAnnouncements)
</script>

<style scoped>
.btn-pink {
  background-color: #E8B4B8;
  color: #2C2C2C;
  font-weight: 600;
  border: none;
}
</style>
