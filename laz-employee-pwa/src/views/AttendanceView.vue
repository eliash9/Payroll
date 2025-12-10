<script setup lang="ts">
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { useAttendanceStore } from '../stores/attendance';
import { MapPin, Clock } from 'lucide-vue-next';

const attendanceStore = useAttendanceStore();

const currentTime = ref('');
const workDuration = ref('');
let timer: any = null;

const todayDocs = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return attendanceStore.history.filter(log => log.date === today);
});

onMounted(() => {
  attendanceStore.checkStatus();
  attendanceStore.startCheckLocation();
  updateTime();
  timer = setInterval(updateTime, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});

const updateTime = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

  if (attendanceStore.isClockedIn && attendanceStore.lastLog?.timestamp) {
    const start = new Date(attendanceStore.lastLog.timestamp);
    const diff = now.getTime() - start.getTime();
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    workDuration.value = `${hours}j ${minutes}m ${seconds}d`;
  } else {
    workDuration.value = '';
  }
};

const formatTime = (isoString: string) => {
  return new Date(isoString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Absensi</h1>

    <!-- Status Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 text-center">
      <p class="text-gray-500 mb-2">Waktu Saat Ini</p>
      <h2 class="text-3xl font-bold text-gray-800 mb-4 font-mono">{{ currentTime }}</h2>

      <div 
        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium mb-4"
        :class="attendanceStore.isClockedIn ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
      >
        {{ attendanceStore.isClockedIn ? 'Sedang Bekerja' : 'Belum Absen Masuk' }}
      </div>
      
      <div v-if="attendanceStore.isClockedIn" class="mt-2">
        <p class="text-xs text-gray-500 mb-1">Durasi Kerja</p>
        <p class="text-xl font-bold text-primary">{{ workDuration }}</p>
      </div>

      <div v-if="attendanceStore.lastLog && !attendanceStore.isClockedIn" class="text-sm text-gray-500 mt-2">
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
          : 'bg-primary hover:bg-[#1f4653] text-white'"
      >
        <Clock class="w-12 h-12 mb-2" />
        <span class="text-xl font-bold">
          {{ !attendanceStore.isLocationReady ? 'Mencari Lokasi...' : (attendanceStore.isLoading ? 'Processing...' : (attendanceStore.isClockedIn ? 'Clock OUT' : 'Clock IN')) }}
        </span>
      </button>
    </div>

    <!-- Location Info -->
    <div class="bg-primary/5 rounded-lg p-4 mb-4">
       <div class="flex items-start space-x-3 mb-3">
          <MapPin class="w-5 h-5 text-primary mt-0.5" />
          <div>
            <h3 class="font-medium text-gray-900">Lokasi Anda</h3>
            <p class="text-sm text-primary mt-1 font-mono">
              {{ attendanceStore.currentLocation }}
            </p>
          </div>
       </div>

       <div v-if="attendanceStore.allowedLocations.length > 0">
           <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Lokasi Diizinkan</h4>
           <ul class="space-y-2">
              <li v-for="(loc, idx) in attendanceStore.allowedLocations" :key="idx" 
                  class="flex justify-between items-center text-sm p-2 bg-white rounded border"
                  :class="{'border-green-500 bg-green-50': attendanceStore.nearestLocation?.name === loc.name && attendanceStore.isLocationValid}"
              >
                  <span class="font-medium text-gray-700">{{ loc.name }}</span>
                  <span class="text-xs text-gray-500">{{ loc.radius }}m</span>
              </li>
           </ul>
           <div v-if="!attendanceStore.isLocationValid && attendanceStore.isLocationReady" class="mt-2 text-xs text-red-600 flex items-center gap-1">
              <MapPin class="w-3 h-3" />
              <span>Anda berada di luar jangkauan ({{ Math.round(attendanceStore.nearestLocation?.distance || 0) }}m)</span>
           </div>
           <div v-if="attendanceStore.isLocationValid" class="mt-2 text-xs text-green-600 flex items-center gap-1">
              <MapPin class="w-3 h-3" />
              <span>Anda berada dalam jangkauan</span>
           </div>
       </div>
    </div>

    <!-- Error Feedback -->
    <div v-if="attendanceStore.error" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
      {{ attendanceStore.error }}
    </div>

    <!-- History List -->
    <div class="mt-8">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Hari Ini</h2>
      <div v-if="todayDocs.length === 0" class="text-gray-500 text-center py-4">
        Belum ada riwayat absensi hari ini.
      </div>
      <div v-else class="space-y-3">
        <div v-for="log in todayDocs" :key="log.id" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center">
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
