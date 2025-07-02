import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios';
import LoginForm from './components/LoginForm.vue';
import Dashboard from './components/Dashboard.vue';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = '/';

createApp({
  data() {
    return {
      user: null,
      userPermissions: [],
      loading: true
    };
  },
  components: { LoginForm, Dashboard },
  template: `
    <div v-if="loading" class="text-center p-6">Loading...</div>
    <LoginForm v-else-if="!user" @login="handleLogin" />
    <Dashboard v-else :user="user" />
  `,
  methods: {
    async handleLogin() {
      await this.fetchUser();
    },
    async fetchUser() {
      try {
        const { data } = await axios.get('http://localhost:8000/user');
        this.user = data.user;
        this.userPermissions = data.permissions;
      } catch (error) {
        this.user = null;
        this.userPermissions = null;
      } finally {
        this.loading = false;
      }
    }
  },
  mounted() {
    this.fetchUser();
  }
}).mount('#app');
