<template>
    <div class="space-y-4">
        <!-- Filter -->
        <div class="flex space-x-2 border-b border-gray-200 pb-3">
            <button
                @click="filter = 'all'"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-md',
                    filter === 'all'
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
            >
                Semua
            </button>
            <button
                @click="filter = 'pending'"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-md',
                    filter === 'pending'
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
            >
                Pending
            </button>
            <button
                @click="filter = 'approved'"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-md',
                    filter === 'approved'
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
            >
                Disetujui
            </button>
            <button
                @click="filter = 'rejected'"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-md',
                    filter === 'rejected'
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
            >
                Ditolak
            </button>
        </div>

        <!-- File List -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-sm text-gray-600">Memuat data...</p>
        </div>
        <div v-else-if="files.length === 0" class="text-center py-8">
            <p class="text-gray-500">Tidak ada file</p>
        </div>
        <div v-else class="space-y-3">
            <div
                v-for="file in files"
                :key="file.id"
                class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between">
                    <!-- File Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h4 class="text-sm font-semibold text-gray-900">
                                {{ file.indikator }}
                            </h4>
                            <component :is="getStatusBadge(file.status)" />
                        </div>
                        <p class="text-sm text-gray-700 mt-1">{{ file.original_filename }}</p>
                        <div class="flex items-center space-x-3 mt-2 text-xs text-gray-500">
                            <span v-if="file.user_name" class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                {{ file.user_name }}
                            </span>
                            <span>{{ file.file_size }}</span>
                            <span>{{ file.uploaded_at }}</span>
                        </div>
                        <div v-if="file.mentor_comment" class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-700">
                            <span class="font-medium">Komentar:</span> {{ file.mentor_comment }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 ml-4">
                        <button
                            @click="handlePreview(file.id)"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-md"
                            title="Preview"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />
                            </svg>
                        </button>
                        <button
                            @click="handleDownload(file.id)"
                            class="p-2 text-green-600 hover:bg-green-50 rounded-md"
                            title="Download"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                />
                            </svg>
                        </button>
                        <button
                            v-if="file.status === 'pending'"
                            @click="openReviewModal(file)"
                            class="px-3 py-1 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md"
                        >
                            Review
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <div
            v-if="selectedFile"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Review File: {{ selectedFile.original_filename }}
                    </h3>

                    <!-- Status Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status Review
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    name="review-status"
                                    value="approved"
                                    v-model="reviewStatus"
                                    class="mr-2"
                                />
                                <span class="text-sm">Disetujui</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    name="review-status"
                                    value="revision_requested"
                                    v-model="reviewStatus"
                                    class="mr-2"
                                />
                                <span class="text-sm">Perlu Revisi</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    name="review-status"
                                    value="rejected"
                                    v-model="reviewStatus"
                                    class="mr-2"
                                />
                                <span class="text-sm">Ditolak</span>
                            </label>
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Komentar/Catatan (Opsional)
                        </label>
                        <textarea
                            v-model="comment"
                            rows="4"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Berikan catatan atau saran untuk user..."
                        />
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="closeReviewModal"
                            :disabled="isSubmitting"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleReview"
                            :disabled="!reviewStatus || isSubmitting"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Review' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    userId: {
        type: [String, Number],
        default: null
    }
});

const files = ref([]);
const loading = ref(true);
const selectedFile = ref(null);
const reviewStatus = ref('');
const comment = ref('');
const isSubmitting = ref(false);
const filter = ref('all');

onMounted(() => {
    loadFiles();
});

watch(() => [props.userId, filter.value], () => {
    loadFiles();
});

const loadFiles = async () => {
    loading.value = true;
    try {
        let url = props.userId
            ? `/api/file-upload/user/${props.userId}`
            : '/api/file-upload/pending';

        const response = await axios.get(url);

        let fileData = response.data.data;

        if (fileData.files) {
            fileData = fileData.files;
        }

        if (filter.value !== 'all') {
            fileData = fileData.filter(file => file.status === filter.value);
        }

        files.value = fileData;
    } catch (error) {
        console.error('Error loading files:', error);
    } finally {
        loading.value = false;
    }
};

const handleReview = async () => {
    if (!selectedFile.value || !reviewStatus.value) {
        alert('Pilih status review');
        return;
    }

    isSubmitting.value = true;
    try {
        const response = await axios.post(`/api/file-upload/${selectedFile.value.id}/review`, {
            status: reviewStatus.value,
            comment: comment.value,
        });

        if (response.data.success) {
            alert('Review berhasil disimpan');
            closeReviewModal();
            loadFiles();
        }
    } catch (error) {
        alert('Gagal menyimpan review: ' + (error.response?.data?.message || error.message));
    } finally {
        isSubmitting.value = false;
    }
};

const getStatusBadge = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        revision_requested: 'bg-orange-100 text-orange-800',
    };

    const labels = {
        pending: 'Menunggu Review',
        approved: 'Disetujui',
        rejected: 'Ditolak',
        revision_requested: 'Perlu Revisi',
    };

    const Badge = {
        template: `<span class="px-2 py-1 text-xs font-semibold rounded-full ${colors[status]}">${labels[status]}</span>`
    };

    return Badge;
};

const handlePreview = (fileId) => {
    window.open(`/api/file-upload/${fileId}/preview`, '_blank');
};

const handleDownload = (fileId) => {
    window.location.href = `/api/file-upload/${fileId}/download`;
};

const openReviewModal = (file) => {
    selectedFile.value = file;
    comment.value = file.mentor_comment || '';
};

const closeReviewModal = () => {
    selectedFile.value = null;
    reviewStatus.value = '';
    comment.value = '';
};
</script>
