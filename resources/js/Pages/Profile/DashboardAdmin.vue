<script setup>
// filepath: /home/wildanrobin/Projects/evaluasi_adiwiyata/resources/js/Pages/Profile/DashboardAdmin.vue

import MainLayout from "@/Layouts/MainLayout.vue";
import Header from "@/Components/Header.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import {
    Trash2,
    X,
    UserPlus,
    Eye,
    EyeOff,
    CheckCircle,
    XCircle,
    Loader,
} from "lucide-vue-next";

// Props dari backend
const props = defineProps({
    admins: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
    mentors: { type: Array, default: () => [] },
    completeSchools: { type: Array, default: () => [] },
    administrasiSekolah: { type: Array, default: () => [] },    
});

// Reactive data
const admins = ref(props.admins);
const users = ref(props.users);
const mentors = ref(props.mentors);

// ✅ FIX: Pisahkan selectedMentor dari savedMentor
const completeSchools = ref(
    props.completeSchools.map(school => ({
        ...school,
        selectedMentor: school.mentor,
        savedMentor: school.mentor,
    }))
);

const administrasiSekolah = ref(props.administrasiSekolah);
const evaluatedSchools = ref([]);

// State
const isLoading = ref(false);
const error = ref("");

// Computed
const availableMentors = computed(() => {
    return mentors.value.map((m) => m.name);
});

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

const last5Users = computed(() => {
    return users.value.slice(-5);
});

// Preview Modal state
const showPreviewModal = ref(false);
const previewData = ref(null);
const previewLoading = ref(false);
const previewError = ref(null);
const previewDocType = ref("");
const previewDocTitle = ref("");
const previewSchoolName = ref("");
const previewUserId = ref(null);

const docTypeConfig = {
    rencana: { title: "Rencana & Evaluasi PBLHS", color: "green" },
    self_assessment: { title: "Self Assessment", color: "blue" },
    kebutuhan_pendampingan: {
        title: "Kebutuhan Pendampingan",
        color: "purple",
    },
    pernyataan: { title: "Pernyataan & Persetujuan", color: "red" },
};

onMounted(() => {
    console.log('='.repeat(60));
    console.log('[DashboardAdmin] Component mounted');
    console.log('[DashboardAdmin] Complete Schools:', completeSchools.value.length);
    console.log('[DashboardAdmin] Complete schools data:', completeSchools.value);
    console.log('='.repeat(60));
});

// ============================================================================
// NAVIGATION FUNCTIONS
// ============================================================================
const navigateToAddUser = (role) => {
    router.visit(route('register.role', { role: role }));
};

// ============================================================================
// ✅ MENTOR ASSIGNMENT FUNCTIONS - TANPA API (Gunakan Inertia)
// ============================================================================
const assignMentor = (school) => {
    if (!school.selectedMentor) {
        alert('Silakan pilih mentor terlebih dahulu');
        return;
    }

    if (school.selectedMentor === school.savedMentor) {
        alert('Mentor yang dipilih sama dengan yang sudah tersimpan');
        return;
    }

    if (!confirm(`Apakah Anda yakin ingin menugaskan ${school.selectedMentor} untuk ${school.nama_sekolah}?`)) {
        return;
    }

    console.log('[DashboardAdmin] Assigning mentor:', {
        user_id_sekolah: school.user_id,
        mentor_name: school.selectedMentor,
    });

    router.post(route('admin.assign-mentor'), {
        user_id_sekolah: school.user_id,
        mentor_name: school.selectedMentor,
    }, {
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('[DashboardAdmin] ✅ Mentor assigned successfully');
            alert(`Mentor ${school.selectedMentor} berhasil ditugaskan untuk ${school.nama_sekolah}`);
            school.savedMentor = school.selectedMentor;
        },
        onError: (errors) => {
            console.error('[DashboardAdmin] ❌ Error assigning mentor:', errors);
            alert(errors.message || 'Gagal menyimpan mentor');
        },
    });
};

const removeMentor = (school) => {
    if (!confirm(`Apakah Anda yakin ingin melepaskan mentor ${school.savedMentor} dari ${school.nama_sekolah}?`)) {
        return;
    }

    console.log('[DashboardAdmin] Removing mentor:', {
        user_id_sekolah: school.user_id,
    });

    router.post(route('admin.finish-mentor-assignment'), {
        user_id_sekolah: school.user_id,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('[DashboardAdmin] ✅ Mentor removed successfully');
            alert(`Mentor berhasil dilepaskan dari ${school.nama_sekolah}`);
            school.savedMentor = null;
            school.selectedMentor = null;
        },
        onError: (errors) => {
            console.error('[DashboardAdmin] ❌ Error removing mentor:', errors);
            alert(errors.message || 'Gagal melepaskan mentor');
        },
    });
};

// Preview Modal Functions
const openPreviewModal = async (userId, docType) => {
    previewUserId.value = userId;
    previewDocType.value = docType;
    previewDocTitle.value = docTypeConfig[docType]?.title || "Preview";
    showPreviewModal.value = true;
    previewLoading.value = true;
    previewError.value = null;
    previewData.value = null;
    previewSchoolName.value = "";

    try {
        const response = await fetch(
            `/administrasi-sekolah/${userId}/preview?docType=${docType}`,
            {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            }
        );

        if (!response.ok) {
            throw new Error("Gagal memuat data dokumen");
        }

        const data = await response.json();
        previewData.value = data.documents || [];
        previewSchoolName.value = data.schoolName || "";
    } catch (error) {
        previewError.value = error.message;
        console.error("Error fetching preview:", error);
    } finally {
        previewLoading.value = false;
    }
};

const closePreviewModal = () => {
    showPreviewModal.value = false;
    previewData.value = null;
    previewError.value = null;
};

const deleteUser = (type, id) => {
    if (confirm("Apakah Anda yakin ingin menghapus user ini?")) {
        router.delete(`/users/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ["admins", "users", "mentors"] });
            },
        });
    }
};

const hasAnySubmission = (user) => {
    return user.a5_status || user.a6_status || user.a7_status || user.a8_status;
};

const viewUserFiles = (user) => {
    if (hasAnySubmission(user)) {
        router.visit(route("admin.user-files", { userId: user.id }));
    }
};
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
            title="Dashboard Greenedu Admin"
            description="Kelola user, assign mentor, dan monitor program lingkungan sekolah"
            color="green"
        />

        <main class="container mx-auto px-4 pt-24 pb-8">
            <!-- User Management Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Admin Container -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Admin</h2>
                        <button
                            @click="navigateToAddUser('admin')"
                            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-md hover:bg-green-600 transition flex items-center gap-2"
                        >
                            <UserPlus :size="18" />
                            Tambah Admin
                        </button>
                    </div>
                    <div class="space-y-3" id="admin-list">
                        <div
                            v-for="admin in admins"
                            :key="admin.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-md"
                        >
                            <div>
                                <p class="font-medium">{{ admin.name }}</p>
                                <p class="text-sm text-gray-500">{{ admin.email }}</p>
                            </div>
                            <button
                                @click="deleteUser('admin', admin.id)"
                                class="text-red-600 hover:text-red-800 transition"
                            >
                                <Trash2 :size="20" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- User Container -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">User</h2>
                        <button
                            @click="navigateToAddUser('user')"
                            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-md hover:bg-green-600 transition flex items-center gap-2"
                        >
                            <UserPlus :size="18" />
                            Tambah User
                        </button>
                    </div>
                    <div class="space-y-3" id="user-list">
                        <div
                            v-for="user in users"
                            :key="user.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-md"
                        >
                            <div>
                                <p class="font-medium">{{ user.name }}</p>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                            </div>
                            <button
                                @click="deleteUser('user', user.id)"
                                class="text-red-600 hover:text-red-800 transition"
                            >
                                <Trash2 :size="20" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mentor Container -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Mentor</h2>
                        <button
                            @click="navigateToAddUser('mentor')"
                            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-md hover:bg-green-600 transition flex items-center gap-2"
                        >
                            <UserPlus :size="18" />
                            Tambah Mentor
                        </button>
                    </div>
                    <div class="space-y-3" id="mentor-list">
                        <div
                            v-for="mentor in mentors"
                            :key="mentor.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-md"
                        >
                            <div>
                                <p class="font-medium">{{ mentor.name }}</p>
                                <p class="text-sm text-gray-500">{{ mentor.email }}</p>
                            </div>
                            <button
                                @click="deleteUser('mentor', mentor.id)"
                                class="text-red-600 hover:text-red-800 transition"
                            >
                                <Trash2 :size="20" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ PENGAJUAN LIST SECTION -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">
                            Daftar Sekolah Pengajuan PPEPP
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Hanya menampilkan sekolah yang sudah menyelesaikan semua tahap form (A5, A6, A7, A8)
                        </p>
                    </div>
                    <div class="text-sm">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold">
                            {{ completeSchools.length }} Sekolah Siap
                        </span>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="completeSchools.length === 0" class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <XCircle class="w-16 h-16 mx-auto" />
                    </div>
                    <p class="text-gray-600 font-semibold">Belum ada sekolah yang menyelesaikan semua form</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Sekolah akan muncul di sini setelah menyelesaikan form A5, A6, A7, dan A8
                    </p>
                </div>

                <!-- ✅ TABLE WITH FIXED BEHAVIOUR -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-green-500 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left">No</th>
                                <th class="px-6 py-4 text-left">Nama Sekolah</th>
                                <th class="px-6 py-4 text-left">NPSN</th>
                                <th class="px-6 py-4 text-center">Mentor</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(school, index) in completeSchools"
                                :key="school.id"
                                class="border-b hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium">{{ school.nama_sekolah }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ school.npsn }}</td>
                                
                                <!-- ✅ KOLOM MENTOR -->
                                <td class="px-6 py-4 text-center">
                                    <select
                                        v-model="school.selectedMentor"
                                        class="border rounded px-3 py-2 w-40 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        :disabled="school.savedMentor !== null"
                                    >
                                        <option :value="null">Pilih Mentor</option>
                                        <option
                                            v-for="mentor in availableMentors"
                                            :key="mentor"
                                            :value="mentor"
                                        >
                                            {{ mentor }}
                                        </option>
                                    </select>
                                    <p v-if="school.savedMentor" class="text-xs text-gray-500 mt-1">
                                        Tersimpan: {{ school.savedMentor }}
                                    </p>
                                </td>
                                
                                <!-- ✅ KOLOM STATUS -->
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 text-sm rounded-full"
                                        :class="school.savedMentor ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                    >
                                        {{ school.savedMentor ? 'Sudah Ada Mentor' : 'Belum Dievaluasi' }}
                                    </span>
                                </td>
                                
                                <!-- ✅ KOLOM AKSI -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            v-if="!school.savedMentor"
                                            @click="assignMentor(school)"
                                            :disabled="!school.selectedMentor"
                                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                                        >
                                            Simpan Mentor
                                        </button>
                                        
                                        <button
                                            v-else
                                            @click="removeMentor(school)"
                                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition"
                                        >
                                            Lepas Mentor
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✅ DAFTAR SEKOLAH SUDAH EVALUASI -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">Daftar Sekolah Sudah Evaluasi</h2>
                
                <!-- Empty State -->
                <div v-if="evaluatedSchools.length === 0" class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <CheckCircle class="w-16 h-16 mx-auto" />
                    </div>
                    <p class="text-gray-600 font-semibold">Belum ada sekolah yang sudah dievaluasi</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Sekolah akan muncul di sini setelah proses evaluasi selesai
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left">No</th>
                                <th class="px-6 py-4 text-left">Nama Sekolah</th>
                                <th class="px-6 py-4 text-left">NPSN</th>
                                <th class="px-6 py-4 text-center">Mentor</th>
                                <th class="px-6 py-4 text-center">Status Evaluasi</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(school, index) in evaluatedSchools"
                                :key="school.id"
                                class="border-b hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium">{{ school.nama_sekolah }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ school.npsn }}</td>
                                <td class="px-6 py-4 text-center">{{ school.mentor }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                        Selesai
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                                        Lihat Hasil
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ✅ PEMANTAUAN ADMINISTRASI SEKOLAH -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">Pemantauan Administrasi Sekolah</h2>
                
                <div v-if="administrasiSekolah.length === 0" class="text-center py-12">
                    <p class="text-gray-600">Belum ada data administrasi sekolah</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-purple-500 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Nama Sekolah</th>
                                <th class="px-4 py-3 text-left">NPSN</th>
                                <th class="px-4 py-3 text-center">Rencana Evaluasi</th>
                                <th class="px-4 py-3 text-center">Self Assessment</th>
                                <th class="px-4 py-3 text-center">Kebutuhan Pendampingan</th>
                                <th class="px-4 py-3 text-center">Pernyataan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(adm, index) in administrasiSekolah"
                                :key="adm.id"
                                class="border-b hover:bg-gray-50 transition"
                            >
                                <td class="px-4 py-3">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ adm.nama_sekolah }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ adm.npsn }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="openPreviewModal(adm.user_id, 'rencana')"
                                        class="inline-flex items-center gap-1"
                                        :class="adm.rencana_evaluasi ? 'text-green-600 hover:text-green-800' : 'text-gray-400 cursor-not-allowed'"
                                        :disabled="!adm.rencana_evaluasi"
                                    >
                                        <Eye v-if="adm.rencana_evaluasi" :size="18" />
                                        <EyeOff v-else :size="18" />
                                        <span class="text-sm">{{ adm.rencana_evaluasi ? 'Lihat' : 'Belum' }}</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="openPreviewModal(adm.user_id, 'self_assessment')"
                                        class="inline-flex items-center gap-1"
                                        :class="adm.self_assessment ? 'text-green-600 hover:text-green-800' : 'text-gray-400 cursor-not-allowed'"
                                        :disabled="!adm.self_assessment"
                                    >
                                        <Eye v-if="adm.self_assessment" :size="18" />
                                        <EyeOff v-else :size="18" />
                                        <span class="text-sm">{{ adm.self_assessment ? 'Lihat' : 'Belum' }}</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="openPreviewModal(adm.user_id, 'kebutuhan_pendampingan')"
                                        class="inline-flex items-center gap-1"
                                        :class="adm.kebutuhan_pendampingan ? 'text-green-600 hover:text-green-800' : 'text-gray-400 cursor-not-allowed'"
                                        :disabled="!adm.kebutuhan_pendampingan"
                                    >
                                        <Eye v-if="adm.kebutuhan_pendampingan" :size="18" />
                                        <EyeOff v-else :size="18" />
                                        <span class="text-sm">{{ adm.kebutuhan_pendampingan ? 'Lihat' : 'Belum' }}</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="openPreviewModal(adm.user_id, 'pernyataan')"
                                        class="inline-flex items-center gap-1"
                                        :class="adm.pernyataan ? 'text-green-600 hover:text-green-800' : 'text-gray-400 cursor-not-allowed'"
                                        :disabled="!adm.pernyataan"
                                    >
                                        <Eye v-if="adm.pernyataan" :size="18" />
                                        <EyeOff v-else :size="18" />
                                        <span class="text-sm">{{ adm.pernyataan ? 'Lihat' : 'Belum' }}</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Pengisian Form Section -->
            <div class="py-12">
                <div class="mx-auto sm:px-3 lg:px-2">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-2">
                            <div class="flex items-center gap-3 mb-6">
                                <h3 class="text-2xl font-bold text-gray-900">
                                    Status Pengisian Form
                                </h3>
                            </div>

                            <div v-if="isLoading" class="flex items-center justify-center py-12">
                                <Loader class="w-8 h-8 animate-spin text-green-600" />
                                <span class="ml-3 text-gray-600">Memuat data...</span>
                            </div>

                            <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                                {{ error }}
                            </div>

                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">A5</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">A6</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">A7</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">A8</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lihat File</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(user, index) in last5Users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
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
                                        <tr v-if="last5Users.length === 0">
                                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                                Tidak ada data pengguna
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

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

                            <div class="mt-6 flex justify-center">
                                <Link
                                    :href="route('form')"
                                    class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 text-white font-semibold rounded-3xl hover:bg-green-700 transition-colors"
                                >
                                    Lihat Selengkapnya
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- ✅ PREVIEW MODAL -->
        <Transition name="modal">
            <div
                v-if="showPreviewModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                @click.self="closePreviewModal"
            >
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-4 border-b bg-gray-50">
                        <div>
                            <h3 class="text-lg font-bold">{{ previewDocTitle }}</h3>
                            <p v-if="previewSchoolName" class="text-sm text-gray-600">
                                {{ previewSchoolName }}
                            </p>
                        </div>
                        <button
                            @click="closePreviewModal"
                            class="text-gray-500 hover:text-gray-700 transition"
                        >
                            <X :size="24" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto max-h-[70vh]">
                        <!-- Loading State -->
                        <div v-if="previewLoading" class="flex items-center justify-center py-12">
                            <Loader class="animate-spin text-green-500" :size="40" />
                            <span class="ml-3 text-gray-600">Memuat data...</span>
                        </div>

                        <!-- Error State -->
                        <div v-else-if="previewError" class="text-center py-12">
                            <XCircle class="mx-auto text-red-500 mb-4" :size="48" />
                            <p class="text-red-600">{{ previewError }}</p>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="!previewData || previewData.length === 0" class="text-center py-12">
                            <EyeOff class="mx-auto text-gray-400 mb-4" :size="48" />
                            <p class="text-gray-600">Tidak ada data untuk ditampilkan</p>
                        </div>

                        <!-- Content -->
                        <div v-else class="space-y-4">
                            <div
                                v-for="(doc, index) in previewData"
                                :key="index"
                                class="border rounded-lg p-4"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold">{{ doc.title || `Dokumen ${index + 1}` }}</h4>
                                        <p class="text-sm text-gray-600">{{ doc.description }}</p>
                                    </div>
                                    <a
                                        v-if="doc.file_path"
                                        :href="doc.file_path"
                                        target="_blank"
                                        class="text-blue-500 hover:text-blue-700"
                                    >
                                        <Eye :size="20" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end p-4 border-t bg-gray-50">
                        <button
                            @click="closePreviewModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </MainLayout>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
    transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
    transform: scale(0.95);
}
</style>