import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { db, type AttendanceLog } from '../db';
import api from '../api/axios';
import { useGeolocation } from '@vueuse/core';

export const useAttendanceStore = defineStore('attendance', () => {
    const { coords, resume } = useGeolocation();
    const isLocationReady = computed(() => coords.value.latitude !== Infinity && coords.value.longitude !== Infinity);
    const currentLocation = computed(() => isLocationReady.value
        ? `${coords.value.latitude.toFixed(6)}, ${coords.value.longitude.toFixed(6)}`
        : 'Mencari lokasi...');
    const isClockedIn = ref(false);
    const lastLog = ref<AttendanceLog | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const history = ref<any[]>([]);
    const summaries = ref<any[]>([]);

    async function fetchSummary(month?: number, year?: number) {
        isLoading.value = true;
        try {
            const params: any = {};
            if (month) params.month = month;
            if (year) params.year = year;

            const response = await api.get('/attendance/summary', { params });
            summaries.value = response.data.data;
        } catch (e) {
            console.error('Failed to fetch summary', e);
            error.value = 'Gagal memuat riwayat.';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchHistory() {
        try {
            const response = await api.get('/attendance/history');
            history.value = response.data.data;
        } catch (e) {
            console.error('Failed to fetch history', e);
        }
    }

    async function checkStatus() {
        error.value = null;
        try {
            // 1. Check local DB first
            const lastLocal = await db.attendance.orderBy('timestamp').last();

            // 2. Fetch latest from backend to be sure
            await fetchHistory();

            if (history.value && history.value.length > 0) {
                const latest = history.value[0];
                // Check if today
                const today = new Date().toISOString().split('T')[0];
                if (latest.date === today) {
                    if (latest.type === 'clock_out') {
                        isClockedIn.value = false;
                        lastLog.value = {
                            type: 'clock_out',
                            timestamp: latest.timestamp,
                            latitude: latest.latitude,
                            longitude: latest.longitude,
                            status: 'synced'
                        };
                    } else {
                        isClockedIn.value = true;
                        lastLog.value = {
                            type: 'clock_in',
                            timestamp: latest.timestamp,
                            latitude: latest.latitude,
                            longitude: latest.longitude,
                            status: 'synced'
                        };
                    }
                    return;
                }
            }

            // Fallback to local if no backend data for today
            if (lastLocal && lastLocal.type === 'clock_in') {
                isClockedIn.value = true;
                lastLog.value = lastLocal;
            } else {
                isClockedIn.value = false;
                lastLog.value = lastLocal || null;
            }
        } catch (e) {
            console.error('Failed to check status', e);
            // Fallback to local on error
            const last = await db.attendance.orderBy('timestamp').last();
            if (last && last.type === 'clock_in') {
                isClockedIn.value = true;
                lastLog.value = last;
            } else {
                isClockedIn.value = false;
                lastLog.value = last || null;
            }
        }
    }

    const allowedLocations = ref<any[]>([]);

    async function fetchAllowedLocations() {
        try {
            const response = await api.get('/attendance/allowed-locations');
            allowedLocations.value = response.data.data;
        } catch (e) {
            console.error('Failed to fetch allowed locations', e);
        }
    }

    function calculateDistance(lat1: number, lon1: number, lat2: number, lon2: number) {
        if (!lat1 || !lon1 || !lat2 || !lon2) return Infinity;
        const R = 6371e3; // metres
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c;
    }

    const startCheckLocation = async () => {
        await fetchAllowedLocations();
    }

    // Check if current location is valid
    const isLocationValid = computed(() => {
        if (!isLocationReady.value || allowedLocations.value.length === 0) return false;

        for (const loc of allowedLocations.value) {
            const dist = calculateDistance(coords.value.latitude, coords.value.longitude, parseFloat(loc.latitude), parseFloat(loc.longitude));
            if (dist <= loc.radius) return true;
        }
        return false;
    });

    const nearestLocation = computed(() => {
        if (!isLocationReady.value || allowedLocations.value.length === 0) return null;
        let min = Infinity;
        let nearest = null;
        for (const loc of allowedLocations.value) {
            const dist = calculateDistance(coords.value.latitude, coords.value.longitude, parseFloat(loc.latitude), parseFloat(loc.longitude));
            if (dist < min) {
                min = dist;
                nearest = { ...loc, distance: dist };
            }
        }
        return nearest;
    });

    async function syncLog(log: AttendanceLog) {
        if (!navigator.onLine) return;

        try {
            const endpoint = log.type === 'clock_in' ? '/attendance/clock-in' : '/attendance/clock-out';

            // Validate coordinates before sending
            if (!log.latitude || !log.longitude || log.latitude === Infinity || log.longitude === Infinity) {
                throw new Error('Data lokasi tidak valid untuk sinkronisasi.');
            }

            await api.post(endpoint, {
                timestamp: log.timestamp,
                latitude: log.latitude,
                longitude: log.longitude
            });

            // Update local status
            await db.attendance.update(log.id!, { status: 'synced', synced_at: new Date().toISOString() });

            // Refresh history after successful sync
            await fetchHistory();
        } catch (err: any) {
            console.error('Sync failed', err.response?.data || err.message);

            // If server says "Already clocked in/out", we can consider it synced
            if (err.response?.status === 400 && err.response?.data?.message?.includes('Already')) {
                await db.attendance.update(log.id!, { status: 'synced', synced_at: new Date().toISOString() });
                await fetchHistory(); // Also refresh history here
            } else {
                error.value = `Sync gagal: ${err.response?.data?.message || err.message}`;
            }
        }
    }

    async function clockIn() {
        resume(); // Ensure geolocation is active
        isLoading.value = true;
        error.value = null;
        try {
            if (coords.value.latitude === Infinity || coords.value.longitude === Infinity) {
                throw new Error('Lokasi belum ditemukan. Pastikan GPS aktif dan tunggu sebentar.');
            }

            // Check radius logic frontend
            if (allowedLocations.value.length > 0 && !isLocationValid.value) {
                const nearest = nearestLocation.value;
                const msg = nearest
                    ? `Anda berada di luar jangkauan. Lokasi terdekat: ${nearest.name} (${Math.round(nearest.distance)}m)`
                    : 'Anda berada di luar jangkauan lokasi kerja.';
                throw new Error(msg);
            }

            const log: AttendanceLog = {
                type: 'clock_in',
                timestamp: new Date().toISOString(),
                latitude: coords.value.latitude,
                longitude: coords.value.longitude,
                status: 'pending'
            };

            // Save to local DB
            const id = await db.attendance.add(log);
            log.id = id as number;

            isClockedIn.value = true;
            lastLog.value = log;

            // Try to sync immediately
            await syncLog(log);
        } catch (err: any) {
            console.error('Clock in failed', err);
            error.value = err.message || 'Gagal melakukan absensi';
        } finally {
            isLoading.value = false;
        }
    }

    // Keep clockOut mostly same but add radius check? Usually clock out doesn't need radius check strictly, but let's keep it consistent if needed. 
    // User requested "tolak cekin jika tidak dalam radius". Cekin usually means Clock In.
    // Clock OUT might be allowed anywhere or same rules. Let's enforce for both for consistency unless specified.

    async function clockOut() {
        resume();
        isLoading.value = true;
        error.value = null;
        try {
            if (coords.value.latitude === Infinity || coords.value.longitude === Infinity) {
                throw new Error('Lokasi belum ditemukan. Pastikan GPS aktif dan tunggu sebentar.');
            }

            // Optional: enforce radius for clock out too? Often yes.
            if (allowedLocations.value.length > 0 && !isLocationValid.value) {
                const nearest = nearestLocation.value;
                const msg = nearest
                    ? `Anda berada di luar jangkauan. Lokasi terdekat: ${nearest.name} (${Math.round(nearest.distance)}m)`
                    : 'Anda berada di luar jangkauan lokasi kerja.';
                throw new Error(msg);
            }

            const log: AttendanceLog = {
                type: 'clock_out',
                timestamp: new Date().toISOString(),
                latitude: coords.value.latitude,
                longitude: coords.value.longitude,
                status: 'pending'
            };

            const id = await db.attendance.add(log);
            log.id = id as number;

            isClockedIn.value = false;
            lastLog.value = log;

            await syncLog(log);
        } catch (err: any) {
            console.error('Clock out failed', err);
            error.value = err.message || 'Gagal melakukan absensi';
        } finally {
            isLoading.value = false;
        }
    }

    return {
        isClockedIn,
        lastLog,
        history,
        isLoading,
        error,
        checkStatus,
        fetchHistory,
        clockIn,
        clockOut,
        isLocationReady,
        currentLocation,
        summaries,
        fetchSummary,
        allowedLocations,
        fetchAllowedLocations,
        isLocationValid,
        nearestLocation,
        startCheckLocation
    };
});
