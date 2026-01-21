<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, router, Link } from '@inertiajs/vue3';
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

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    
                <div
                    class="min-h-screen bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50"
                >
                    <!-- Header dengan Login & Register Button -->
                    <div class="absolute top-0 right-0 p-6 z-50">
                        <div class="flex gap-4">
                            <Link
                                v-if="canLogin"
                                href="/login"
                                class="px-4 py-2 text-gray-600 hover:text-gray-900 font-medium"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                href="/register"
                                class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-2 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 font-medium transition"
                            >
                                Register
                            </Link>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <main class="container mx-auto px-4 pt-24 pb-8">
                        <!-- Header Section -->
                        <div class="mb-12">
                            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                                Dashboard Greenedu
                            </h1>
                            <p class="text-gray-600 text-lg">
                                Kelola dan monitor program lingkungan sekolah
                                Anda
                            </p>
                        </div>

                        <!-- Welcome Card -->
                        <div
                            class="bg-white rounded-lg shadow-lg p-8 text-center mb-12"
                        >
                            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                                Selamat Datang di Evaluasi Greenedu
                            </h2>
                            <p class="text-gray-600 mb-8 text-lg">
                                Platform evaluasi program lingkungan
                                berkelanjutan untuk sekolah
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                                <Link
                                    v-if="canLogin"
                                    href="/login"
                                    class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 font-medium transition"
                                >
                                    Masuk Sekarang
                                </Link>
                                <Link
                                    href="/request-access"
                                    class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 font-semibold transition"
                                >
                                    Request Akun Sekolah
                                </Link>
                            </div>
                        </div>

                        <!-- Features Section -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
                            >
                                <div class="text-4xl mb-3">📋</div>
                                <h3
                                    class="text-xl font-bold text-gray-800 mb-2"
                                >
                                    Evaluasi Komprehensif
                                </h3>
                                <p class="text-gray-600">
                                    Evaluasi menyeluruh program lingkungan
                                    sekolah Anda
                                </p>
                            </div>

                            <div
                                class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
                            >
                                <div class="text-4xl mb-3">📊</div>
                                <h3
                                    class="text-xl font-bold text-gray-800 mb-2"
                                >
                                    Analisis Data
                                </h3>
                                <p class="text-gray-600">
                                    Lihat hasil evaluasi dan rekomendasi
                                    perbaikan
                                </p>
                            </div>

                            <div
                                class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
                            >
                                <div class="text-4xl mb-3">🌱</div>
                                <h3
                                    class="text-xl font-bold text-gray-800 mb-2"
                                >
                                    Program Berkelanjutan
                                </h3>
                                <p class="text-gray-600">
                                    Kelola program lingkungan berkelanjutan di
                                    sekolah
                                </p>
                            </div>
                        </div>
                    </main>
                </div>

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
    0%,
    100% {
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
