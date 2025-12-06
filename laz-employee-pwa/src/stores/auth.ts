import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../api/axios';
import router from '../router';

interface User {
    id: number;
    name: string;
    email: string;
    // Add other fields as needed
}

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('auth_token'));
    const user = ref<User | null>(null);
    const isAuthenticated = computed(() => !!token.value);

    async function login(email: string, password: string) {
        try {
            // 1. Get CSRF cookie first (Sanctum)
            // Note: csrf-cookie is usually at root, not under /api
            await api.get('http://localhost:8000/sanctum/csrf-cookie');

            // 2. Login to get token
            const response = await api.post('/auth/token', {
                email,
                password,
                device_name: 'laz-employee-pwa'
            });

            token.value = response.data.token;
            localStorage.setItem('auth_token', token.value as string);

            // 3. Fetch user details
            await fetchUser();

            router.push({ name: 'home' });
        } catch (error) {
            console.error('Login failed', error);
            throw error;
        }
    }

    async function fetchUser() {
        if (!token.value) return;
        try {
            // The endpoint is /api/user, but axios base URL is /api, so we request /user
            const response = await api.get('/user');
            user.value = response.data;
        } catch (error) {
            console.error('Fetch user failed', error);
            logout();
        }
    }

    function logout() {
        token.value = null;
        user.value = null;
        localStorage.removeItem('auth_token');
        router.push({ name: 'login' });
    }

    return {
        token,
        user,
        isAuthenticated,
        login,
        fetchUser,
        logout
    };
});
