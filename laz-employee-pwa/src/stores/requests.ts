import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../api/axios';

export const useRequestStore = defineStore('request', () => {
    const leaveTypes = ref<any[]>([]);
    const leaveHistory = ref<any[]>([]);
    const overtimeHistory = ref<any[]>([]);
    const isLoading = ref(false);

    async function fetchLeaveTypes() {
        try {
            const response = await api.get('/leave/types');
            leaveTypes.value = response.data.data;
        } catch (e) {
            console.error('Failed to fetch leave types', e);
        }
    }

    async function fetchLeaveHistory() {
        try {
            const response = await api.get('/leave/requests');
            leaveHistory.value = response.data.data;
        } catch (e) {
            console.error('Failed to fetch leave history', e);
        }
    }

    async function submitLeave(data: any) {
        isLoading.value = true;
        try {
            await api.post('/leave/requests', data);
            await fetchLeaveHistory();
            return true;
        } catch (e: any) {
            console.error('Failed to submit leave', e);
            throw e;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchOvertimeHistory() {
        try {
            const response = await api.get('/overtime/requests');
            overtimeHistory.value = response.data.data;
        } catch (e) {
            console.error('Failed to fetch overtime history', e);
        }
    }

    async function submitOvertime(data: any) {
        isLoading.value = true;
        try {
            await api.post('/overtime/requests', data);
            await fetchOvertimeHistory();
            return true;
        } catch (e: any) {
            console.error('Failed to submit overtime', e);
            throw e;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        leaveTypes,
        leaveHistory,
        overtimeHistory,
        isLoading,
        fetchLeaveTypes,
        fetchLeaveHistory,
        submitLeave,
        fetchOvertimeHistory,
        submitOvertime
    };
});
