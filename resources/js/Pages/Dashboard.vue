<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { CheckCircle, XCircle, Loader, Users, Eye, EyeOff, UserCheck, UserMinus, UsersRound } from 'lucide-vue-next';

// State
const users = ref([]);
const isLoading = ref(true);
const error = ref('');

// ============================================================================
// COMPUTED - Statistics
// ============================================================================

// Total jumlah user
const totalUsers = computed(() => users.value.length);

// Jumlah user yang sudah submit sebagian (minimal 1 tapi belum semua)
const partialSubmitUsers = computed(() => {
    return users.value.filter(user => {
        const submittedCount = [user.a5_status, user.a6_status, user.a7_status, user.a8_status].filter(Boolean).length;
        return submittedCount > 0 && submittedCount < 4;
    }).length;
});

// Jumlah user yang sudah submit semua (A5, A6, A7, A8)
const completeSubmitUsers = computed(() => {
    return users.value.filter(user => {
        return user.a5_status && user.a6_status && user.a7_status && user.a8_status;
    }).length;
});

// Check if user has any submission
const hasAnySubmission = (user) => {
    return user.a5_status || user.a6_status || user.a7_status || user.a8_status;
};

// Navigate to administration page
const viewUserFiles = (user) => {
    if (hasAnySubmission(user)) {
        router.visit(route('admin.user-files', { userId: user.id }));
    }
};

// Fetch users submission status
const fetchUsersStatus = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(route('users.submission-status'));
        users.value = response.data.users;
    } catch (err) {
        console.error('Error fetching users status:', err);
        error.value = 'Gagal memuat data pengguna';
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchUsersStatus();
});
</script>

<template>
    <MainLayout>
        <Head title="Dashboard" />
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <Header
            title="Dashboard Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-3 lg:px-5">
                
                <!-- ============================================================== -->
                <!-- STATISTICS CARDS -->
                <!-- ============================================================== -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Card 1: Total Users -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Pengguna</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">
                                        <span v-if="isLoading" class="inline-block w-12 h-8 bg-gray-200 rounded animate-pulse"></span>
                                        <span v-else>{{ totalUsers }}</span>
                                    </p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <UsersRound class="w-8 h-8 text-blue-600" />
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-600">
                                Jumlah seluruh pengguna terdaftar
                            </p>
                        </div>
                        <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    </div>

                    <!-- Card 2: Partial Submit Users -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Submit Sebagian</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">
                                        <span v-if="isLoading" class="inline-block w-12 h-8 bg-gray-200 rounded animate-pulse"></span>
                                        <span v-else>{{ partialSubmitUsers }}</span>
                                    </p>
                                </div>
                                <div class="p-3 bg-amber-100 rounded-full">
                                    <UserMinus class="w-8 h-8 text-amber-600" />
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-600">
                                Pengguna yang belum lengkap submit
                            </p>
                        </div>
                        <div class="h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                    </div>

                    <!-- Card 3: Complete Submit Users -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Submit Lengkap</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">
                                        <span v-if="isLoading" class="inline-block w-12 h-8 bg-gray-200 rounded animate-pulse"></span>
                                        <span v-else>{{ completeSubmitUsers }}</span>
                                    </p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <UserCheck class="w-8 h-8 text-green-600" />
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-600">
                                Pengguna yang sudah submit A5-A8
                            </p>
                        </div>
                        <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- USER SUBMISSION STATUS TABLE -->
                <!-- ============================================================== -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-center gap-3 mb-6">
                            <Users class="w-6 h-6 text-green-600" />
                            <h3 class="text-xl font-bold text-gray-900">Status Pengisian Form</h3>
                        </div>

                        <!-- Loading State -->
                        <div v-if="isLoading" class="flex items-center justify-center py-12">
                            <Loader class="w-8 h-8 animate-spin text-green-600" />
                            <span class="ml-3 text-gray-600">Memuat data...</span>
                        </div>

                        <!-- Error State -->
                        <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                            {{ error }}
                        </div>

                        <!-- Table -->
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            A5
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            A6
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            A7
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            A8
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lihat File
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(user, index) in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span v-if="user.a5_status" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <CheckCircle class="w-3.5 h-3.5" />
                                                Submitted
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <XCircle class="w-3.5 h-3.5" />
                                                Not Submitted
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span v-if="user.a6_status" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <CheckCircle class="w-3.5 h-3.5" />
                                                Submitted
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <XCircle class="w-3.5 h-3.5" />
                                                Not Submitted
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span v-if="user.a7_status" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <CheckCircle class="w-3.5 h-3.5" />
                                                Submitted
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <XCircle class="w-3.5 h-3.5" />
                                                Not Submitted
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span v-if="user.a8_status" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <CheckCircle class="w-3.5 h-3.5" />
                                                Submitted
                                            </span>
                                            <span v-else class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <XCircle class="w-3.5 h-3.5" />
                                                Not Submitted
                                            </span>
                                        </td>
                                        <!-- Kolom Lihat File -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button
                                                v-if="hasAnySubmission(user)"
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
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                            Tidak ada data pengguna
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Legend -->
                        <div class="mt-6 flex flex-wrap items-center gap-6 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <CheckCircle class="w-3.5 h-3.5" />
                                    Submitted
                                </span>
                                <span>= Sudah mengisi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <XCircle class="w-3.5 h-3.5" />
                                    Not Submitted
                                </span>
                                <span>= Belum mengisi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-600">
                                    <Eye class="w-4 h-4" />
                                </span>
                                <span>= Lihat file submission</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
