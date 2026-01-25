# Administration Wizard Plan

Transform the Administration page into a 3-step wizard (like Form.vue) with consistent PDF upload behavior, mapped to existing tables and models.

## Goals
- Guided, step-by-step data entry flow for Administration.
- Reuse upload mechanics from A5/A6/A8 for PDF files.
- Persist data to: administrasi_sekolah, sk_tim (+ ketua & anggota), administrasi_dasar.
- Provide a status endpoint that drives `completedSteps` props.

## Scope
- Frontend: Replace Administration.vue UI with WizardLayout, add 3 step components.
- Backend: Routes, controllers, requests, status computation, file storage integration.
- Migrations: Fix down() table names for sk_tim and administrasi_dasar.

## Steps (Wizard)
1) Sekolah (administrasi_sekolah)
   - Form fields per schema: nama_sekolah, npsn, alamat, wilayah, kontak, koordinat, kepala sekolah, tim_greenedu JSON, status, catatan_admin.
   - Create/Update record bound to current user.

2) Tim (sk_tim PDF + ketua & anggota)
   - Upload SK Tim (PDF): save `path_file`, `file_upload_id`, `user_id` with soft deletes.
   - Ketua: sekolah_id -> administrasi_sekolah, fields: nama, nip?, email?, nomor_telepon?, timestamps.
   - Anggota: ketua_id, sekolah_id, fields: nama, nip?, email?, nomor_telepon?, timestamps.

3) Administrasi Dasar (PDF)
   - Upload PDF with fields: `indikator`, `path_file`, `file_upload_id`, `user_id` with soft deletes.

## Frontend Implementation
- Replace Administration.vue with WizardLayout + step controls mirroring Form.vue:
  - `steps` array: [{ id: 'adm1', label: 'Data Sekolah' }, { id: 'adm2', label: 'Tim & SK' }, { id: 'adm3', label: 'Administrasi Dasar' }]
  - `completedSteps` reactive state.
  - `fetchStepStatus()` -> GET `administrasi.get-status` to hydrate `completedSteps`.
  - Navigation: `goNext`, `goPrevious`, `handleSaveAndContinue`, `handleFinalSubmit` like Form.vue.
- Create step components:
  - resources/js/Pages/Features/Administration/SekolahStep.vue
  - resources/js/Pages/Features/Administration/TimStep.vue
  - resources/js/Pages/Features/Administration/AdministrasiDasarStep.vue
- Reuse upload patterns from A5/A6/A8:
  - Extract shared upload component/composable: Components/FileUploadInput.vue or composables/useFileUpload.js.
  - Enforce PDF mime, size cap, progress UI, error states.

## Backend Implementation
- Routes (routes/web.php):
  - GET  administrasi.index -> Inertia page with `completedSteps`.
  - GET  administrasi.get-status -> JSON `{ completedSteps: { adm1, adm2, adm3 } }`.
  - POST administrasi.sekolah.store -> create/update AdministrasiSekolah.
  - POST administrasi.sk-tim.store -> upload SK Tim (PDF) to sk_tim.
  - POST administrasi.ketua.store -> create Ketua.
  - POST administrasi.anggota.store -> create Anggota.
  - POST administrasi.administrasi-dasar.store -> upload Admin Dasar (PDF).
- Controllers (app/Http/Controllers/AdministrationController.php):
  - `index()` -> render Inertia page with shared props `completedSteps`.
  - `getStatus()` -> compute completion across tables for current user.
  - `storeSekolah(StoreSekolahRequest $req)`.
  - `storeSkTim(StoreSkTimRequest $req)` -> handle PDF upload + FileUpload link.
  - `storeKetua(StoreKetuaRequest $req)`.
  - `storeAnggota(StoreAnggotaRequest $req)`.
  - `storeAdministrasiDasar(StoreAdministrasiDasarRequest $req)` -> PDF upload.
- Requests (app/Http/Requests):
  - `StoreSekolahRequest`: validate core fields (npsn unique per user, strings, emails, coords decimals, status enum).
  - `StoreSkTimRequest`: `file` required PDF, max size, `path_file` derived; optional link to FileUpload.
  - `StoreKetuaRequest`: `sekolah_id` exists, contact fields optional.
  - `StoreAnggotaRequest`: `ketua_id`, `sekolah_id` exist, basic fields.
  - `StoreAdministrasiDasarRequest`: `indikator` required, `file` PDF required.

## Models
- Confirm and/or add:
  - AdministrasiSekolah: `user()` relation; fields per migration.
  - SkTim: `user()`, `fileUpload()`; uses SoftDeletes.
  - Ketua: `sekolah()`, `anggota()`.
  - Anggota: `ketua()`, `sekolah()`.
  - AdministrasiDasar: `user()`, `fileUpload()`; uses SoftDeletes.
  - FileUpload: existing implementation used across submissions.

## Storage & Uploads
- Use same disk/config as Form.vue flows (A5/A6/A8):
  - Store PDF to `/storage/app/...` via `Storage::putFile` with sanitized filenames.
  - Create/associate `file_uploads` row; persist `path_file` and `file_upload_id`.
  - Return file meta to frontend for confirmation.

## Completion Logic (`getStatus`)
- adm1: completed if a valid `administrasi_sekolah` exists for user.
- adm2: completed if a `sk_tim` file exists AND at least one `ketua` (with optional members). Optionally require >=1 anggota.
- adm3: completed if at least one `administrasi_dasar` record with valid PDF for user.

## Migrations Fix
- Update down():
  - 2026_01_24_151150_create_sk_tim.php -> `Schema::dropIfExists('sk_tim')`.
  - 2026_01_24_151151_create_administrasi_dasar.php -> `Schema::dropIfExists('administrasi_dasar')`.

## UX Notes
- Prefill existing data in forms; allow edit/overwrite.
- Guard admin users to admin dashboard (mirror Form.vue behavior).
- Show clear success/error toasts and disable buttons during save.

## Risks & Assumptions
- Assumes FileUpload model/service mirrors usage in A5/A6/A8.
- Assumes unique constraints (e.g., npsn) won’t conflict across users.
- Clarify overwrite vs versioning for repeated uploads.

## Next Actions
- Confirm naming (step IDs, route names) and validation specifics.
- Implement step components and backend endpoints.
- Wire `completedSteps` and verify navigation mirrors Form.vue.
