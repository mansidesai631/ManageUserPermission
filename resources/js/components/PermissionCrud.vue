<template>
  <div class="p-4 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <h2 class="text-2xl font-bold text-indigo-600">🔐 Permission Management</h2>
      <div class="flex gap-2 w-full sm:w-auto">
        <input
          v-model="search"
          @input="filter"
          placeholder="Search permissions..."
          class="input flex-1"
        />
        <button class="btn-primary" @click="openModal()">+ Add</button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full bg-white rounded-lg shadow text-sm min-w-[400px]">
        <thead class="bg-indigo-50 text-left">
          <tr>
            <th class="p-4 font-medium text-gray-600">Permission</th>
            <th class="p-4 font-medium text-gray-600">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredPermissions" :key="p.id" class="hover:bg-gray-50">
            <td class="p-4 font-medium text-gray-800">{{ p.name }}</td>
            <td class="p-4 space-x-2">
              <button class="btn-sm text-blue-600" @click="openModal(p)">Edit</button>
              <button class="btn-sm text-red-600" @click="remove(p)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50">
        <form @submit.prevent="submitForm" class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl space-y-4">
          <h3 class="text-xl font-semibold text-indigo-600">{{ form.id ? 'Edit' : 'Add' }} Permission</h3>
          <input v-model="form.name" placeholder="Permission Name" class="input" />
          <div class="flex justify-end gap-3 pt-4">
            <button type="button" class="btn-secondary" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="loading">
              {{ loading ? 'Saving…' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      permissions: [],
      filteredPermissions: [],
      search: '',
      showModal: false,
      loading: false,
      form: { id: null, name: '' },
    };
  },
  mounted() {
    this.fetchPermissions();
  },
  methods: {
    async fetchPermissions() {
      const { data } = await axios.get('/permissions');
      this.permissions = data;
      this.filteredPermissions = data;
    },
    filter() {
      this.filteredPermissions = this.permissions.filter(p =>
        p.name.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    openModal(p = null) {
      this.form = p ? { ...p } : { id: null, name: '' };
      this.showModal = true;
    },
    async submitForm() {
      this.loading = true;
      try {
        if (this.form.id) {
          await axios.put(`/permissions/${this.form.id}`, this.form);
        } else {
          await axios.post('/permissions', this.form);
        }
        await this.fetchPermissions();
        this.showModal = false;
      } catch (e) {
        alert(e.response?.data?.message || 'Something went wrong');
      }
      this.loading = false;
    },
    async remove(p) {
      if (confirm(`Delete permission "${p.name}"?`)) {
        await axios.delete(`/permissions/${p.id}`);
        await this.fetchPermissions();
      }
    },
  },
};
</script>

<style scoped>
.input {
  @apply w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-300;
}
.btn-primary {
  @apply px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50;
}
.btn-secondary {
  @apply px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition;
}
.btn-sm {
  @apply px-2 py-1 text-sm font-medium rounded hover:underline;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
