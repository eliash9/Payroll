<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useAttendanceStore } from '../stores/attendance';
import { useRouter } from 'vue-router';
import { Clock, HandCoins, ChevronRight, FileText, Receipt, TrendingUp, Wallet, Trophy } from 'lucide-vue-next';
import { useVolunteerStore } from '../stores/volunteer';

const authStore = useAuthStore();
const attendanceStore = useAttendanceStore();
const volunteerStore = useVolunteerStore();
const router = useRouter();

onMounted(async () => {
  if (!authStore.user) await authStore.fetchUser();
  attendanceStore.checkStatus();
  volunteerStore.fetchDashboard();
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
      <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold">
        {{ authStore.user?.name?.charAt(0) || 'U' }}
      </div>
    </div>

    <div v-if="volunteerStore.isVolunteer && volunteerStore.stats" class="mb-6">
       <!-- Volunteer Summary Card -->
       <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-xl p-6 text-white shadow-lg mb-4">
          <div class="flex justify-between items-start mb-4">
             <div>
                <p class="text-white/80 text-sm mb-1">Perolehan Hari Ini</p>
                <h2 class="text-3xl font-bold">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(volunteerStore.stats.today.amount) }}</h2>
             </div>
             <div class="bg-white/20 p-2 rounded-lg">
                <TrendingUp class="w-6 h-6 text-white" />
             </div>
          </div>
          <div class="flex gap-4">
             <div class="bg-black/10 px-3 py-1.5 rounded-lg text-sm flex items-center gap-1.5">
                <Wallet class="w-4 h-4 text-white/90" />
                <span>{{ volunteerStore.stats.today.count }} Transaksi</span>
             </div>
             <div class="bg-black/10 px-3 py-1.5 rounded-lg text-sm flex items-center gap-1.5">
                <Trophy class="w-4 h-4 text-white/90" />
                <span>Peringkat #{{ volunteerStore.stats.rank }}</span>
             </div>
          </div>
       </div>
    </div>

    <!-- Quick Stats / Attendance Status -->
    <div 
      class="rounded-xl p-6 text-white mb-6 shadow-lg transition-transform active:scale-95"
      :class="attendanceStore.isClockedIn ? 'bg-gradient-to-r from-secondary to-[#8dab22]' : 'bg-gradient-to-r from-primary to-[#1f4653]'"
      @click="navigate('/attendance')"
    >
      <div class="flex justify-between items-start mb-4">
        <div>
          <p class="text-white/80 text-sm mb-1">Status Absensi</p>
          <h2 class="text-2xl font-bold">{{ attendanceStore.isClockedIn ? 'Sedang Bekerja' : 'Belum Masuk' }}</h2>
        </div>
        <div class="bg-white/20 p-2 rounded-lg">
          <Clock class="w-6 h-6 text-white" />
        </div>
      </div>
      <div class="flex items-center text-sm text-white/70">
        <span>{{ attendanceStore.isClockedIn ? 'Ketuk untuk absen pulang' : 'Ketuk untuk absen masuk' }}</span>
        <ChevronRight class="w-4 h-4 ml-1" />
      </div>
    </div>

    <!-- Quick Actions -->
    <h2 class="text-lg font-bold text-gray-800 mb-4">Menu Cepat</h2>
    <div class="grid grid-cols-3 gap-6 mb-6">
      <button 
        v-if="volunteerStore.isVolunteer"
        @click="navigate('/fundraising')"
        class="flex flex-col items-center justify-center space-y-2 group"
      >
        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-orange-600 group-active:scale-95 transition-transform border border-gray-100">
          <HandCoins class="w-7 h-7" />
        </div>
        <span class="text-xs font-medium text-gray-700 text-center">Donasi</span>
      </button>

      <button 
        @click="navigate('/history')"
        class="flex flex-col items-center justify-center space-y-2 group"
      >
        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-purple-600 group-active:scale-95 transition-transform border border-gray-100">
          <Clock class="w-7 h-7" />
        </div>
        <span class="text-xs font-medium text-gray-700 text-center">Riwayat</span>
      </button>
      
      <button 
        @click="navigate('/requests')"
        class="flex flex-col items-center justify-center space-y-2 group"
      >
        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-teal-600 group-active:scale-95 transition-transform border border-gray-100">
          <FileText class="w-7 h-7" />
        </div>
        <span class="text-xs font-medium text-gray-700 text-center">Pengajuan</span>
      </button>

      <button 
        @click="navigate('/expense-claims')"
        class="flex flex-col items-center justify-center space-y-2 group"
      >
        <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-pink-600 group-active:scale-95 transition-transform border border-gray-100">
          <Receipt class="w-7 h-7" />
        </div>
        <span class="text-xs font-medium text-gray-700 text-center">Klaim</span>
      </button>
    </div>

    <!-- Recent Activity Placeholder -->
    <h2 class="text-lg font-bold text-gray-800 mb-4">Info Terbaru</h2>
    <div class="bg-white rounded-xl shadow-sm p-4">
      <p class="text-gray-500 text-sm">Tidak ada pengumuman baru.</p>
    </div>
  </div>
</template>
