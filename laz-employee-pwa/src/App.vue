<script setup lang="ts">
import { RouterView } from 'vue-router'
import BottomNav from './components/BottomNav.vue'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-gray-50 pb-16">
    <RouterView />
    <BottomNav v-if="authStore.isAuthenticated" />
  </div>
</template>
