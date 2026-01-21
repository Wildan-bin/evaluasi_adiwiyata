<script setup>
import { ref, computed, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Upload, FileText, CheckCircle, AlertCircle, Loader, Eye, Download, Trash2, RefreshCw } from 'lucide-vue-next';
import axios from 'axios';
import { secureUpload } from '@/Composables/useCsrf';

// Props from backend
const props = defineProps({
    uploadedFiles: {
        type: Object,
        default: () => ({})
    }
});

// State
const files = ref({
    a1_sk: null,
    a2_tim: null,
    a3_rencana: null,
    a4_anggaran: null,
    a5_pblhs: null,
    a6_self_assessment: null,
    a7_pendampingan: null,
    a8_pernyataan: null
});

const uploadStatus = ref({
    a1_sk: null,
    a2_tim: null,
    a3_rencana: null,
    a4_anggaran: null,
    a5_pblhs: null,
    a6_self_assessment: null,
    a7_pendampingan: null,
    a8_pernyataan: null
});

const isDragging = ref({
    a1_sk: false,
    a2_tim: false,
    a3_rencana: false,
    a4_anggaran: false,
    a5_pblhs: false,
    a6_self_assessment: false,
    a7_pendampingan: false,
    a8_pernyataan: false
});

// Store uploaded files from props
const existingFiles = ref({});

const isUploading = ref(false);
const uploadingKey = ref(null);
const globalMessage = ref('');
const globalError = ref('');
const showReupload = ref({});

// Initialize existing files from props
onMounted(() => {
    if (props.uploadedFiles) {
        existingFiles.value = { ...props.uploadedFiles };
    }
});

// ============================================================================
// VALIDATION
// ============================================================================
const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

const validateFile = (file) => {
    if (!file) {
        return { valid: false, error: 'File diperlukan' };
    }

    // Type check - accept PDF only for now
    if (file.type !== 'application/pdf') {
        return {
            valid: false,
            error: `Hanya PDF yang diperbolehkan. Anda memilih: ${file.type || 'unknown'}`
        };
    }

    // Size check
    if (file.size > MAX_FILE_SIZE) {
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        return {
            valid: false,
            error: `File terlalu besar: ${sizeMB}MB (max: 10MB)`
        };
    }

    return { valid: true };
};

// ============================================================================
// DRAG & DROP HANDLERS
// ============================================================================
const handleDragOver = (key, event) => {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value[key] = true;
};

const handleDragLeave = (key, event) => {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value[key] = false;
};

const handleDrop = (key, event) => {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value[key] = false;

    const file = event.dataTransfer?.files?.[0];
    if (file) {
        processFile(key, file);
    }
};

// ============================================================================
// FILE PROCESSING
// ============================================================================
const processFile = (key, file) => {
    if (!file) return;

    // Clear previous status
    uploadStatus.value[key] = null;
    globalError.value = '';

    // Validate
    const { valid, error } = validateFile(file);
    if (!valid) {
        uploadStatus.value[key] = 'error';
        globalError.value = error;
        return;
    }

    files.value[key] = file;
};

// File handlers
const handleFileSelect = (key, event) => {
    const file = event.target.files?.[0];
    if (file) {
        processFile(key, file);
    }
};

const uploadFile = async (key) => {
    if (!files.value[key]) return;

    isUploading.value = true;
    uploadingKey.value = key;
    uploadStatus.value[key] = 'uploading';
    globalError.value = '';
    globalMessage.value = '';

    const formData = new FormData();
    formData.append('file', files.value[key]);
    formData.append('category', 'administrasi');
    formData.append('indikator', key);
    formData.append('section', 'administrasi_sekolah');

    try {
        // Use secureUpload for CSRF protection
        const response = await secureUpload('/api/file-upload', formData);

        if (response.data.success) {
            uploadStatus.value[key] = 'success';
            globalMessage.value = `✓ ${documents.find(d => d.key === key)?.label || key} berhasil diupload!`;
            
            // Update existing files with the new upload
            // API returns data in response.data.data
            const uploadedData = response.data.data;
            existingFiles.value[key] = {
                id: uploadedData.id,
                original_filename: uploadedData.original_filename,
                file_size: uploadedData.file_size,
                status: 'pending',
                created_at: new Date().toISOString()
            };
            files.value[key] = null;
            showReupload.value[key] = false;
        } else {
            throw new Error(response.data.message || 'Upload gagal');
        }
    } catch (error) {
        console.error('Upload error:', error);
        uploadStatus.value[key] = 'error';
        globalError.value = error.response?.data?.message || error.message || 'Gagal mengupload file';
    } finally {
        isUploading.value = false;
        uploadingKey.value = null;
    }
};

const uploadAllFiles = async () => {
    // Upload all files that haven't been uploaded yet
    const filesToUpload = Object.entries(files.value)
        .filter(([key, file]) => file !== null && uploadStatus.value[key] !== 'success');
    
    if (filesToUpload.length === 0) {
        globalError.value = 'Pilih minimal satu file untuk diupload';
        return;
    }

    for (const [key] of filesToUpload) {
        await uploadFile(key);
        // Small delay between uploads
        await new Promise(resolve => setTimeout(resolve, 300));
    }
};

const removeFile = (key) => {
    files.value[key] = null;
    uploadStatus.value[key] = null;
    globalError.value = '';
    showReupload.value[key] = false;
};

// Check if document is already uploaded
const hasExistingFile = (key) => {
    return existingFiles.value && existingFiles.value[key];
};

// Get existing file data
const getExistingFile = (key) => {
    return existingFiles.value[key] || null;
};

// Format file size - handle both bytes (number) and formatted string
const formatFileSize = (size) => {
    if (!size) return '0 B';
    // If already formatted string (e.g., "525.99 KB"), return as is
    if (typeof size === 'string') return size;
    // If number (bytes), format it
    const bytes = Number(size);
    if (isNaN(bytes)) return '0 B';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${sizes[i]}`;
};

// Get status badge class
const getStatusBadgeClass = (status) => {
    switch(status) {
        case 'approved': return 'bg-green-100 text-green-700';
        case 'rejected': return 'bg-red-100 text-red-700';
        default: return 'bg-yellow-100 text-yellow-700';
    }
};

// Get status text
const getStatusText = (status) => {
    switch(status) {
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        default: return 'Menunggu Review';
    }
};

// Preview file
const previewFile = (fileId) => {
    window.open(`/api/file-upload/${fileId}/preview`, '_blank');
};

// Download file
const downloadFile = (fileId) => {
    window.location.href = `/api/file-upload/${fileId}/download`;
};

// Toggle reupload mode
const toggleReupload = (key) => {
    showReupload.value[key] = !showReupload.value[key];
    if (!showReupload.value[key]) {
        files.value[key] = null;
        uploadStatus.value[key] = null;
    }
};

// Document sections
const documents = [
    {
        key: 'a1_sk',
        label: 'A1 - SK Tim Adiwiyata',
        description: 'Upload Surat Keputusan (SK) pembentukan Tim Adiwiyata sekolah',
        accept: 'application/pdf'
    },
    {
        key: 'a2_tim',
        label: 'A2 - Struktur Tim Adiwiyata',
        description: 'Upload dokumen struktur dan susunan Tim Adiwiyata',
        accept: 'application/pdf'
    },
    {
        key: 'a3_rencana',
        label: 'A3 - Rencana Kegiatan',
        description: 'Upload rencana kegiatan program Adiwiyata',
        accept: 'application/pdf'
    },
    {
        key: 'a4_anggaran',
        label: 'A4 - Anggaran Program',
        description: 'Upload dokumen anggaran untuk program Adiwiyata',
        accept: 'application/pdf'
    },
    {
        key: 'a5_pblhs',
        label: 'A5 - Rencana & Evaluasi PBLHS',
        description: 'Upload dokumen rencana dan evaluasi Pembelajaran Berbasis Lingkungan Hidup Sekolah',
        accept: 'application/pdf'
    },
    {
        key: 'a6_self_assessment',
        label: 'A6 - Bukti Self Assessment',
        description: 'Upload bukti dokumen hasil self assessment sekolah',
        accept: 'application/pdf'
    },
    {
        key: 'a7_pendampingan',
        label: 'A7 - Kebutuhan Pendampingan',
        description: 'Upload dokumen kebutuhan pendampingan program Adiwiyata',
        accept: 'application/pdf'
    },
    {
        key: 'a8_pernyataan',
        label: 'A8 - Pernyataan & Persetujuan',
        description: 'Upload dokumen pernyataan dan persetujuan keikutsertaan program Adiwiyata',
        accept: 'application/pdf'
    }
];

// ============================================================================
// COMPUTED HELPERS
// ============================================================================
const getUploadZoneClass = (key) => {
    const baseClass = 'border-2 border-dashed rounded-lg p-6 text-center transition-all duration-200';
    
    if (isDragging.value[key]) {
        return `${baseClass} border-emerald-500 bg-emerald-50 shadow-lg`;
    }
    
    if (uploadStatus.value[key] === 'error') {
        return `${baseClass} border-red-400 bg-red-50`;
    }
    
    return `${baseClass} border-gray-300 hover:border-emerald-400 cursor-pointer`;
};
</script>

<template>
    <MainLayout>
        <div class="p-6 max-w-5xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Administrasi Sekolah
                </h1>
                <p class="text-gray-600">
                    Unggah dokumen administrasi sekolah untuk program Adiwiyata
                </p>
            </div>

            <!-- Global Success Message -->
            <transition
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
            >
                <div v-if="globalMessage" class="mb-6 p-4 bg-green-50 border border-green-300 rounded-lg text-green-700 font-medium flex items-center gap-2">
                    <CheckCircle class="w-5 h-5" />
                    {{ globalMessage }}
                </div>
            </transition>

            <!-- Global Error Message -->
            <transition
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
            >
                <div v-if="globalError" class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg text-red-700 font-medium flex items-center gap-2">
                    <AlertCircle class="w-5 h-5" />
                    {{ globalError }}
                </div>
            </transition>

            <!-- Upload Cards -->
            <div class="space-y-6">
                <div
                    v-for="doc in documents"
                    :key="doc.key"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                    :class="{ 'border-green-300 bg-green-50/30': hasExistingFile(doc.key) && !showReupload[doc.key] }"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ doc.label }}
                                </h3>
                                <!-- Status Badge for uploaded files -->
                                <span 
                                    v-if="hasExistingFile(doc.key)"
                                    class="text-xs px-2 py-1 rounded-full"
                                    :class="getStatusBadgeClass(getExistingFile(doc.key).status)"
                                >
                                    {{ getStatusText(getExistingFile(doc.key).status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ doc.description }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle v-if="hasExistingFile(doc.key) && !showReupload[doc.key]" class="w-8 h-8 text-green-500" />
                            <FileText v-else class="w-8 h-8 text-emerald-500" />
                        </div>
                    </div>

                    <!-- EXISTING FILE DISPLAY (when already uploaded) -->
                    <div v-if="hasExistingFile(doc.key) && !showReupload[doc.key]" class="space-y-3">
                        <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <FileText class="w-6 h-6 text-red-600" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ getExistingFile(doc.key).original_filename }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatFileSize(getExistingFile(doc.key).file_size) }} • 
                                        Diupload {{ new Date(getExistingFile(doc.key).created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Preview Button -->
                                <button
                                    @click="previewFile(getExistingFile(doc.key).id)"
                                    class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                    title="Preview"
                                >
                                    <Eye class="w-5 h-5" />
                                </button>

                                <!-- Download Button -->
                                <button
                                    @click="downloadFile(getExistingFile(doc.key).id)"
                                    class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                    title="Download"
                                >
                                    <Download class="w-5 h-5" />
                                </button>

                                <!-- Reupload Button -->
                                <button
                                    @click="toggleReupload(doc.key)"
                                    class="p-2 text-orange-600 hover:bg-orange-100 rounded-lg transition-colors"
                                    title="Upload Ulang"
                                >
                                    <RefreshCw class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Success indicator -->
                        <div class="flex items-center gap-2 text-green-700 text-sm">
                            <CheckCircle class="w-4 h-4" />
                            <span>File telah berhasil diupload</span>
                        </div>
                    </div>

                    <!-- UPLOAD AREA (when no file or reupload mode) -->
                    <div v-else class="space-y-3">
                        <!-- Reupload Notice -->
                        <div v-if="showReupload[doc.key]" class="flex items-center justify-between bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3">
                            <p class="text-sm text-orange-700">
                                <RefreshCw class="w-4 h-4 inline mr-1" />
                                Mode upload ulang aktif. File lama akan diganti.
                            </p>
                            <button 
                                @click="toggleReupload(doc.key)"
                                class="text-sm text-orange-700 hover:text-orange-900 underline"
                            >
                                Batalkan
                            </button>
                        </div>

                        <!-- Upload Zone (when no file selected) -->
                        <div
                            v-if="!files[doc.key]"
                            :class="getUploadZoneClass(doc.key)"
                            @click="$refs[`fileInput_${doc.key}`][0].click()"
                            @dragover="handleDragOver(doc.key, $event)"
                            @dragleave="handleDragLeave(doc.key, $event)"
                            @drop="handleDrop(doc.key, $event)"
                            role="button"
                            tabindex="0"
                            @keydown.enter="$refs[`fileInput_${doc.key}`][0].click()"
                            @keydown.space.prevent="$refs[`fileInput_${doc.key}`][0].click()"
                        >
                            <!-- Drag Over Visual -->
                            <div v-if="isDragging[doc.key]" class="py-4">
                                <Upload class="w-12 h-12 text-emerald-500 mx-auto mb-3 animate-bounce" />
                                <p class="text-sm font-semibold text-emerald-700">Drop file di sini</p>
                            </div>
                            
                            <!-- Default State -->
                            <div v-else class="py-4">
                                <Upload class="w-10 h-10 text-gray-400 mx-auto mb-3" />
                                <p class="text-sm font-semibold text-gray-900 mb-1">
                                    Klik atau drag PDF di sini
                                </p>
                                <p class="text-xs text-gray-500">
                                    Format: PDF | Max: 10MB
                                </p>
                            </div>
                            
                            <input
                                :ref="`fileInput_${doc.key}`"
                                type="file"
                                accept="application/pdf"
                                class="hidden"
                                @change="handleFileSelect(doc.key, $event)"
                            />
                        </div>

                        <!-- Selected File Display -->
                        <div v-else class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <FileText class="w-6 h-6 text-red-600" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ files[doc.key].name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ (files[doc.key].size / 1024 / 1024).toFixed(2) }} MB
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Status Indicator -->
                                <CheckCircle
                                    v-if="uploadStatus[doc.key] === 'success'"
                                    class="w-5 h-5 text-green-500"
                                />
                                <AlertCircle
                                    v-if="uploadStatus[doc.key] === 'error'"
                                    class="w-5 h-5 text-red-500"
                                />
                                <Loader
                                    v-if="uploadStatus[doc.key] === 'uploading'"
                                    class="w-5 h-5 text-emerald-500 animate-spin"
                                />

                                <!-- Upload Button -->
                                <button
                                    v-if="uploadStatus[doc.key] !== 'success'"
                                    @click="uploadFile(doc.key)"
                                    :disabled="isUploading"
                                    class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                                >
                                    <Loader v-if="uploadStatus[doc.key] === 'uploading'" class="w-4 h-4 animate-spin" />
                                    {{ uploadStatus[doc.key] === 'uploading' ? 'Uploading...' : 'Upload' }}
                                </button>

                                <!-- Remove Button -->
                                <button
                                    @click="removeFile(doc.key)"
                                    :disabled="uploadStatus[doc.key] === 'uploading'"
                                    class="px-4 py-2 text-red-600 text-sm hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Success Message per Card -->
                        <div v-if="uploadStatus[doc.key] === 'success'" class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <CheckCircle class="w-4 h-4 text-green-600" />
                            <p class="text-sm text-green-700">File berhasil diupload!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload All Button -->
            <div class="mt-8 text-center">
                <button
                    @click="uploadAllFiles"
                    :disabled="isUploading || Object.values(files).every(f => f === null)"
                    class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2 mx-auto"
                >
                    <Loader v-if="isUploading" class="w-5 h-5 animate-spin" />
                    {{ isUploading ? 'Sedang Upload...' : 'Upload Semua File' }}
                </button>
            </div>

            <!-- Info Notice -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <AlertCircle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Catatan Penting:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li>Pastikan semua dokumen telah lengkap sebelum melanjutkan</li>
                            <li>Format file yang diizinkan: PDF</li>
                            <li>Maksimal ukuran file: 10 MB per dokumen</li>
                            <li>Anda dapat drag & drop file langsung ke area upload</li>
                        </ul>
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

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-4px);
    }
}

.animate-bounce {
    animation: bounce 1s ease-in-out infinite;
}
</style>
