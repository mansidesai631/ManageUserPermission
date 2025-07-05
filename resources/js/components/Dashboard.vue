<template>
  <div class="flex h-screen bg-gradient-to-tr from-indigo-50 to-white overflow-hidden">
    <!-- Sidebar -->
    <aside
      :class="[
        'bg-white w-64 z-40 h-full shadow-xl fixed transition-transform duration-300 ease-in-out rounded-r-xl',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'lg:translate-x-0 lg:relative'
      ]"
    >
      <Sidebar :permissions="user.permissions" :role="user.user_type" @navigate="setView" @logout="logout" />
    </aside>

    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black bg-opacity-30 z-30 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <header class="flex items-center justify-between bg-white px-6 py-4 shadow-md">
        <div class="flex items-center gap-3">
          <button @click="toggleSidebar" class="lg:hidden text-indigo-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h1 class="text-2xl font-semibold text-gray-800 tracking-tight">📊 Dashboard</h1>
        </div>
        <div class="text-sm text-gray-600">
          Welcome, <span class="font-medium text-indigo-500">{{ user.name }}</span>
        </div>
      </header>

      <main class="flex-1 p-6 overflow-y-auto bg-white rounded-tl-2xl shadow-inner">
        <component :is="currentView" :user="user" :userPermissions="user.permissions" />
      </main>
    </div>
  </div>
</template>

<script>
import DashboardHome from './DashboardHome.vue';
import Sidebar from './Sidebar.vue';
import UserManagement from './UserManagement.vue';
import PermissionManagement from './PermissionManagement.vue';
import PermissionCrud from './PermissionCrud.vue';
import axios from 'axios';

export default {
  props: ['user'],
  components: { DashboardHome, Sidebar, UserManagement, PermissionManagement, PermissionCrud},
  data() {
    return {
      currentView: 'DashboardHome',
      sidebarOpen: false,
    };
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    setView(view) {
      this.currentView = view;
      this.sidebarOpen = false;
    },
    logout() {
      axios.post('/logout').then(() => location.reload());
    }
  }
}
</script>
