<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useAttendanceStore } from '../stores/attendance';
import { Calendar, Filter, Clock } from 'lucide-vue-next';

const store = useAttendanceStore();
const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(new Date().getFullYear());

const months = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' }
];

const years = computed(() => {
  const current = new Date().getFullYear();
  return [current, current - 1, current - 2];
});

onMounted(() => {
  store.fetchSummary(selectedMonth.value, selectedYear.value);
});

const applyFilter = () => {
  store.fetchSummary(selectedMonth.value, selectedYear.value);
};

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
};

const formatTime = (dateTimeStr: string | null) => {
  if (!dateTimeStr) return '--:--';
  return new Date(dateTimeStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'present': return 'bg-green-100 text-green-700';
    case 'absent': return 'bg-red-100 text-red-700';
    case 'late': return 'bg-orange-100 text-orange-700';
    default: return 'bg-gray-100 text-gray-700';
  }
};

const getStatusLabel = (status: string) => {
    switch (status) {
    case 'present': return 'Hadir';
    case 'absent': return 'Alpa';
    case 'late': return 'Terlambat';
    default: return status;
  }
}
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Riwayat Absensi</h1>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
      <div class="flex gap-4">
        <div class="w-1/2">
          <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
          <select v-model="selectedMonth" class="w-full text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary">
            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
          </select>
        </div>
        <div class="w-1/2">
          <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
          <select v-model="selectedYear" class="w-full text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary">
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
      </div>
      <button 
        @click="applyFilter"
        class="w-full mt-4 bg-primary text-white py-2 rounded-lg text-sm font-medium hover:bg-[#1f4653] flex items-center justify-center gap-2"
        :disabled="store.isLoading"
      >
        <Filter class="w-4 h-4" />
        {{ store.isLoading ? 'Memuat...' : 'Terapkan Filter' }}
      </button>
    </div>

    <!-- Summary List -->
    <div v-if="store.summaries.length === 0 && !store.isLoading" class="text-center py-10 bg-white rounded-xl shadow-sm">
      <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
        <Calendar class="w-8 h-8 text-gray-400" />
      </div>
      <p class="text-gray-500">Tidak ada data absensi untuk periode ini.</p>
    </div>

    <div v-else class="space-y-3">
      <div v-for="log in store.summaries" :key="log.id" class="bg-white p-4 rounded-xl shadow-sm border-l-4" :class="log.status === 'present' ? 'border-primary' : 'border-red-500'">
        <div class="flex justify-between items-start mb-3">
          <div>
            <span class="text-xs font-bold text-gray-400 uppercase">{{ formatDate(log.work_date) }}</span>
            <div class="mt-1">
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" :class="getStatusColor(log.status)">
                    {{ getStatusLabel(log.status) }}
                </span>
            </div>
          </div>
          <div class="text-right">
             <span class="text-xs text-gray-400">Durasi</span>
             <p class="font-bold text-gray-800">{{ Math.floor(log.worked_minutes / 60) }}j {{ log.worked_minutes % 60 }}m</p>
          </div>
        </div>
        
        <div class="flex justify-between items-center text-sm border-t pt-3 border-gray-100">
           <div class="flex items-center gap-2">
               <div class="p-1.5 bg-green-50 rounded-full">
                   <Clock class="w-3 h-3 text-green-600" />
               </div>
               <div>
                   <p class="text-xs text-gray-500">Masuk</p>
                   <p class="font-medium text-gray-900">{{ formatTime(log.check_in) }}</p>
               </div>
           </div>
           <div class="w-px h-8 bg-gray-100"></div>
           <div class="flex items-center gap-2 text-right">
               <div>
                   <p class="text-xs text-gray-500">Pulang</p>
                   <p class="font-medium text-gray-900">{{ formatTime(log.check_out) }}</p>
               </div>
               <div class="p-1.5 bg-red-50 rounded-full">
                   <Clock class="w-3 h-3 text-red-600" />
               </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>
