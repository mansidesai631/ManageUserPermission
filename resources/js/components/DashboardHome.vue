<template>
  <div class="text-gray-800">
    <h2 class="text-2xl font-semibold mb-4">Welcome, {{ user.name }} 👋</h2>
  </div>
</template>

<script>
export default {
  props: ['user']
};
</script>
