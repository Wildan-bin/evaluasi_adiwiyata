<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Upload, FileText, CheckCircle, AlertCircle } from 'lucide-vue-next';

// State
const files = ref({
    a1_sk: null,
    a2_tim: null,
    a3_rencana: null,
    a4_anggaran: null
});

const uploadStatus = ref({
    a1_sk: null,
    a2_tim: null,
    a3_rencana: null,
    a4_anggaran: null
});

const isUploading = ref(false);

// File handlers
const handleFileSelect = (key, event) => {
    const file = event.target.files[0];
    if (file) {
        files.value[key] = file;
        uploadStatus.value[key] = null; // Reset status
    }
};

const uploadFile = async (key) => {
    if (!files.value[key]) return;

    isUploading.value = true;
    uploadStatus.value[key] = 'uploading';

    const formData = new FormData();
    formData.append('file', files.value[key]);
    formData.append('type', key);

    try {
        // TODO: Replace with actual API endpoint
        // const response = await axios.post('/api/administration/upload', formData);

        // Simulate upload
        await new Promise(resolve => setTimeout(resolve, 1500));

        uploadStatus.value[key] = 'success';
    } catch (error) {
        console.error('Upload error:', error);
        uploadStatus.value[key] = 'error';
    } finally {
        isUploading.value = false;
    }
};

const removeFile = (key) => {
    files.value[key] = null;
    uploadStatus.value[key] = null;
};

// Document sections
const documents = [
    {
        key: 'a1_sk',
        label: 'A1 - SK Tim Adiwiyata',
        description: 'Upload Surat Keputusan (SK) pembentukan Tim Adiwiyata sekolah',
        accept: '.pdf,.doc,.docx'
    },
    {
        key: 'a2_tim',
        label: 'A2 - Struktur Tim Adiwiyata',
        description: 'Upload dokumen struktur dan susunan Tim Adiwiyata',
        accept: '.pdf,.doc,.docx'
    },
    {
        key: 'a3_rencana',
        label: 'A3 - Rencana Kegiatan',
        description: 'Upload rencana kegiatan program Adiwiyata',
        accept: '.pdf,.doc,.docx,.xls,.xlsx'
    },
    {
        key: 'a4_anggaran',
        label: 'A4 - Anggaran Program',
        description: 'Upload dokumen anggaran untuk program Adiwiyata',
        accept: '.pdf,.doc,.docx,.xls,.xlsx'
    }
];
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

            <!-- Upload Cards -->
            <div class="space-y-6">
                <div
                    v-for="doc in documents"
                    :key="doc.key"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                {{ doc.label }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ doc.description }}
                            </p>
                        </div>
                        <FileText class="w-8 h-8 text-emerald-500" />
                    </div>

                    <!-- File Input Area -->
                    <div class="space-y-3">
                        <div
                            v-if="!files[doc.key]"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-emerald-400 transition-colors cursor-pointer"
                            @click="$refs[`fileInput_${doc.key}`][0].click()"
                        >
                            <Upload class="w-10 h-10 text-gray-400 mx-auto mb-3" />
                            <p class="text-sm text-gray-600 mb-1">
                                Klik untuk memilih file
                            </p>
                            <p class="text-xs text-gray-500">
                                Format: {{ doc.accept }}
                            </p>
                            <input
                                :ref="`fileInput_${doc.key}`"
                                type="file"
                                :accept="doc.accept"
                                class="hidden"
                                @change="handleFileSelect(doc.key, $event)"
                            />
                        </div>

                        <!-- Selected File Display -->
                        <div v-else class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <FileText class="w-5 h-5 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ files[doc.key].name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ (files[doc.key].size / 1024).toFixed(2) }} KB
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

                                <!-- Upload Button -->
                                <button
                                    v-if="uploadStatus[doc.key] !== 'success'"
                                    @click="uploadFile(doc.key)"
                                    :disabled="isUploading"
                                    class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    {{ uploadStatus[doc.key] === 'uploading' ? 'Uploading...' : 'Upload' }}
                                </button>

                                <!-- Remove Button -->
                                <button
                                    @click="removeFile(doc.key)"
                                    class="px-4 py-2 text-red-600 text-sm hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Notice -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <AlertCircle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Catatan Penting:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li>Pastikan semua dokumen telah lengkap sebelum melanjutkan</li>
                            <li>Format file yang diizinkan: PDF, DOC, DOCX, XLS, XLSX</li>
                            <li>Maksimal ukuran file: 10 MB per dokumen</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
