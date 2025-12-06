import Dexie, { type Table } from 'dexie';

export interface AttendanceLog {
    id?: number;
    type: 'clock_in' | 'clock_out';
    timestamp: string;
    latitude: number;
    longitude: number;
    photo_path?: string; // Path to stored image blob or base64
    status: 'pending' | 'synced';
    synced_at?: string;
}

export interface FundraisingTransaction {
    id?: number;
    donor_name: string;
    type: 'zakat' | 'infaq' | 'sadaqah' | 'wakaf';
    amount: number;
    notes?: string;
    timestamp: string;
    status: 'pending' | 'synced';
    synced_at?: string;
}

// Singleton instance
const db = new Dexie('LazEmployeeDB') as Dexie & {
    attendance: Table<AttendanceLog>;
    fundraising: Table<FundraisingTransaction>;
};

db.version(1).stores({
    attendance: '++id, type, timestamp, status',
    fundraising: '++id, type, timestamp, status'
});

export { db };
