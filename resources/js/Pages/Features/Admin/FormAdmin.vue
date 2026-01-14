<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head } from '@inertiajs/vue3';
import {
    UsersRound,
    UserCheck,
    UserMinus,
    Users,
    Eye,
    EyeOff,
    CheckCircle,
    XCircle,
    Loader,
} from 'lucide-vue-next';

// ============================================================================
// STATE
// ============================================================================
const isLoading = ref(true);
const error = ref('');
const users = ref([]);

// ============================================================================
// COMPUTED - STATISTICS
// ============================================================================
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
            user.a5_status &&
            user.a6_status &&
            user.a7_status &&
            user.a8_status
        );
    }).length;
});

// ============================================================================
// METHODS - FETCH USER SUBMISSION STATUS
// ============================================================================
const fetchUsersStatus = async () => {
    try {
        isLoading.value = true;
        error.value = '';
        
        const response = await axios.get(route('form-admin.users-status'));
        users.value = response.data.users || [];
    } catch (err) {
        console.error('Error fetching users status:', err);
        error.value = 'Gagal memuat data pengguna. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

// ============================================================================
// METHODS - CHECK USER SUBMISSION
// ============================================================================
const hasAnySubmission = (user) => {
    return user.a5_status || user.a6_status || user.a7_status || user.a8_status;
};

// ============================================================================
// METHODS - NAVIGATE TO USER FILES
// ============================================================================
const viewUserFiles = (user) => {
    if (hasAnySubmission(user)) {
        router.visit(route('admin.user-files', { userId: user.id }));
    }
};

// ============================================================================
// LIFECYCLE
// ============================================================================
onMounted(() => {
    fetchUsersStatus();
});
</script>

<template>
    <MainLayout>
        <Head title="Status Pengisian Form" />
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Status Pengisian Form
            </h2>
        </template>

        <Header
            title="Monitor Pengisian Form"
            description="Pantau status pengisian form A5-A8 dari semua pengguna"
            color="green"
        />

        <main class="container mx-auto px-4 pt-4 pb-8">
            <div class="py-12">
                <div class="mx-auto sm:px-3 lg:px-5">
                    <!-- ============================================================== -->
                    <!-- STATISTICS CARDS -->
                    <!-- ============================================================== -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Card 1: Total Users -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-500 uppercase tracking-wide"
                                        >
                                            Total Pengguna
                                        </p>
                                        <p
                                            class="mt-2 text-3xl font-bold text-gray-900"
                                        >
                                            <span
                                                v-if="isLoading"
                                                class="inline-block w-12 h-8 bg-gray-200 rounded animate-pulse"
                                            ></span>
                                            <span v-else>{{ totalUsers }}</span>
                                        </p>
                                    </div>
                                    <div class="p-3 bg-blue-100 rounded-full">
                                        <UsersRound
                                            class="w-8 h-8 text-blue-600"
                                        />
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-gray-600">
                                    Jumlah seluruh pengguna terdaftar
                                </p>
                            </div>
                            <div
                                class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"
                            ></div>
                        </div>

                        <!-- Card 2: Partial Submit Users -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-500 uppercase tracking-wide"
                                        >
                                            Submit Sebagian
                                        </p>
                                        <p
                                            class="mt-2 text-3xl font-bold text-gray-900"
                                        >
                                            <span
                                                v-if="isLoading"
                                                class="inline-block w-12 h-8 bg-gray-200 rounded animate-pulse"
                                            ></span>
                                            <span v-else>{{
                                                partialSubmitUsers
                                            }}</span>
                                        </p>
                                    </div>
                                    <div class="p-3 bg-amber-100 rounded-full">
                                        <UserMinus
                                            class="w-8 h-8 text-amber-600"
                                        />
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-gray-600">
                                    Pengguna yang belum lengkap submit
                                </p>
                            </div>
                            <div
                                class="h-1 bg-gradient-to-r from-amber-500 to-orange-500"
                            ></div>
                        </div>

                        <!-- Card 3: Complete Submit Users -->
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-500 uppercase tracking-wide"
                                        >
                                            Submit Lengkap
                                        </p>
                                        <p
                                            class="mt-2 text-3xl font-bold text-gray-900"
                                        >
                                            <span
                                                v-if="isLoading"
                                                class="inline-block w-12 h-8 bg-gray-200 rounded animate-pulse"
                                            ></span>
                                            <span v-else>{{
                                                completeSubmitUsers
                                            }}</span>
                                        </p>
                                    </div>
                                    <div class="p-3 bg-green-100 rounded-full">
                                        <UserCheck
                                            class="w-8 h-8 text-green-600"
                                        />
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-gray-600">
                                    Pengguna yang sudah submit A5-A8
                                </p>
                            </div>
                            <div
                                class="h-1 bg-gradient-to-r from-green-500 to-emerald-500"
                            ></div>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- USER SUBMISSION STATUS TABLE -->
                    <!-- ============================================================== -->
                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-center gap-3 mb-6">
                                <Users class="w-6 h-6 text-green-600" />
                                <h3 class="text-xl font-bold text-gray-900">
                                    Status Pengisian Form
                                </h3>
                            </div>

                            <!-- Loading State -->
                            <div
                                v-if="isLoading"
                                class="flex items-center justify-center py-12"
                            >
                                <Loader
                                    class="w-8 h-8 animate-spin text-green-600"
                                />
                                <span class="ml-3 text-gray-600"
                                    >Memuat data...</span
                                >
                            </div>

                            <!-- Error State -->
                            <div
                                v-else-if="error"
                                class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700"
                            >
                                {{ error }}
                            </div>

                            <!-- Table -->
                            <div v-else class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                No
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Nama
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Email
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                A5
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                A6
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                A7
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                A8
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Lihat File
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-gray-200"
                                    >
                                        <tr
                                            v-for="(user, index) in users"
                                            :key="user.id"
                                            class="hover:bg-gray-50 transition-colors"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            >
                                                {{ index + 1 }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ user.name }}
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap"
                                            >
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{ user.email }}
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center"
                                            >
                                                <span
                                                    v-if="user.a5_status"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                >
                                                    <CheckCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Submitted
                                                </span>
                                                <span
                                                    v-else
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                >
                                                    <XCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Not Submitted
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center"
                                            >
                                                <span
                                                    v-if="user.a6_status"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                >
                                                    <CheckCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Submitted
                                                </span>
                                                <span
                                                    v-else
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                >
                                                    <XCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Not Submitted
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center"
                                            >
                                                <span
                                                    v-if="user.a7_status"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                >
                                                    <CheckCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Submitted
                                                </span>
                                                <span
                                                    v-else
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                >
                                                    <XCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Not Submitted
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center"
                                            >
                                                <span
                                                    v-if="user.a8_status"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                >
                                                    <CheckCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Submitted
                                                </span>
                                                <span
                                                    v-else
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                >
                                                    <XCircle
                                                        class="w-3.5 h-3.5"
                                                    />
                                                    Not Submitted
                                                </span>
                                            </td>
                                            <!-- Kolom Lihat File -->
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center"
                                            >
                                                <button
                                                    v-if="
                                                        hasAnySubmission(user)
                                                    "
                                                    @click="viewUserFiles(user)"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                                                    title="Lihat File"
                                                >
                                                    <Eye class="w-5 h-5" />
                                                </button>
                                                <span
                                                    v-else
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-400 cursor-not-allowed"
                                                    title="Belum ada file yang disubmit"
                                                >
                                                    <EyeOff class="w-5 h-5" />
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- Empty State -->
                                        <tr v-if="users.length === 0">
                                            <td
                                                colspan="8"
                                                class="px-6 py-12 text-center text-gray-500"
                                            >
                                                Tidak ada data pengguna
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Legend -->
                            <div
                                class="mt-6 flex flex-wrap items-center gap-6 text-sm text-gray-600"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        <CheckCircle class="w-3.5 h-3.5" />
                                        Submitted
                                    </span>
                                    <span>= Sudah mengisi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                    >
                                        <XCircle class="w-3.5 h-3.5" />
                                        Not Submitted
                                    </span>
                                    <span>= Belum mengisi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-600"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </span>
                                    <span>= Lihat file submission</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </MainLayout>
</template>

<style scoped>
/* Add any additional component-specific styles here */
</style>