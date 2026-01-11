# Add Form Admin Dashboard with Submission Status

## Overview
Create a dedicated admin page (FormAdmin.vue) that displays form submission statistics and a detailed status table for all users. This requires:
- Creating a new route & controller to fetch user submission data
- Populating FormAdmin.vue with the statistics cards and status table from DashboardAdmin
- Adding role-based middleware to restrict admin access
- Updating the form wizard to check user role and redirect admins to the new page instead of the regular Form.vue

---

## Task Breakdown

### 1. Create FormAdminController
**File:** `app/Http/Controllers/FormAdminController.php`

**Responsibilities:**
- Fetch all users with role 'user'
- Compute A5-A8 submission status for each user (via existence checks on Rencana, BuktiSelfAssessment, Pendampingan, Pernyataan models)
- Return data as Inertia response to FormAdmin.vue
- Apply admin role middleware

**Data Structure to Return:**
```php
{
    "users": [
        {
            "id": integer,
            "name": string,
            "email": string,
            "role": string,
            "a5_status": boolean,  // Rencana::where('user_id', $user->id)->exists()
            "a6_status": boolean,  // BuktiSelfAssessment::where('user_id', $user->id)->exists()
            "a7_status": boolean,  // Pendampingan::where('user_id', $user->id)->exists() || Permintaan::where('user_id', $user->id)->exists()
            "a8_status": boolean   // Pernyataan::where('user_id', $user->id)->exists()
        }
    ]
}
```

---

### 2. Create Admin Form Route
**File:** `routes/web.php`

**Route Configuration:**
- Path: `/admin/form-submissions` or `/admin/form`
- Method: `GET`
- Controller: `FormAdminController@index`
- Middleware: `role:admin` (restrict to admin only)
- Renders: `Features/Admin/FormAdmin.vue`

---

### 3. Populate FormAdmin.vue Template
**File:** `resources/js/Pages/Features/Admin/FormAdmin.vue`

**Structure:**
```
MainLayout
  └─ Header (title: "Form Submission Management")
  └─ Main Container
      ├─ Statistics Cards (grid 3 columns)
      │  ├─ Card 1: Total Users
      │  ├─ Card 2: Partial Submit Users
      │  └─ Card 3: Complete Submit Users
      │
      └─ User Submission Status Table
         ├─ Header with title & icon
         ├─ Loading/Error states
         └─ Table with columns:
            - No
            - Name
            - Email
            - A5 (Rencana)
            - A6 (Self Assessment)
            - A7 (Pendampingan)
            - A8 (Pernyataan)
            - View Files (button)
         ├─ Legend (Submitted/Not Submitted icons explanation)
```

---

### 4. Script Setup for FormAdmin.vue

**Required Imports:**
```javascript
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import {
    UsersRound,
    UserCheck,
    UserMinus,
    Users,
    Eye,
    EyeOff,
    CheckCircle,
    XCircle,
    Loader
} from 'lucide-vue-next';
import axios from 'axios';
```

**State Management:**
```javascript
const page = usePage();
const users = ref([]);
const isLoading = ref(true);
const error = ref('');
```

**Computed Properties:**
```javascript
const totalUsers = computed(() => users.value.length);

const partialSubmitUsers = computed(() => {
    return users.value.filter((user) => {
        const submittedCount = [
            user.a5_status,
            user.a6_status,
            user.a7_status,
            user.a8_status,
        ].filter(Boolean).length;
        return submittedCount > 0 && submittedCount < 4;
    }).length;
});

const completeSubmitUsers = computed(() => {
    return users.value.filter((user) => {
        return (
            user.a5_status && user.a6_status && user.a7_status && user.a8_status
        );
    }).length;
});
```

**Methods:**
```javascript
const fetchUsersStatus = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(route('admin.form-submissions.status'));
        users.value = response.data.users;
    } catch (err) {
        console.error('Error fetching users status:', err);
        error.value = 'Gagal memuat data pengguna';
    } finally {
        isLoading.value = false;
    }
};

const hasAnySubmission = (user) => {
    return user.a5_status || user.a6_status || user.a7_status || user.a8_status;
};

const viewUserFiles = (user) => {
    if (hasAnySubmission(user)) {
        router.visit(route('admin.user-files', { userId: user.id }));
    }
};

onMounted(() => {
    fetchUsersStatus();
});
```

---

### 5. Update Routes (routes/web.php)
**Add Route:**
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/form-submissions', [FormAdminController::class, 'index'])
        ->name('admin.form-submissions.index');
    
    Route::get('/admin/form-submissions/status', [FormAdminController::class, 'getStatus'])
        ->name('admin.form-submissions.status');
});
```

---

### 6. Update Form.vue (Optional - Redirect Admin Users)
**File:** `resources/js/Pages/Features/Form.vue`

**Redirect at Controller/Middleware Level (Recommended):**
- Modify the form route in `routes/web.php` to check user role
- If admin, redirect to `admin.form-submissions.index`
- This prevents unnecessary frontend rendering

---

## Implementation Checklist

- [ ] Create `FormAdminController.php` with index() and getStatus() methods
- [ ] Add `FormAdminController` imports in `routes/web.php`
- [ ] Create admin form submission routes with role middleware
- [ ] Populate `FormAdmin.vue` with statistics cards
- [ ] Add user submission status table to `FormAdmin.vue`
- [ ] Import all required Lucide icons
- [ ] Add script setup with state, computed properties, and methods
- [ ] Add loading/error state handling
- [ ] Add Legend section for status badges
- [ ] Test form access for admin users
- [ ] Test form access for regular users (should work normally)
- [ ] Verify statistics cards compute correctly
- [ ] Test "View Files" button navigation
- [ ] Optional: Add redirect logic in Form.vue or form route handler

---

## Key Data Points

**User Submission Status Computation:**
- **A5**: `Rencana::where('user_id', $user->id)->exists()`
- **A6**: `BuktiSelfAssessment::where('user_id', $user->id)->exists()`
- **A7**: `Pendampingan::where('user_id', $user->id)->exists() || Permintaan::where('user_id', $user->id)->exists()`
- **A8**: `Pernyataan::where('user_id', $user->id)->exists()`

**Computed Counts:**
- **Total Users**: Count of all users with role 'user'
- **Partial Submit**: Users with 1-3 of 4 submissions completed
- **Complete Submit**: Users with all 4 submissions (A5, A6, A7, A8) completed

**Existing Related Routes:**
- `/admin/users/{userId}/files` - View user's file submissions (use this in viewUserFiles())
- Navigation via: `router.visit(route('admin.user-files', { userId: user.id }))`

---

## Design Pattern Reference

**Mirror DashboardAdmin.vue Structure:**
- Same statistics card styling and layout
- Same table structure with status badges
- Same icon indicators (CheckCircle for submitted, XCircle for not submitted)
- Same color scheme (green for complete, amber/orange for partial, blue for icons)
- Same loading skeleton animations
- Same Legend section at bottom

---

## Alternative Approaches Considered

1. **Reuse DashboardAdmin.vue** - Pro: Less code duplication. Con: Mixes unrelated admin concerns (user management + form submissions)
2. **Add tab to DashboardAdmin** - Pro: Single page. Con: Component becomes too large and complex
3. **Standalone FormAdmin (Chosen)** - Pro: Clean separation of concerns, dedicated admin form management interface. Con: Some code duplication from DashboardAdmin (acceptable for maintainability)
