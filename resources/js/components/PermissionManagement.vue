<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold text-indigo-600 mb-6">🔐 Permission Management</h2>

    <div v-if="loading" class="text-gray-500">Loading...</div>

    <div v-else class="space-y-8">
      <div
        v-for="role in roles"
        :key="role.id"
        class="bg-white rounded-lg shadow p-6 border"
      >
        <h3 class="text-lg font-medium text-gray-800 mb-4 capitalize">
          {{ role.name }}
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
          <label
            v-for="perm in allPermissions"
            :key="perm.id"
            class="flex items-center space-x-2"
          >
            <input
              type="checkbox"
              :value="perm.name"
              v-model="rolePermissions[role.id]"
              class="form-checkbox text-indigo-600"
            />
            <span class="text-gray-700">{{ perm.name }}</span>
          </label>
        </div>

        <div class="flex justify-end mt-6">
          <button
            class="btn-primary"
            @click="savePermissions(role.id)"
            :disabled="saving[role.id]"
          >
            {{ saving[role.id] ? 'Saving…' : 'Save Permissions' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      roles: [],
      allPermissions: [],
      rolePermissions: {},
      saving: {},
      loading: true,
    };
  },
  async mounted() {
    await this.fetchRolesAndPermissions();
  },
  methods: {
    async fetchRolesAndPermissions() {
      try {
        const { data } = await axios.get('/roles-permissions');
        this.roles = data.roles;
        this.allPermissions = data.permissions; 
        this.rolePermissions = data.rolePermissions;
      } catch (e) {
        console.error('Error fetching roles/permissions', e);
      } finally {
        this.loading = false;
      }
    },
    async savePermissions(roleId) {
      this.saving[roleId] = true;
      try {
        await axios.post(`/roles/${roleId}/permissions`, {
          permissions: this.rolePermissions[roleId],
        });
      } catch (e) {
        console.error('Failed to save permissions:', e);
        alert('Failed to save permissions.');
      }
      this.saving[roleId] = false;
    },
  },
};
</script>

<style scoped>
.btn-primary {
  @apply px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition;
}
</style>
