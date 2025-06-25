<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-tr from-indigo-100 via-white to-indigo-200 px-4">
    <form
      @submit.prevent="submit"
      class="bg-white w-full max-w-sm rounded-2xl shadow-xl p-8 space-y-6"
    >
      <div class="text-center">
        <h2 class="text-2xl font-bold text-indigo-600">🔐 Login</h2>
        <p class="text-gray-500 text-sm mt-1">Access your dashboard</p>
      </div>

      <div class="space-y-4">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          class="w-full px-4 py-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
          required
        />

        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="w-full px-4 py-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
          required
        />
      </div>

      <button
        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md transition disabled:opacity-50"
        :disabled="loading"
      >
        {{ loading ? 'Logging in…' : 'Login' }}
      </button>

      <p v-if="error" class="text-center text-sm text-red-600">{{ error }}</p>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  emits: ['login'],
  data: () => ({
    email: '',
    password: '',
    loading: false,
    error: '',
  }),
  methods: {
    async submit() {
      this.error = '';
      this.loading = true;
      try {
        await axios.get('http://localhost:8000/sanctum/csrf-cookie');
        const { data } = await axios.post('http://localhost:8000/api/login', {
          email: this.email,
          password: this.password,
        });
        this.$emit('login', data);
      } catch (e) {
        this.error = e.response?.data?.message || 'Login failed';
      }
      this.loading = false;
    },
  },
};
</script>
