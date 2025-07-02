<template>
  <aside class="h-full w-64 bg-gray-900 text-white flex flex-col shadow-lg">
    <div class="px-6 py-4 border-b border-gray-700">
      	<h2 class="text-2xl font-semibold text-indigo-400 tracking-wide">📊 Dashboard</h2>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 space-y-2 px-4">
      <button
        v-if="canAccess('UserManagement')"
        @click="$emit('navigate', 'UserManagement')"
        class="flex items-center w-full px-4 py-2 rounded-lg hover:bg-indigo-600 bg-opacity-10 transition-colors text-sm font-medium"
      >
        <span class="text-indigo-300">👤</span>
        <span class="ml-3">User Management</span>
      </button>

      <button
        v-if="canAccess('PermissionCrud')"
        @click="$emit('navigate', 'PermissionCrud')"
        class="flex items-center w-full px-4 py-2 rounded-lg hover:bg-indigo-600 bg-opacity-10 transition-colors text-sm font-medium"
      >
        <span class="text-indigo-300">🔐</span>
        <span class="ml-3">Permission Management</span>
      </button>

      <button
        v-if="canAccess('PermissionManagement')"
        @click="$emit('navigate', 'PermissionManagement')"
        class="flex items-center w-full px-4 py-2 rounded-lg hover:bg-indigo-600 bg-opacity-10 transition-colors text-sm font-medium"
      >
        <span class="text-indigo-300">🧩</span>
        <span class="ml-3">Permissions Assign</span>
      </button>
    </nav>

    <div class="px-4 py-6 border-t border-gray-700">
      <button
        @click="$emit('logout')"
        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition-colors font-medium text-sm"
      >
        🚪 Logout
      </button>
    </div>
  </aside>
</template>

<script>
export default {
  props: {
    role: {
      type: Number,
      required: true
    },
    permissions: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    canAccess(permission) {
      // If Owner, allow all
      if (this.role == 1) return true;

      // Otherwise check assigned permissions
      return this.permissions.includes(permission);
    }
  }
}
</script>
