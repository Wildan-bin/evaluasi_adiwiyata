<template>
    <div class="w-full">
        <!-- Upload Success State -->
        <div v-if="uploadedFile" class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-4">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-green-900">File Berhasil Diupload</h4>
                        <p class="text-sm text-green-700 mt-1">{{ uploadedFile.original_filename }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ uploadedFile.file_size }} • {{ uploadedFile.uploaded_at }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            Status: <span class="font-medium">{{ uploadedFile.status }}</span>
                        </p>
                    </div>
                </div>
                <button
                    @click="handleReset"
                    class="text-green-600 hover:text-green-800 text-sm font-medium"
                >
                    Upload Lagi
                </button>
            </div>
        </div>

        <!-- Upload Area -->
        <div v-if="!uploadedFile">
            <div
                :class="[
                    'relative border-2 border-dashed rounded-lg p-8 text-center transition-all',
                    isDragging
                        ? 'border-blue-500 bg-blue-50'
                        : 'border-gray-300 bg-white hover:border-gray-400',
                    isUploading ? 'pointer-events-none opacity-50' : ''
                ]"
                @dragenter="handleDragEnter"
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop"
            >
                <input
                    ref="fileInputRef"
                    type="file"
                    accept="application/pdf"
                    @change="handleFileSelect"
                    class="hidden"
                    id="file-upload"
                />

                <template v-if="!selectedFile">
                    <div>
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 48 48"
                        >
                            <path
                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        <div class="mt-4">
                            <label
                                for="file-upload"
                                class="cursor-pointer rounded-md bg-white font-medium text-blue-600 hover:text-blue-500"
                            >
                                <span>Upload file</span>
                            </label>
                            <p class="pl-1 inline">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            PDF hingga {{ maxSize }} MB
                        </p>
                    </div>
                </template>

                <template v-else>
                    <div class="space-y-4">
                        <div class="flex items-center justify-center space-x-3">
                            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</p>
                                <p class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div v-if="isUploading" class="w-full bg-gray-200 rounded-full h-2">
                            <div
                                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                :style="{ width: `${uploadProgress}%` }"
                            />
                        </div>

                        <div class="flex space-x-3 justify-center">
                            <button
                                @click="handleUpload"
                                :disabled="isUploading"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium text-white',
                                    isUploading
                                        ? 'bg-blue-400 cursor-not-allowed'
                                        : 'bg-blue-600 hover:bg-blue-700'
                                ]"
                            >
                                {{ isUploading ? `Uploading ${uploadProgress}%` : 'Upload File' }}
                            </button>
                            <button
                                @click="handleReset"
                                :disabled="isUploading"
                                class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 disabled:opacity-50"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="mt-3 bg-red-50 border border-red-200 rounded-md p-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <p class="text-sm text-red-700">{{ error }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    category: {
        type: String,
        required: true
    },
    indikator: {
        type: String,
        required: true
    },
    section: {
        type: String,
        default: ''
    },
    onUploadSuccess: {
        type: Function,
        default: null
    },
    onUploadError: {
        type: Function,
        default: null
    },
    maxSize: {
        type: Number,
        default: 5
    }
});

const isDragging = ref(false);
const selectedFile = ref(null);
const uploadProgress = ref(0);
const isUploading = ref(false);
const error = ref('');
const uploadedFile = ref(null);
const fileInputRef = ref(null);

const maxSizeBytes = props.maxSize * 1024 * 1024;

const validateFile = (file) => {
    error.value = '';

    if (!file) {
        error.value = 'Tidak ada file yang dipilih';
        return false;
    }

    if (file.type !== 'application/pdf') {
        error.value = 'Hanya file PDF yang diperbolehkan';
        return false;
    }

    if (file.size > maxSizeBytes) {
        error.value = `Ukuran file melebihi ${props.maxSize} MB`;
        return false;
    }

    return true;
};

const handleDragEnter = (e) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = true;
};

const handleDragLeave = (e) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = false;
};

const handleDragOver = (e) => {
    e.preventDefault();
    e.stopPropagation();
};

const handleDrop = (e) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = false;

    const files = e.dataTransfer.files;
    if (files && files.length > 0) {
        const file = files[0];
        if (validateFile(file)) {
            selectedFile.value = file;
        }
    }
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file && validateFile(file)) {
        selectedFile.value = file;
    }
};

const handleUpload = async () => {
    if (!selectedFile.value) {
        error.value = 'Pilih file terlebih dahulu';
        return;
    }

    isUploading.value = true;
    error.value = '';
    uploadProgress.value = 0;

    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('category', props.category);
    formData.append('indikator', props.indikator);
    formData.append('section', props.section);

    try {
        const response = await axios.post('/api/file-upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                const progress = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                );
                uploadProgress.value = progress;
            },
        });

        if (response.data.success) {
            uploadedFile.value = response.data.data;
            selectedFile.value = null;
            if (props.onUploadSuccess) {
                props.onUploadSuccess(response.data.data);
            }
        }
    } catch (err) {
        const errorMessage = err.response?.data?.message || 'Gagal mengupload file';
        error.value = errorMessage;
        if (props.onUploadError) {
            props.onUploadError(errorMessage);
        }
    } finally {
        isUploading.value = false;
        uploadProgress.value = 0;
    }
};

const handleReset = () => {
    selectedFile.value = null;
    uploadedFile.value = null;
    error.value = '';
    uploadProgress.value = 0;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};
</script>
