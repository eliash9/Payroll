<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useExpenseStore } from '../stores/expense';
import { Plus, X, Receipt, CheckCircle, XCircle, Clock } from 'lucide-vue-next';

const store = useExpenseStore();
const showForm = ref(false);

const form = ref({
  date: new Date().toISOString().split('T')[0],
  amount: 0,
  description: '',
  receipt_url: '' // Placeholder for now
});

onMounted(() => {
  store.fetchClaims();
});

const handleSubmit = async () => {
  try {
    await store.submitClaim(form.value);
    showForm.value = false;
    form.value = { date: new Date().toISOString().split('T')[0], amount: 0, description: '', receipt_url: '' };
    alert('Klaim berhasil diajukan');
  } catch (e: any) {
    alert('Gagal mengajukan klaim');
  }
};

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'approved': return 'text-green-600 bg-green-50 border-green-200';
        case 'rejected': return 'text-red-600 bg-red-50 border-red-200';
        default: return 'text-orange-600 bg-orange-50 border-orange-200';
    }
}
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Klaim Pengeluaran</h1>

    <!-- Action Button -->
    <button 
      @click="showForm = true"
      class="btn-primary-soft flex items-center justify-center gap-2 mb-6"
    >
      <Plus class="w-5 h-5" />
      Ajukan Klaim Baru
    </button>

    <!-- History List -->
    <div class="space-y-4">
      <div v-if="store.claims.length === 0 && !store.isLoading" class="text-center text-gray-500 py-10">
        Belum ada riwayat klaim pengeluaran.
      </div>
      
      <div v-for="claim in store.claims" :key="claim.id" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="text-xs text-gray-500 mb-1">{{ formatDate(claim.date) }}</p>
                <h3 class="font-bold text-gray-800 text-lg">{{ formatCurrency(claim.amount) }}</h3>
            </div>
            <div class="px-3 py-1 rounded-full text-xs font-medium border flex items-center gap-1" :class="getStatusColor(claim.status)">
                <CheckCircle v-if="claim.status === 'approved'" class="w-3 h-3" />
                <XCircle v-else-if="claim.status === 'rejected'" class="w-3 h-3" />
                <Clock v-else class="w-3 h-3" />
                <span class="capitalize">{{ claim.status === 'pending' ? 'Menunggu' : (claim.status === 'approved' ? 'Disetujui' : 'Ditolak') }}</span>
            </div>
        </div>
        <p class="text-gray-600 text-sm bg-gray-50 p-2 rounded-lg mt-2">{{ claim.description }}</p>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
      <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6 relative animate-slide-up">
        <button @click="showForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
          <X class="w-6 h-6" />
        </button>
        
        <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center gap-2">
            <Receipt class="w-6 h-6 text-primary" />
            Form Klaim
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input v-model="form.date" type="date" required class="input-soft" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
            <input v-model="form.amount" type="number" min="0" required class="input-soft" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea v-model="form.description" rows="3" required class="input-soft" placeholder="Contoh: Bensin operasional ke cabang X"></textarea>
          </div>
          <!-- Upload receipt placeholder -->
           <div class="p-4 border-2 border-dashed border-gray-300 rounded-xl text-center text-gray-400 text-sm">
              <p>Upload Struk / Bukti (Segera Hadir)</p>
           </div>

          <button type="submit" :disabled="store.isLoading" class="btn-primary-soft">
            {{ store.isLoading ? 'Mengirim...' : 'Kirim Klaim' }}
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
