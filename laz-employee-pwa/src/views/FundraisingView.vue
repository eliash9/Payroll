<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useFundraisingStore } from '../stores/fundraising';
import { CheckCircle, Clock } from 'lucide-vue-next';

const store = useFundraisingStore();

const form = ref({
  donor_name: '',
  type: 'zakat' as const,
  amount: 0,
  notes: ''
});

const successMsg = ref('');

onMounted(() => {
  store.loadRecent();
});

const handleSubmit = async () => {
  try {
    await store.addTransaction({ ...form.value });
    successMsg.value = 'Transaksi berhasil disimpan!';
    form.value = { donor_name: '', type: 'zakat', amount: 0, notes: '' };
    setTimeout(() => successMsg.value = '', 3000);
  } catch (e) {
    alert('Gagal menyimpan transaksi');
  }
};

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
};
</script>

<template>
  <div class="p-4 pb-20">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Donasi</h1>

    <!-- Input Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Input Transaksi</h2>
      
      <div v-if="successMsg" class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
        {{ successMsg }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Donatur</label>
          <input v-model="form.donor_name" type="text" required class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Donasi</label>
          <select v-model="form.type" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <option value="zakat">Zakat</option>
            <option value="infaq">Infaq</option>
            <option value="sadaqah">Sadaqah</option>
            <option value="wakaf">Wakaf</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
          <input v-model="form.amount" type="number" min="1000" required class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
          <textarea v-model="form.notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>

        <button 
          type="submit" 
          :disabled="store.isLoading"
          class="w-full py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50"
        >
          {{ store.isLoading ? 'Menyimpan...' : 'Simpan Transaksi' }}
        </button>
      </form>
    </div>

    <!-- Recent History -->
    <div>
      <h2 class="text-lg font-semibold mb-4 text-gray-800">Riwayat Terkini</h2>
      <div class="space-y-3">
        <div v-for="tx in store.recentTransactions" :key="tx.id" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center">
          <div>
            <p class="font-medium text-gray-900">{{ tx.donor_name }}</p>
            <p class="text-sm text-gray-500 capitalize">{{ tx.type }} • {{ formatCurrency(tx.amount) }}</p>
          </div>
          <div class="flex items-center">
            <CheckCircle v-if="tx.status === 'synced'" class="w-5 h-5 text-green-500" />
            <Clock v-else class="w-5 h-5 text-orange-500" />
          </div>
        </div>
        <div v-if="store.recentTransactions.length === 0" class="text-center text-gray-500 py-4">
          Belum ada transaksi baru.
        </div>
      </div>
    </div>
  </div>
</template>
