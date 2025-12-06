<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useAttendanceStore } from '../stores/attendance';
import { useRouter } from 'vue-router';
import { Clock, HandCoins, ChevronRight } from 'lucide-vue-next';

const authStore = useAuthStore();
const attendanceStore = useAttendanceStore();
const router = useRouter();

onMounted(() => {
  if (!authStore.user) authStore.fetchUser();
  attendanceStore.checkStatus();
});

const navigate = (path: string) => router.push(path);
</script>

<template>
  <div class="p-4 pb-20">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <p class="text-gray-500 text-sm">Selamat Datang,</p>
        <h1 class="text-xl font-bold text-gray-900">{{ authStore.user?.name || 'Karyawan' }}</h1>
      </div>
      <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
        {{ authStore.user?.name?.charAt(0) || 'U' }}
      </div>
    </div>

    <!-- Quick Stats / Attendance Status -->
    <div 
      class="rounded-xl p-6 text-white mb-6 shadow-lg transition-transform active:scale-95"
      :class="attendanceStore.isClockedIn ? 'bg-gradient-to-r from-green-500 to-emerald-600' : 'bg-gradient-to-r from-blue-600 to-indigo-700'"
      @click="navigate('/attendance')"
    >
      <div class="flex justify-between items-start mb-4">
        <div>
          <p class="text-blue-100 text-sm mb-1">Status Absensi</p>
          <h2 class="text-2xl font-bold">{{ attendanceStore.isClockedIn ? 'Sedang Bekerja' : 'Belum Masuk' }}</h2>
        </div>
        <div class="bg-white/20 p-2 rounded-lg">
          <Clock class="w-6 h-6 text-white" />
        </div>
      </div>
      <div class="flex items-center text-sm text-blue-50">
        <span>{{ attendanceStore.isClockedIn ? 'Ketuk untuk absen pulang' : 'Ketuk untuk absen masuk' }}</span>
        <ChevronRight class="w-4 h-4 ml-1" />
      </div>
    </div>

    <!-- Quick Actions -->
    <h2 class="text-lg font-bold text-gray-800 mb-4">Menu Cepat</h2>
    <div class="grid grid-cols-2 gap-4 mb-6">
      <button 
        @click="navigate('/fundraising')"
        class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center justify-center space-y-2 active:bg-gray-50"
      >
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-orange-600">
          <HandCoins class="w-6 h-6" />
        </div>
        <span class="font-medium text-gray-700">Input Donasi</span>
      </button>

      <button 
        @click="navigate('/profile')"
        class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center justify-center space-y-2 active:bg-gray-50"
      >
        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
          <Clock class="w-6 h-6" />
        </div>
        <span class="font-medium text-gray-700">Riwayat Absen</span>
      </button>
    </div>

    <!-- Recent Activity Placeholder -->
    <h2 class="text-lg font-bold text-gray-800 mb-4">Info Terbaru</h2>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <p class="text-gray-500 text-sm">Tidak ada pengumuman baru.</p>
    </div>
  </div>
</template>
