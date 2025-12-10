<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { User, LogOut, Building, Briefcase } from 'lucide-vue-next';

const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
  if (!authStore.user) {
    authStore.fetchUser();
  }
});

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Profil Saya</h1>

    <!-- Profile Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 flex items-center space-x-4">
      <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center text-primary">
        <User class="w-8 h-8" />
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">{{ authStore.user?.name || 'Loading...' }}</h2>
        <p class="text-gray-500">{{ authStore.user?.email }}</p>
      </div>
    </div>

    <!-- Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
      <div class="p-4 border-b border-gray-100 flex items-center space-x-3">
        <Briefcase class="w-5 h-5 text-gray-400" />
        <div>
          <p class="text-xs text-gray-500">Jabatan</p>
          <p class="font-medium text-gray-900">{{ authStore.user?.position?.name || '-' }}</p>
        </div>
      </div>
      <div class="p-4 flex items-center space-x-3">
        <Building class="w-5 h-5 text-gray-400" />
        <div>
          <p class="text-xs text-gray-500">Cabang</p>
          <p class="font-medium text-gray-900">{{ authStore.user?.branch?.name || '-' }}</p>
        </div>
      </div>
    </div>
    
    <button 
      @click="handleLogout"
      class="w-full flex items-center justify-center space-x-2 py-3 px-4 border border-red-200 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
    >
      <LogOut class="w-5 h-5" />
      <span class="font-medium">Keluar Aplikasi</span>
    </button>
  </div>
</template>
