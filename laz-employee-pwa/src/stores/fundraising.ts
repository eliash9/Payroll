import { defineStore } from 'pinia';
import { ref } from 'vue';
import { db, type FundraisingTransaction } from '../db';
import api from '../api/axios';

export const useFundraisingStore = defineStore('fundraising', () => {
    const isLoading = ref(false);
    const recentTransactions = ref<FundraisingTransaction[]>([]);

    async function addTransaction(transaction: Omit<FundraisingTransaction, 'id' | 'timestamp' | 'status'>) {
        isLoading.value = true;
        try {
            const newTx: FundraisingTransaction = {
                ...transaction,
                timestamp: new Date().toISOString(),
                status: 'pending'
            };

            // Save to local DB
            const id = await db.fundraising.add(newTx);
            newTx.id = id as number;

            recentTransactions.value.unshift(newTx);

            // Try to sync immediately
            await syncTransaction(newTx);
        } catch (error) {
            console.error('Add transaction failed', error);
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    async function syncTransaction(tx: FundraisingTransaction) {
        if (!navigator.onLine) return;

        try {
            await api.post('/fundraising/transactions', {
                donor_name: tx.donor_name,
                type: tx.type,
                amount: tx.amount,
                notes: tx.notes,
                timestamp: tx.timestamp
            });

            // Update local status
            if (tx.id) {
                await db.fundraising.update(tx.id, { status: 'synced', synced_at: new Date().toISOString() });
            }

            // Update in list if exists
            const index = recentTransactions.value.findIndex(t => t.id === tx.id);
            if (index !== -1 && recentTransactions.value[index]) {
                recentTransactions.value[index].status = 'synced';
            }
        } catch (error) {
            console.error('Sync transaction failed', error);
        }
    }

    async function loadRecent() {
        recentTransactions.value = await db.fundraising.orderBy('timestamp').reverse().limit(10).toArray();
    }

    return {
        isLoading,
        recentTransactions,
        addTransaction,
        loadRecent
    };
});
