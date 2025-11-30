# Implementation Plan: Separate Frontend for LAZ Permohonan

This plan outlines the architecture and steps to create a dedicated frontend application for the public-facing "Permohonan LAZ" (LAZ Applications) system, separating it from the main `payroll-app` monolith. The `payroll-app` will serve as the backend API.

## 1. Architecture Overview

*   **Backend (`payroll-app`)**:
    *   Acts as the Headless CMS and API Provider.
    *   Manages database, business logic, admin dashboard (already integrated), and API endpoints.
    *   **Location**: `d:\LAZ\Payroll\payroll-app`
*   **Frontend (`laz-public-frontend`)**:
    *   A modern, responsive web application for public users.
    *   Features: Landing page, Program listing, Application form, Status checking.
    *   **Location**: `d:\LAZ\Payroll\laz-public-frontend` (New Folder)
    *   **Tech Stack**: Next.js (React) or Vite (Vue/React) + Tailwind CSS.

## 2. Backend API Requirements (`payroll-app`)

We need to expose specific endpoints in `routes/api.php` to allow the frontend to fetch data and submit forms.

### API Endpoints (Prefix: `/api/v1/laz`)

| Method | Endpoint | Description | Public/Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/programs` | List active programs and periods | Public |
| `GET` | `/programs/{id}` | Get program details | Public |
| `POST` | `/applications` | Submit a new application (with file uploads) | Public |
| `POST` | `/check-status` | Check application status by code/NIK | Public |

### Backend Tasks
1.  **Create API Controllers**:
    *   `App\Http\Controllers\Api\Laz\PublicProgramController`
    *   `App\Http\Controllers\Api\Laz\PublicApplicationController`
2.  **Define Routes**: Update `routes/api.php`.
3.  **CORS Configuration**: Update `config/cors.php` to allow requests from the frontend URL (e.g., `http://localhost:3000`).
4.  **API Resources**: Create Eloquent API Resources to format JSON responses consistently.

## 3. Frontend Application Structure (`laz-public-frontend`)

We will initialize a new Next.js project. This framework is chosen for its robust routing, server-side rendering capabilities (good for SEO of public programs), and modern developer experience.

### Key Pages
1.  **Home (`/`)**: Hero section, introduction to LAZ, featured programs.
2.  **Programs (`/programs`)**: Grid view of available aid programs.
3.  **Apply (`/apply/{programId}`)**: Multi-step form for submitting applications.
    *   Steps: Personal Info -> Program Selection -> Documents -> Review.
4.  **Status (`/status`)**: Simple form to input Ticket Code/NIK and view progress.
5.  **Success (`/success`)**: Confirmation page after submission.

### Design System
*   **Tailwind CSS**: For rapid, beautiful styling.
*   **Components**: Reusable UI elements (Buttons, Inputs, Cards, Modals).
*   **Theme**: Professional, trustworthy, yet modern (Clean whites, Emerald greens, soft shadows).

## 4. Implementation Steps

### Phase 1: Backend API Preparation
1.  [ ] Create `PublicProgramController` and `PublicApplicationController` in `payroll-app`.
2.  [ ] Define API routes in `routes/api.php`.
3.  [ ] Create API Resources (`ProgramResource`, `ApplicationResource`).
4.  [ ] Test endpoints using Postman or curl.

### Phase 2: Frontend Initialization
1.  [ ] Create folder `laz-public-frontend`.
2.  [ ] Initialize Next.js app: `npx create-next-app@latest .`
3.  [ ] Setup Tailwind CSS and project structure.

### Phase 3: Frontend Development
1.  [ ] Build **API Client** service (using `axios` or `fetch`) to communicate with `payroll-app`.
2.  [ ] Develop **Home** and **Program List** pages.
3.  [ ] Develop **Application Form** with validation (using `react-hook-form` + `zod`).
4.  [ ] Develop **Status Check** page.

### Phase 4: Integration & Polish
1.  [ ] Connect Frontend forms to Backend API.
2.  [ ] Handle file uploads (multipart/form-data).
3.  [ ] Implement error handling and loading states.
4.  [ ] Final UI/UX polish (animations, responsive check).

## 5. Directory Structure Preview

```
d:\LAZ\Payroll\
├── payroll-app/           (Backend: Laravel)
│   ├── app/Http/Controllers/Api/Laz/
│   ├── routes/api.php
│   └── ...
└── laz-public-frontend/   (Frontend: Next.js)
    ├── src/
    │   ├── app/           (Routes)
    │   ├── components/    (UI Parts)
    │   ├── lib/           (API Client)
    │   └── ...
    └── package.json
```
