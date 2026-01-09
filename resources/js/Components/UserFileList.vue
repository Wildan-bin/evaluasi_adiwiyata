<template>
    <div>
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-sm text-gray-600">Memuat data...</p>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="files.length === 0"
            class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
        >
            <svg
                class="mx-auto h-12 w-12 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                />
            </svg>
            <p class="mt-2 text-sm text-gray-500">Belum ada file yang diupload</p>
        </div>

        <!-- Files List -->
        <div v-else class="space-y-3">
            <div
                v-for="file in files"
                :key="file.id"
                class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between">
                    <!-- Left: File Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h4 class="text-sm font-semibold text-gray-900">
                                {{ file.indikator }}
                            </h4>
                            <span
                                v-if="file.section"
                                class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded"
                            >
                                {{ file.section }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mt-1">{{ file.original_filename }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="text-xs text-gray-500">{{ file.file_size }}</span>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500">{{ file.uploaded_at }}</span>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center space-x-2 mt-3">
                            <component :is="getStatusIcon(file.status)" />
                            <span
                                :class="[
                                    'text-sm font-medium',
                                    file.status === 'approved'
                                        ? 'text-green-700'
                                        : file.status === 'pending'
                                        ? 'text-yellow-700'
                                        : 'text-red-700'
                                ]"
                            >
                                {{ file.status_label }}
                            </span>
                        </div>

                        <!-- Mentor Comment -->
                        <div
                            v-if="file.mentor_comment"
                            class="mt-3 p-3 bg-blue-50 border-l-4 border-blue-400 rounded"
                        >
                            <p class="text-xs font-medium text-blue-900 mb-1">Komentar Mentor:</p>
                            <p class="text-sm text-blue-800">{{ file.mentor_comment }}</p>
                        </div>

                        <!-- Revision Indicator -->
                        <div v-if="file.revision_number > 0" class="mt-2">
                            <span class="text-xs text-gray-500">
                                Revisi ke-{{ file.revision_number }}
                            </span>
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex flex-col space-y-2 ml-4">
                        <button
                            @click="handlePreview(file.id)"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                            title="Preview"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
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
                            class="p-2 text-green-600 hover:bg-green-50 rounded-md transition-colors"
                            title="Download"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                />
                            </svg>
                        </button>
                        <button
                            v-if="file.status !== 'approved'"
                            @click="handleDelete(file.id)"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-md transition-colors"
                            title="Hapus"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

/**
 * UserFileList Component
 *
 * Komponen untuk menampilkan list file yang sudah diupload user
 * Menampilkan status review, komentar mentor, dan opsi untuk upload ulang jika ditolak
 */

const props = defineProps({
    category: {
        type: String,
        default: null
    },
    onFileDeleted: {
        type: Function,
        default: null
    }
});

const files = ref([]);
const loading = ref(true);

onMounted(() => {
    loadFiles();
});

const loadFiles = async () => {
    loading.value = true;
    try {
        const params = props.category ? { category: props.category } : {};
        const response = await axios.get('/api/file-upload/user', { params });
        files.value = response.data.data;
    } catch (error) {
        console.error('Error loading files:', error);
    } finally {
        loading.value = false;
    }
};

const handleDelete = async (fileId) => {
    if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) {
        return;
    }

    try {
        const response = await axios.delete(`/api/file-upload/${fileId}`);
        if (response.data.success) {
            alert('File berhasil dihapus');
            loadFiles();
            if (props.onFileDeleted) {
                props.onFileDeleted(fileId);
            }
        }
    } catch (error) {
        alert('Gagal menghapus file: ' + (error.response?.data?.message || error.message));
    }
};

const handlePreview = (fileId) => {
    window.open(`/api/file-upload/${fileId}/preview`, '_blank');
};

const handleDownload = (fileId) => {
    window.location.href = `/api/file-upload/${fileId}/download`;
};

const PendingIcon = {
    template: `
        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
        </svg>
    `
};

const ApprovedIcon = {
    template: `
        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
    `
};

const RejectedIcon = {
    template: `
        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
    `
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'pending':
            return PendingIcon;
        case 'approved':
            return ApprovedIcon;
        case 'rejected':
        case 'revision_requested':
            return RejectedIcon;
        default:
            return null;
    }
};
</script>
