import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import AttendanceView from '../views/AttendanceView.vue'
import HistoryView from '../views/HistoryView.vue'
import RequestView from '../views/RequestView.vue'
import ExpenseClaimView from '../views/ExpenseClaimView.vue'
import FundraisingView from '../views/FundraisingView.vue'
import ProfileView from '../views/ProfileView.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView,
            meta: { requiresAuth: true }
        },
        {
            path: '/attendance',
            name: 'attendance',
            component: AttendanceView,
            meta: { requiresAuth: true }
        },
        {
            path: '/history',
            name: 'history',
            component: HistoryView,
            meta: { requiresAuth: true }
        },
        {
            path: '/requests',
            name: 'requests',
            component: RequestView,
            meta: { requiresAuth: true }
        },
        {
            path: '/expense-claims',
            name: 'expense-claims',
            component: ExpenseClaimView,
            meta: { requiresAuth: true }
        },
        {
            path: '/fundraising',
            name: 'fundraising',
            component: FundraisingView,
            meta: { requiresAuth: true }
        },
        {
            path: '/profile',
            name: 'profile',
            component: ProfileView,
            meta: { requiresAuth: true }
        },
        {
            path: '/login',
            name: 'login',
            component: LoginView,
            meta: { guest: true }
        }
    ]
})

router.beforeEach((to, _from, next) => {
    const authStore = useAuthStore()

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' })
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'home' })
    } else {
        next()
    }
})

export default router
