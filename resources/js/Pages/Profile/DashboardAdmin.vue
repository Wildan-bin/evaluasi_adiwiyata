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
    UserCheck,
    UserMinus,
    UsersRound,
    Users,
    CheckCircle,
    XCircle,
    Loader,
    RefreshCw,
} from "lucide-vue-next";

// State
const isLoading = ref(true);
const error = ref("");
const schoolsLoading = ref(false);
const assignLoading = ref(false);

// Props dari backend
const props = defineProps({
    admins: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    mentors: {
        type: Array,
        default: () => [],
    },
    administrasiSekolah: {
        type: Array,
        default: () => [],
    },
});

// Reactive data untuk users (dari props)
const admins = ref(props.admins);
const users = ref(props.users);
const mentors = ref(props.mentors);

// Total jumlah user
const totalUsers = computed(() => users.value.length);

// Jumlah user yang sudah submit sebagian (minimal 1 tapi belum semua)
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

// Jumlah user yang sudah submit semua (A5, A6, A7, A8)
const completeSubmitUsers = computed(() => {
    return users.value.filter((user) => {
        return (
            user.a5_status && user.a6_status && user.a7_status && user.a8_status
        );
    }).length;
});

// 5 user terakhir untuk ditampilkan di dashboard
const last5Users = computed(() => {
    return users.value.slice(-5);
});

// Data sekolah - now fetched from API
const schools = ref([]);
const evaluatedSchools = ref([]);
const availableMentorsData = ref([]);

// Data administrasi sekolah dari backend props
const administrasiSekolah = ref(props.administrasiSekolah);

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

// ============================================================================
// NAVIGATION FUNCTIONS - Navigasi ke halaman Register dengan role
// ============================================================================
const navigateToAddUser = (role) => {
    router.visit(route('register.role', { role: role }));
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

// Functions untuk assign mentor
const assignMentor = async (schoolId, mentorId) => {
    if (!mentorId) {
        alert("Pilih mentor terlebih dahulu!");
        return;
    }

    assignLoading.value = true;
    try {
        const response = await fetch('/api/mentor-assignment/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                school_id: schoolId,
                mentor_id: mentorId,
            }),
        });

        const data = await response.json();

        if (data.success) {
            alert(`Mentor berhasil di-assign ke ${data.data.school_name}!`);
            // Refresh schools data
            await fetchSchoolsReadyForAssignment();
        } else {
            alert(data.message || 'Gagal assign mentor');
        }
    } catch (err) {
        console.error('Error assigning mentor:', err);
        alert('Terjadi kesalahan saat assign mentor');
    } finally {
        assignLoading.value = false;
    }
};

// Re-assign mentor (change mentor)
const reassignMentor = async (schoolId, newMentorId) => {
    if (!newMentorId) {
        alert("Pilih mentor baru terlebih dahulu!");
        return;
    }

    if (!confirm("Apakah Anda yakin ingin mengganti mentor untuk sekolah ini?")) {
        return;
    }

    assignLoading.value = true;
    try {
        const response = await fetch('/api/mentor-assignment/reassign', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                school_id: schoolId,
                new_mentor_id: newMentorId,
            }),
        });

        const data = await response.json();

        if (data.success) {
            alert(`Mentor berhasil diganti dari ${data.data.old_mentor_name} ke ${data.data.new_mentor_name}!`);
            // Refresh schools data
            await fetchSchoolsReadyForAssignment();
        } else {
            alert(data.message || 'Gagal re-assign mentor');
        }
    } catch (err) {
        console.error('Error re-assigning mentor:', err);
        alert('Terjadi kesalahan saat re-assign mentor');
    } finally {
        assignLoading.value = false;
    }
};

// Available mentors list - now from API data
const availableMentors = computed(() => {
    return availableMentorsData.value;
});

// Check if user has any submission
const hasAnySubmission = (user) => {
    return user.a5_status || user.a6_status || user.a7_status || user.a8_status;
};

// Navigate to administration page
const viewUserFiles = (user) => {
    if (hasAnySubmission(user)) {
        router.visit(route("admin.user-files", { userId: user.id }));
    }
};

// Fetch schools ready for mentor assignment
const fetchSchoolsReadyForAssignment = async () => {
    schoolsLoading.value = true;
    try {
        const response = await fetch('/api/mentor-assignment/schools', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            // Separate schools: those without mentor (for assignment) and those with mentor
            const schoolsWithoutMentor = data.schools
                .filter(s => !s.has_mentor)
                .map(s => ({
                    ...s,
                    selectedMentorId: null, // for v-model
                }));
            
            const schoolsWithMentor = data.schools
                .filter(s => s.has_mentor)
                .map(s => ({
                    ...s,
                    selectedNewMentorId: null, // for re-assign v-model
                }));
            
            schools.value = schoolsWithoutMentor;
            // Schools with mentor that haven't been fully evaluated yet
            // (can be shown in a different section or merged)
        }
    } catch (err) {
        console.error('Error fetching schools:', err);
    } finally {
        schoolsLoading.value = false;
    }
};

// Fetch available mentors
const fetchMentors = async () => {
    try {
        const response = await fetch('/api/mentor-assignment/mentors', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            availableMentorsData.value = data.mentors;
        }
    } catch (err) {
        console.error('Error fetching mentors:', err);
    }
};

// Fetch evaluated schools
const fetchEvaluatedSchools = async () => {
    try {
        const response = await fetch('/api/mentor-assignment/evaluated', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (data.success) {
            evaluatedSchools.value = data.schools.map(s => ({
                ...s,
                hasFile: false, // TODO: implement file check
            }));
        }
    } catch (err) {
        console.error('Error fetching evaluated schools:', err);
    }
};

// Fetch users submission status
const fetchUsersStatus = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(route("users.submission-status"));
        users.value = response.data.users;
    } catch (err) {
        console.error("Error fetching users status:", err);
        error.value = "Gagal memuat data pengguna";
    } finally {
        isLoading.value = false;
    }
};

onMounted(async () => {
    // Fetch all data in parallel
    await Promise.all([
        fetchUsersStatus(),
        fetchSchoolsReadyForAssignment(),
        fetchMentors(),
        fetchEvaluatedSchools(),
    ]);
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
                                <p class="text-sm text-gray-500">
                                    {{ admin.email }}
                                </p>
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
                                <p class="text-sm text-gray-500">
                                    {{ user.email }}
                                </p>
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
                                <p class="text-sm text-gray-500">
                                    {{ mentor.email }}
                                </p>
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

            <!-- Pengajuan List Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">
                        Daftar Sekolah Pengajuan PPEPP
                    </h2>
                    <button 
                        @click="fetchSchoolsReadyForAssignment" 
                        class="flex items-center gap-2 px-4 py-2 text-sm text-green-600 hover:bg-green-50 rounded-lg transition"
                        :disabled="schoolsLoading"
                    >
                        <RefreshCw :size="16" :class="{ 'animate-spin': schoolsLoading }" />
                        Refresh
                    </button>
                </div>

                <div v-if="schoolsLoading" class="flex items-center justify-center py-12">
                    <Loader class="w-8 h-8 animate-spin text-green-600" />
                    <span class="ml-3 text-gray-600">Memuat data sekolah...</span>
                </div>

                <div v-else-if="schools.length === 0" class="text-center py-12 text-gray-500">
                    <UsersRound class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                    <p>Tidak ada sekolah yang siap untuk di-assign mentor.</p>
                    <p class="text-sm mt-2">Sekolah harus sudah mengisi semua form (A5, A6, A7, A8).</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-gray-500 text-left text-xs font-medium uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    Nama Sekolah
                                </th>
                                <th class="px-6 py-4 text-center">Mentor</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="school in schools"
                                :key="school.id"
                                class="border-b hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium">{{ school.name }}</p>
                                        <p class="text-sm text-gray-500">{{ school.email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <select
                                        v-model="school.selectedMentorId"
                                        class="border rounded px-3 py-2 w-48 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        <option :value="null">
                                            Pilih Mentor
                                        </option>
                                        <option
                                            v-for="mentor in availableMentors"
                                            :key="mentor.id"
                                            :value="mentor.id"
                                        >
                                            {{ mentor.name }} ({{ mentor.active_assignments }} sekolah)
                                        </option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800"
                                    >
                                        Belum Di-assign
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        @click="assignMentor(school.id, school.selectedMentorId)"
                                        :disabled="!school.selectedMentorId || assignLoading"
                                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                                    >
                                        <span v-if="assignLoading">
                                            <Loader class="w-4 h-4 animate-spin inline" />
                                        </span>
                                        <span v-else>Simpan Mentor</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daftar Sekolah Sudah Evaluasi -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">
                    Daftar Sekolah Sudah Evaluasi PPEPP
                </h2>

                <div v-if="evaluatedSchools.length === 0" class="text-center py-12 text-gray-500">
                    <CheckCircle class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                    <p>Belum ada sekolah yang selesai dievaluasi.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-gray-500 text-left text-xs font-medium uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    Nama Sekolah
                                </th>
                                <th class="px-6 py-4 text-center">Mentor</th>
                                <th class="px-6 py-4 text-center">
                                    Tanggal Selesai
                                </th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="school in evaluatedSchools"
                                :key="school.id"
                                class="border-b hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4">{{ school.school_name }}</td>
                                <td class="px-6 py-4 text-center">
                                    {{ school.mentor_name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ school.completed_at ? new Date(school.completed_at).toLocaleDateString('id-ID') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800"
                                    >
                                        Sudah Dievaluasi
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pemantauan Administrasi Sekolah Section -->
            

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

        <!-- Preview Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showPreviewModal"
                    class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
                    @click.self="closePreviewModal"
                >
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between z-10">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ previewDocTitle }}</h2>
                                <p class="text-sm text-gray-600">{{ previewSchoolName }}</p>
                            </div>
                            <button @click="closePreviewModal" class="text-gray-400 hover:text-gray-600 transition">
                                <X :size="24" />
                            </button>
                        </div>

                        <div class="p-6">
                            <div v-if="previewLoading" class="flex flex-col items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                                <p class="mt-4 text-gray-600">Memuat dokumen...</p>
                            </div>

                            <div v-else-if="previewError" class="flex flex-col items-center justify-center py-12">
                                <div class="text-red-500 mb-4"><X :size="48" /></div>
                                <p class="text-gray-900 font-semibold">{{ previewError }}</p>
                                <button @click="closePreviewModal" class="mt-4 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                                    Tutup
                                </button>
                            </div>

                            <div v-else-if="Array.isArray(previewData) && previewData.length > 0" class="space-y-4">
                                <div v-for="(doc, index) in previewData" :key="index" class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-green-300 hover:bg-green-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ doc.title || doc.indikator || `Dokumen ${index + 1}` }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ doc.description || "File dokumen" }}</p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Diupload: {{ doc.created_at ? new Date(doc.created_at).toLocaleDateString("id-ID") : "N/A" }}
                                            </p>
                                        </div>
                                        <a
                                            v-if="doc.path_file"
                                            :href="`/administrasi-sekolah/${previewUserId}/file?path=${encodeURIComponent(doc.path_file)}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm ml-4 flex-shrink-0"
                                        >
                                            <Eye :size="16" />
                                            Buka
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="flex flex-col items-center justify-center py-12">
                                <div class="text-gray-400 mb-4"><X :size="48" /></div>
                                <p class="text-gray-900 font-semibold">Tidak ada dokumen</p>
                                <p class="text-gray-600 text-sm mt-1">Belum ada file yang diunggah untuk bagian ini.</p>
                            </div>
                        </div>

                        <div class="sticky bottom-0 bg-gray-50 border-t px-6 py-4 flex justify-end">
                            <button @click="closePreviewModal" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
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
