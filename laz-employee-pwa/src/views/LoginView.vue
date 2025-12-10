<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const loginId = ref('');
const password = ref('');
const authStore = useAuthStore();
const isLoading = ref(false);
const errorMsg = ref('');

const handleLogin = async () => {
  isLoading.value = true;
  errorMsg.value = '';
  try {
    await authStore.login(loginId.value, password.value);
  } catch (e: any) {
    errorMsg.value = 'Login failed. Please check your credentials.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-soft px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <div class="text-center mb-8">
        <img src="/logo-new.png" alt="MyLAZ Logo" class="h-24 mx-auto mb-6 object-contain drop-shadow-sm" />
        <h1 class="text-3xl font-bold text-primary mb-2">MyLAZ</h1>
        <p class="text-gray-500 font-medium">Sistem Informasi Karyawan & Absensi</p>
        <p class="text-sm text-gray-400 mt-2">Masuk untuk memulai hari produktif Anda</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="login_id" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Email / NIP</label>
          <input
            id="login_id"
            v-model="loginId"
            type="text"
            required
            class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
            placeholder="nama@lazsidogiri.org"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Kata Sandi</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
            placeholder="••••••••"
          />
        </div>

        <div v-if="errorMsg" class="text-red-500 text-sm text-center">
          {{ errorMsg }}
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-primary/30 text-sm font-bold text-white bg-primary hover:bg-[#1f4653] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-all duration-200 transform active:scale-[0.98]"
        >
          <span v-if="isLoading">Sedang Masuk...</span>
          <span v-else>Masuk Sekarang</span>
        </button>
      </form>
    </div>
  </div>
</template>
