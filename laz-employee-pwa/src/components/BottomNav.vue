<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router';
import { Home, MapPin, HandCoins, User } from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();

const navItems = [
  { name: 'Home', path: '/', icon: Home },
  { name: 'Absen', path: '/attendance', icon: MapPin },
  { name: 'Donasi', path: '/fundraising', icon: HandCoins },
  { name: 'Profil', path: '/profile', icon: User },
];

const navigate = (path: string) => {
  router.push(path);
};

const isActive = (path: string) => {
  return route.path === path;
};
</script>

<template>
  <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pb-safe">
    <div class="flex justify-around items-center h-16">
      <button
        v-for="item in navItems"
        :key="item.name"
        @click="navigate(item.path)"
        class="flex flex-col items-center justify-center w-full h-full space-y-1"
        :class="isActive(item.path) ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
      >
        <component :is="item.icon" class="w-6 h-6" />
        <span class="text-xs font-medium">{{ item.name }}</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
