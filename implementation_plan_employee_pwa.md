# Implementation Plan: Employee Field App (PWA)

## Overview
This document outlines the architecture and implementation plan for a new "Offline-First" Mobile PWA designed for employees in the field. The app will handle Attendance (Absensi), Fundraising, and Salary/Profile viewing.

## Technology Stack
- **Framework**: Vue 3 (Composition API, Script Setup)
- **Build Tool**: Vite
- **PWA Support**: `vite-plugin-pwa` (Zero-config PWA for Vite)
- **Styling**: Tailwind CSS (Mobile First Design)
- **State Management**: Pinia
- **Routing**: Vue Router
- **Local Database (Offline)**: Dexie.js (IndexedDB wrapper) for storing attendance logs and fundraising transactions when offline.
- **Icons**: Lucide Vue
- **HTTP Client**: Axios (with interceptors for auth)

## Core Features

### 1. Authentication
- Login with Email/Password.
- Store Auth Token (Sanctum) securely.
- Auto-login if token exists and is valid.

### 2. Attendance (Absensi)
- **Clock In / Clock Out**:
  - Capture GPS Location (Latitude/Longitude).
  - Capture Timestamp.
  - **Offline Mode**: If no internet, save data to IndexedDB with status `pending`.
  - **Sync**: Auto-sync when internet is restored (using `@vueuse/core` network detection).
- **History**: View recent attendance logs.

### 3. Fundraising
- **Input Transaction**:
  - Form: Donor Name, Type (Zakat/Infaq/etc.), Amount, Notes.
  - **Offline Mode**: Save transaction locally if offline.
- **History**: List of collected funds.

### 4. Employee Data (Data Karyawan & Gaji)
- **Profile**: View basic employee info (Name, Position, Branch).
- **Salary (Gaji)**: View basic salary info or payslip summary (Read-only, requires online connection usually, but can cache last loaded data).

## Backend API Requirements (Laravel)
We need to ensure the `payroll-app` exposes the following API endpoints (via `routes/api.php`):

### Auth
- `POST /api/auth/token` (Existing)
- `GET /api/user` (To get current employee details)

### Attendance
- `POST /api/attendance/clock-in`
- `POST /api/attendance/clock-out`
- `GET /api/attendance/history`

### Fundraising
- `POST /api/fundraising/transactions` (Existing)
- `GET /api/fundraising/history` (Need to filter by current logged-in employee)

### Employee
- `GET /api/employee/profile`
- `GET /api/employee/salary-slip` (Or similar)

## Project Structure (Proposed)
We will create a new Vite project named `laz-employee-pwa` in the root directory.

```
d:\LAZ\Payroll\laz-employee-pwa\
```

## Step-by-Step Implementation

### Phase 1: Setup & Boilerplate
1. Initialize Vite app: `npm create vite@latest laz-employee-pwa -- --template vue-ts`.
2. Install dependencies: `axios`, `pinia`, `vue-router`, `dexie`, `lucide-vue-next`, `@vueuse/core`.
3. Install Tailwind CSS.
4. Configure `vite-plugin-pwa` for offline capabilities (Service Worker, Manifest).

### Phase 2: Authentication & Offline DB
1. Setup `Dexie` database schema (`attendance`, `fundraising`).
2. Create Pinia Stores (`authStore`, `syncStore`).
3. Implement Axios interceptors for Bearer token.

### Phase 3: UI Implementation (Mobile First)
1. **Layout**: Bottom Navigation Bar (Home, Absen, Donasi, Profil).
2. **Home**: Dashboard with summary.
3. **Absen**: Big "Clock In" button, Map view (optional), Status.
4. **Donasi**: Form for input.
5. **Profil**: Employee details card.

### Phase 4: Integration
1. Connect to Laravel API.
2. Test Offline/Online toggling.
3. Build & Deploy.

