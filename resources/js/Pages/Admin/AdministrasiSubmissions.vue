<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    submissions: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// State
const searchQuery = ref(props.filters.q || '');
const selectedStatus = ref(props.filters.status || 'all');
const showDetailModal = ref(false);
const showNoteModal = ref(false);
const currentSubmission = ref(null);

// Forms
const noteForm = useForm({
    admin_note: '',
});

const verifyForm = useForm({
    verified: false,
});

// Methods
function search() {
    router.get(route('administrasi-submissions'), {
        q: searchQuery.value,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : null,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function filterByStatus(status) {
    selectedStatus.value = status;
    search();
}

function openDetailModal(submission) {
    currentSubmission.value = submission;
    showDetailModal.value = true;
}

function closeDetailModal() {
    showDetailModal.value = false;
    currentSubmission.value = null;
}

function openNoteModal(submission) {
    currentSubmission.value = submission;
    noteForm.admin_note = submission.admin_note || '';
    showNoteModal.value = true;
}

function closeNoteModal() {
    showNoteModal.value = false;
    currentSubmission.value = null;
    noteForm.reset();
}

function saveNote() {
    noteForm.patch(route('administrasi-update-note', currentSubmission.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeNoteModal();
        },
    });
}

function toggleVerify(submission) {
    if (confirm(`Apakah Anda yakin ingin ${submission.verified ? 'membatalkan verifikasi' : 'memverifikasi'} data ini?`)) {
        verifyForm.verified = !submission.verified;
        verifyForm.patch(route('administrasi-verify', submission.id), {
            preserveScroll: true,
            onSuccess: () => {
                verifyForm.reset();
            },
        });
    }
}

function unlockForEdit(submission) {
    if (confirm('Membuka akses edit akan mengubah status menjadi unverified. Lanjutkan?')) {
        router.patch(route('administrasi-unlock', submission.id), {}, {
            preserveScroll: true,
        });
    }
}

// Computed
const statusCounts = computed(() => {
    // You would ideally get this from backend
    return {
        all: props.submissions.total,
        unverified: 0,
        verified: 0,
        edit_requested: 0,
    };
});
</script>

<template>
    <MainLayout>
        <Head title="Admin - Submissions Administrasi Sekolah" />

        <Header
            title="Submissions Administrasi Sekolah"
            description="Kelola dan verifikasi data administrasi sekolah"
            color="purple"
        />

        <main class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12">
            <div class="container mx-auto px-4 max-w-7xl">
                <!-- Filters & Search -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <!-- Status Filter Tabs -->
                    <div class="flex flex-wrap gap-2 mb-6 pb-6 border-b">
                        <button
                            @click="filterByStatus('all')"
                            class="px-4 py-2 rounded-lg transition font-medium"
                            :class="selectedStatus === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Semua
                        </button>
                        <button
                            @click="filterByStatus('unverified')"
                            class="px-4 py-2 rounded-lg transition font-medium"
                            :class="selectedStatus === 'unverified' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Unverified
                        </button>
                        <button
                            @click="filterByStatus('verified')"
                            class="px-4 py-2 rounded-lg transition font-medium"
                            :class="selectedStatus === 'verified' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Verified
                        </button>
                        <button
                            @click="filterByStatus('edit_requested')"
                            class="px-4 py-2 rounded-lg transition font-medium"
                            :class="selectedStatus === 'edit_requested' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Edit Request
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="flex gap-3">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="search"
                            type="text"
                            placeholder="Cari nama sekolah atau user..."
                            class="flex-1 rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring-2 focus:ring-purple-500"
                        />
                        <button
                            @click="search"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium"
                        >
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="mb-6">
                    <p class="text-gray-600">
                        Menampilkan <span class="font-semibold">{{ submissions.data.length }}</span> dari
                        <span class="font-semibold">{{ submissions.total }}</span> total submissions
                    </p>
                </div>

                <!-- Cards Grid -->
                <div v-if="submissions.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div
                        v-for="submission in submissions.data"
                        :key="submission.id"
                        class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
                    >
                        <!-- Card Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-900 line-clamp-2">
                                    {{ submission.nama_sekolah }}
                                </h3>

                                <!-- Status Badge -->
                                <span
                                    v-if="submission.verified"
                                    class="flex-shrink-0 ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                                >
                                    ✓
                                </span>
                                <span
                                    v-else
                                    class="flex-shrink-0 ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"
                                >
                                    ✗
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">User:</span> {{ submission.user?.name || 'Unknown' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">NPSN:</span> {{ submission.npsn }}
                            </p>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 bg-gray-50">
                            <div class="space-y-2 text-sm">
                                <div v-if="submission.submitted_at" class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ new Date(submission.submitted_at).toLocaleDateString('id-ID') }}</span>
                                </div>

                                <div v-if="submission.edit_requested" class="flex items-center text-yellow-600 font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                    </svg>
                                    <span>Edit Request</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer - Actions -->
                        <div class="p-4 bg-gray-100 flex items-center justify-between gap-2">
                            <!-- Preview -->
                            <button
                                @click="openDetailModal(submission)"
                                class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                                title="Preview"
                            >
                                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Note -->
                            <button
                                @click="openNoteModal(submission)"
                                class="flex-1 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium"
                                title="Catatan"
                            >
                                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </button>

                            <!-- Verify/Unverify -->
                            <button
                                @click="toggleVerify(submission)"
                                class="flex-1 px-3 py-2 rounded-lg transition text-sm font-medium"
                                :class="submission.verified ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700'"
                                :title="submission.verified ? 'Unverify' : 'Verify'"
                            >
                                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="!submission.verified" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    <path v-else fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Unlock for Edit -->
                            <button
                                v-if="submission.verified || submission.edit_requested"
                                @click="unlockForEdit(submission)"
                                class="flex-1 px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-sm font-medium"
                                title="Unlock Edit"
                            >
                                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 text-lg">Tidak ada data ditemukan</p>
                </div>

                <!-- Pagination -->
                <div v-if="submissions.data.length > 0" class="mt-8">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Halaman {{ submissions.current_page }} dari {{ submissions.last_page }}
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-if="submissions.prev_page_url"
                                :href="submissions.prev_page_url"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                            >
                                ← Sebelumnya
                            </Link>
                            <Link
                                v-if="submissions.next_page_url"
                                :href="submissions.next_page_url"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                            >
                                Selanjutnya →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Detail Modal -->
        <div
            v-if="showDetailModal && currentSubmission"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeDetailModal"
        >
            <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Detail Submission</h3>
                    <button
                        @click="closeDetailModal"
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Identitas Sekolah Table -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Identitas Sekolah</h4>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50 w-1/3">NPSN</td>
                                    <td class="py-2 px-3">{{ currentSubmission.npsn }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50">Nama Sekolah</td>
                                    <td class="py-2 px-3">{{ currentSubmission.nama_sekolah }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50">Alamat</td>
                                    <td class="py-2 px-3">{{ currentSubmission.alamat }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50">Kota</td>
                                    <td class="py-2 px-3">{{ currentSubmission.kota }}, {{ currentSubmission.provinsi }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ketua -->
                    <div v-if="currentSubmission.ketua" class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Ketua Tim</h4>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50 w-1/3">Nama</td>
                                    <td class="py-2 px-3">{{ currentSubmission.ketua.nama }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50">NIP</td>
                                    <td class="py-2 px-3">{{ currentSubmission.ketua.nip || '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-medium bg-gray-50">Email</td>
                                    <td class="py-2 px-3">{{ currentSubmission.ketua.email || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Anggota -->
                    <div v-if="currentSubmission.ketua?.anggota?.length > 0" class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Daftar Anggota ({{ currentSubmission.ketua.anggota.length }})</h4>
                        <ul class="space-y-2">
                            <li
                                v-for="(anggota, index) in currentSubmission.ketua.anggota"
                                :key="index"
                                class="p-3 bg-gray-50 rounded-lg text-sm"
                            >
                                <strong>{{ index + 1 }}. {{ anggota.nama }}</strong>
                                <span v-if="anggota.nip" class="text-gray-600 ml-2">({{ anggota.nip }})</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Full View Link -->
                    <Link
                        :href="route('administrasi-preview', currentSubmission.id)"
                        class="block w-full text-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium"
                    >
                        Lihat Detail Lengkap
                    </Link>
                </div>
            </div>
        </div>

        <!-- Note Modal -->
        <div
            v-if="showNoteModal && currentSubmission"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeNoteModal"
        >
            <div class="bg-white rounded-xl max-w-lg w-full">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Catatan Admin</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ currentSubmission.nama_sekolah }}</p>
                </div>

                <form @submit.prevent="saveNote" class="p-6">
                    <textarea
                        v-model="noteForm.admin_note"
                        rows="6"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring-2 focus:ring-purple-500"
                        placeholder="Tulis catatan untuk user..."
                    ></textarea>

                    <div class="flex gap-3 justify-end mt-4">
                        <button
                            type="button"
                            @click="closeNoteModal"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="noteForm.processing"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 transition"
                        >
                            {{ noteForm.processing ? 'Menyimpan...' : 'Simpan Catatan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
