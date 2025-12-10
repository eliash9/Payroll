<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRequestStore } from '../stores/requests';
import { Calendar, Clock, Plus, X } from 'lucide-vue-next';

const store = useRequestStore();
const activeTab = ref<'leave' | 'overtime'>('leave');
const showForm = ref(false);

// Leave Form Data
const leaveForm = ref({
  leave_type_id: '',
  start_date: '',
  end_date: '',
  reason: ''
});

// Overtime Form Data
const overtimeForm = ref({
  work_date: '',
  start_time: '',
  end_time: '',
  reason: ''
});

onMounted(() => {
  store.fetchLeaveTypes();
  store.fetchLeaveHistory();
  store.fetchOvertimeHistory();
});

const handleLeaveSubmit = async () => {
  try {
    await store.submitLeave(leaveForm.value);
    showForm.value = false;
    leaveForm.value = { leave_type_id: '', start_date: '', end_date: '', reason: '' };
    alert('Pengajuan cuti berhasil dikirim');
  } catch (e: any) {
    alert('Gagal mengirim pengajuan: ' + (e.response?.data?.message || 'Error'));
  }
};

const handleOvertimeSubmit = async () => {
    try {
    await store.submitOvertime(overtimeForm.value);
    showForm.value = false;
    overtimeForm.value = { work_date: '', start_time: '', end_time: '', reason: '' };
    alert('Pengajuan lembur berhasil dikirim');
  } catch (e: any) {
    alert('Gagal mengirim pengajuan: ' + (e.response?.data?.message || 'Error'));
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'approved': return 'bg-green-100 text-green-700';
    case 'rejected': return 'bg-red-100 text-red-700';
    default: return 'bg-orange-100 text-orange-700'; // pending
  }
};

const getStatusLabel = (status: string) => {
    switch (status) {
    case 'approved': return 'Disetujui';
    case 'rejected': return 'Ditolak';
    case 'pending': return 'Menunggu';
    default: return status;
  }
}

const getBorderClass = (status: string) => {
  const color = getStatusColor(status);
  const parts = color.split(' ');
  return parts[0] ? parts[0].replace('bg-', 'border-') : 'border-gray-200';
};
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Pengajuan</h1>

    <!-- Tabs -->
    <div class="flex p-1 bg-gray-100 rounded-xl mb-6">
      <button 
        @click="activeTab = 'leave'"
        class="flex-1 py-2 text-sm font-medium rounded-lg transition-all"
        :class="activeTab === 'leave' ? 'bg-white shadow-sm text-primary' : 'text-gray-500'"
      >
        Cuti & Izin
      </button>
      <button 
        @click="activeTab = 'overtime'"
        class="flex-1 py-2 text-sm font-medium rounded-lg transition-all"
        :class="activeTab === 'overtime' ? 'bg-white shadow-sm text-primary' : 'text-gray-500'"
      >
        Lembur
      </button>
    </div>

    <!-- Action Button -->
    <button 
      @click="showForm = true"
      class="btn-primary-soft flex items-center justify-center gap-2 mb-6"
    >
      <Plus class="w-5 h-5" />
      {{ activeTab === 'leave' ? 'Ajukan Cuti Baru' : 'Ajukan Lembur Baru' }}
    </button>

    <!-- List -->
    <div class="space-y-4">
      <div v-if="activeTab === 'leave'">
        <div v-if="store.leaveHistory.length === 0" class="text-center text-gray-500 py-8">
            Belum ada riwayat pengajuan cuti.
        </div>
        <div v-for="item in store.leaveHistory" :key="item.id" class="bg-white p-4 rounded-xl shadow-sm border-l-4" :class="getBorderClass(item.status)">
          <div class="flex justify-between items-start mb-2">
            <div>
              <h3 class="font-bold text-gray-800">{{ item.leave_type_name }}</h3>
              <p class="text-xs text-gray-500">Diajukan: {{ formatDate(item.created_at) }}</p>
            </div>
            <span class="px-2 py-1 rounded text-xs font-medium capitalize" :class="getStatusColor(item.status)">{{ getStatusLabel(item.status) }}</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <Calendar class="w-4 h-4" />
            <span>{{ formatDate(item.start_date) }} - {{ formatDate(item.end_date) }}</span>
          </div>
          <p v-if="item.reason" class="text-sm text-gray-500 italic">"{{ item.reason }}"</p>
        </div>
      </div>

      <div v-else>
         <div v-if="store.overtimeHistory.length === 0" class="text-center text-gray-500 py-8">
            Belum ada riwayat pengajuan lembur.
        </div>
        <div v-for="item in store.overtimeHistory" :key="item.id" class="bg-white p-4 rounded-xl shadow-sm border-l-4" :class="getBorderClass(item.status)">
          <div class="flex justify-between items-start mb-2">
            <div>
              <h3 class="font-bold text-gray-800">Lembur</h3>
              <p class="text-xs text-gray-500">Diajukan: {{ formatDate(item.created_at) }}</p>
            </div>
            <span class="px-2 py-1 rounded text-xs font-medium capitalize" :class="getStatusColor(item.status)">{{ getStatusLabel(item.status) }}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-600 mb-2">
             <div class="flex items-center gap-2">
                <Calendar class="w-4 h-4" />
                <span>{{ formatDate(item.work_date) }}</span>
             </div>
             <div class="flex items-center gap-2">
                <Clock class="w-4 h-4" />
                <span>{{ item.total_minutes }} menit</span>
             </div>
          </div>
          <p v-if="item.reason" class="text-sm text-gray-500 italic">"{{ item.reason }}"</p>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
      <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6 relative animate-slide-up">
        <button @click="showForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
          <X class="w-6 h-6" />
        </button>
        
        <h2 class="text-xl font-bold mb-4 text-gray-800">
          {{ activeTab === 'leave' ? 'Form Pengajuan Cuti' : 'Form Pengajuan Lembur' }}
        </h2>

        <!-- Leave Form -->
        <form v-if="activeTab === 'leave'" @submit.prevent="handleLeaveSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti</label>
            <select v-model="leaveForm.leave_type_id" required class="input-soft">
              <option value="" disabled>Pilih Jenis Cuti</option>
              <option v-for="type in store.leaveTypes" :key="type.id" :value="type.id">
                {{ type.name }} (Sisa: {{ type.default_quota_days }} hari)
              </option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
              <input v-model="leaveForm.start_date" type="date" required class="input-soft" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
              <input v-model="leaveForm.end_date" type="date" required class="input-soft" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
            <textarea v-model="leaveForm.reason" rows="3" required class="input-soft"></textarea>
          </div>
          <button type="submit" :disabled="store.isLoading" class="btn-primary-soft">
            {{ store.isLoading ? 'Mengirim...' : 'Kirim Pengajuan' }}
          </button>
        </form>

        <!-- Overtime Form -->
        <form v-else @submit.prevent="handleOvertimeSubmit" class="space-y-4">
           <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lembur</label>
              <input v-model="overtimeForm.work_date" type="date" required class="input-soft" />
            </div>
            <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Jam</label>
              <input v-model="overtimeForm.start_time" type="time" required class="input-soft" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Selesai Jam</label>
              <input v-model="overtimeForm.end_time" type="time" required class="input-soft" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan / Pekerjaan</label>
            <textarea v-model="overtimeForm.reason" rows="3" required class="input-soft"></textarea>
          </div>
          <button type="submit" :disabled="store.isLoading" class="btn-primary-soft">
            {{ store.isLoading ? 'Mengirim...' : 'Kirim Pengajuan' }}
          </button>
        </form>

      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
