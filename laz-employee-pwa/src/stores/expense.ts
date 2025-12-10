import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../api/axios';

export const useExpenseStore = defineStore('expense', () => {
    const claims = ref<any[]>([]);
    const isLoading = ref(false);

    async function fetchClaims() {
        isLoading.value = true;
        try {
            const response = await api.get('/pwa/expense-claims');
            claims.value = response.data.data.data; // Pagination structure
        } catch (e) {
            console.error('Failed to fetch expense claims', e);
        } finally {
            isLoading.value = false;
        }
    }

    async function submitClaim(data: any) {
        isLoading.value = true;
        try {
            await api.post('/pwa/expense-claims', data);
            await fetchClaims();
            return true;
        } catch (e: any) {
            console.error('Failed to submit claim', e);
            throw e;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        claims,
        isLoading,
        fetchClaims,
        submitClaim
    };
});
