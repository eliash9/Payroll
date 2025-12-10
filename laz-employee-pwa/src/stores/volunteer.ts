import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../api/axios';

export const useVolunteerStore = defineStore('volunteer', () => {
    const isVolunteer = ref(false);
    const stats = ref<any>(null);
    const isLoading = ref(false);

    async function fetchDashboard() {
        isLoading.value = true;
        try {
            const response = await api.get('/volunteer/dashboard');
            if (response.data.is_volunteer) {
                isVolunteer.value = true;
                stats.value = response.data.stats;
            } else {
                isVolunteer.value = false;
                stats.value = null;
            }
        } catch (e) {
            console.error('Failed to fetch volunteer dashboard', e);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        isVolunteer,
        stats,
        isLoading,
        fetchDashboard
    };
});
