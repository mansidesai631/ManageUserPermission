<template>
  <div class="p-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
      <h2 class="text-2xl font-semibold text-indigo-600">👥 User Management</h2>
      <button v-if="hasPermission('user-create')" class="btn-primary" @click="openModal()">+ Add User</button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        @input="fetchUsers"
        placeholder="Search users..."
        class="input w-full sm:w-1/3"
      />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
      <table class="w-full text-sm text-left border-collapse">
        <thead class="bg-indigo-50 text-gray-700 uppercase text-xs">
          <tr>
            <th class="p-4">Name</th>
            <th class="p-4">Email</th>
            <th class="p-4">Mobile</th>
            <th class="p-4">Role</th>
            <th class="p-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
            <td class="p-4">{{ user.name }}</td>
            <td class="p-4">{{ user.email }}</td>
            <td class="p-4">{{ user.mobile }}</td>
            <td class="p-4">{{ USER_TYPE_LABELS[user.user_type] }}</td>
            <td class="p-4 space-x-2">
              <button v-if="hasPermission('user-edit')" class="btn-sm text-blue-600" @click="openModal(user)">Edit</button>
              <button v-if="hasPermission('user-delete')" class="btn-sm text-red-600" @click="remove(user)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-end space-x-2">
      <button class="btn-pagination" :disabled="!users.prev_page_url" @click="page--">Prev</button>
      <button class="btn-pagination" :disabled="!users.next_page_url" @click="page++">Next</button>
    </div>

    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <form @submit.prevent="submitForm" class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md space-y-4">
          <h3 class="text-xl font-semibold text-indigo-600">{{ form.id ? 'Edit' : 'Add' }} User</h3>
          <input v-model="form.name" placeholder="Name" class="input" />
          <input v-model="form.email" placeholder="Email" class="input" />
          <input v-model="form.mobile" placeholder="Mobile" class="input" />
          <input
            v-model="form.password"
            type="password"
            placeholder="Password"
            class="input"
            :required="!form.id"
          />
          <div class="flex gap-4 items-center">
            <label class="flex items-center">
              <input type="radio" :value="2" v-model="form.user_type" />
              <span class="ml-2">Admin</span>
            </label>
            <label class="flex items-center">
              <input type="radio" :value="3" v-model="form.user_type" />
              <span class="ml-2">Associate</span>
            </label>
          </div>
          <div class="flex justify-end space-x-3">
            <button type="button" class="btn-secondary" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn-primary">{{ loading ? 'Saving…' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  props: ['user', 'userPermissions'],
  data() {
    return {
      users: { data: [], prev_page_url: null, next_page_url: null },
      search: '', page: 1,
      showModal: false,
      form: { id: null, name: '', email: '', mobile: '', user_type: 'Admin', password: '' },
      loading: false,
      USER_TYPES: { OWNER: 1, ADMIN: 2, ASSOCIATE: 3},
      USER_TYPE_LABELS: { 1: 'Owner', 2: 'Admin', 3: 'Associate' },
    };
  },
  watch: {
    page: 'fetchUsers',
  },
  computed: {
    isOwner() {
      return this.user?.user_type === this.USER_TYPES.OWNER;
    },
    isAdmin() {
      return this.user?.user_type === this.USER_TYPES.ADMIN;
    },
    isAssociate() {
      return this.user?.user_type === this.USER_TYPES.ASSOCIATE;
    }
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    hasPermission(permission) {
      return this.userPermissions?.includes(permission);
    },
    async fetchUsers() {
      const { data } = await axios.get(`/users`, {
        params: { page: this.page, search: this.search },
      });
      this.users = data;
    },
    openModal(u = null) {
      this.form = u ? { ...u, password: '' } : { id: null, name: '', email: '', mobile: '', user_type: '2', password: '' };
      this.showModal = true;
    },
    async submitForm() {
      this.loading = true;
      try {
        if (this.form.id) {
          await axios.put(`/users/${this.form.id}`, this.form);
        } else {
          await axios.post(`/users`, this.form);
        }
        this.fetchUsers();
        this.showModal = false;
      } catch (e) {
        alert(e.response?.data?.message || 'Error');
      }
      this.loading = false;
    },
    async remove(u) {
      if (confirm('Delete user?')) {
        await axios.delete(`/users/${u.id}`);
        this.fetchUsers();
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
  @apply px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition;
}
.btn-secondary {
  @apply px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition;
}
.btn-sm {
  @apply px-2 py-1 text-sm font-medium rounded hover:underline;
}
.btn-pagination {
  @apply px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-100 disabled:opacity-50;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
