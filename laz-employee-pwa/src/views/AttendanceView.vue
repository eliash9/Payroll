<script setup lang="ts">
import { onMounted } from 'vue';
import { useAttendanceStore } from '../stores/attendance';
import { MapPin, Clock } from 'lucide-vue-next';

const attendanceStore = useAttendanceStore();

onMounted(() => {
  attendanceStore.checkStatus();
});

const formatTime = (isoString: string) => {
  return new Date(isoString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Absensi</h1>

    <!-- Status Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 text-center">
      <p class="text-gray-500 mb-2">Status Saat Ini</p>
      <div 
        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium mb-4"
        :class="attendanceStore.isClockedIn ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
      >
        {{ attendanceStore.isClockedIn ? 'Sedang Bekerja' : 'Belum Absen Masuk' }}
      </div>
      
      <div v-if="attendanceStore.lastLog" class="text-sm text-gray-500">
        Terakhir: {{ attendanceStore.lastLog.type === 'clock_in' ? 'Masuk' : 'Keluar' }} 
        pukul {{ formatTime(attendanceStore.lastLog.timestamp) }}
      </div>
    </div>

    <!-- Action Button -->
    <div class="flex justify-center mb-8">
      <button
        @click="attendanceStore.isClockedIn ? attendanceStore.clockOut() : attendanceStore.clockIn()"
        :disabled="attendanceStore.isLoading || !attendanceStore.isLocationReady"
        class="w-48 h-48 rounded-full flex flex-col items-center justify-center shadow-lg transition-transform active:scale-95 focus:outline-none"
        :class="attendanceStore.isClockedIn 
          ? 'bg-red-500 hover:bg-red-600 text-white' 
          : 'bg-blue-600 hover:bg-blue-700 text-white'"
      >
        <Clock class="w-12 h-12 mb-2" />
        <span class="text-xl font-bold">
          {{ !attendanceStore.isLocationReady ? 'Mencari Lokasi...' : (attendanceStore.isLoading ? 'Processing...' : (attendanceStore.isClockedIn ? 'Clock OUT' : 'Clock IN')) }}
        </span>
      </button>
    </div>

    <!-- Location Info -->
    <div class="bg-blue-50 rounded-lg p-4 flex items-start space-x-3">
      <MapPin class="w-5 h-5 text-blue-600 mt-0.5" />
      <div>
        <h3 class="font-medium text-blue-900">Lokasi Anda</h3>
        <p class="text-sm text-blue-700 mt-1">
          {{ attendanceStore.currentLocation }}
        </p>
      </div>
    </div>

    <!-- Error Feedback -->
    <div v-if="attendanceStore.error" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
      {{ attendanceStore.error }}
    </div>

    <!-- History List -->
    <div class="mt-8">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Hari Ini</h2>
      <div v-if="attendanceStore.history.length === 0" class="text-gray-500 text-center py-4">
        Belum ada riwayat absensi.
      </div>
      <div v-else class="space-y-3">
        <div v-for="log in attendanceStore.history" :key="log.id" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center">
          <div>
            <div class="font-medium" :class="log.type === 'clock_in' ? 'text-green-600' : 'text-red-600'">
              {{ log.type === 'clock_in' ? 'Masuk' : 'Keluar' }}
            </div>
            <div class="text-xs text-gray-400 mt-1">{{ log.date }}</div>
          </div>
          <div class="text-right">
            <div class="text-lg font-bold text-gray-800">{{ log.time }}</div>
            <div class="text-xs text-gray-400 mt-1">
              {{ log.latitude && log.longitude ? 'GPS OK' : 'No GPS' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
