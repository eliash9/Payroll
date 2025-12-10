import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../api/axios';
import router from '../router';

interface User {
    id: number;
    name: string;
    email: string;
    position?: { name: string };
    department?: { name: string };
    branch?: { name: string };
    is_volunteer?: boolean;
    // Add other fields as needed
}

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('auth_token'));
    const user = ref<User | null>(null);
    const isAuthenticated = computed(() => !!token.value);

    async function login(login_id: string, password: string) {
        try {
            // 1. Get CSRF cookie first (Sanctum)
            // Note: csrf-cookie is usually at root, not under /api
            const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';
            const rootURL = baseURL.replace(/\/api\/?$/, ''); // Remove trailing /api or /api/
            await api.get(`${rootURL}/sanctum/csrf-cookie`);

            // 2. Login to get token
            const response = await api.post('/auth/token', {
                login_id,
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
            // Fetch detailed employee profile instead of just user
            // The endpoint is /api/employee/profile
            const response = await api.get('/employee/profile');
            // The API returns { user: {...}, employee: {...} }
            // We'll flatten it or store it as is. For simplicity let's store the combined object
            user.value = { ...response.data.user, ...response.data.employee };
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
