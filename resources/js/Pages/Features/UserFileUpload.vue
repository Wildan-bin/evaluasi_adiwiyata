<template>
    <AuthenticatedLayout
        :user="auth.user"
    >
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upload Dokumen</h2>
        </template>

        <Head title="Upload Dokumen" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Step Navigation -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <div class="flex items-center justify-between">
                        <template v-for="(step, idx) in steps" :key="step.id">
                            <button
                                @click="currentStep = step.id"
                                :class="[
                                    'flex-1 text-center py-3 px-4 rounded-lg font-medium transition-colors',
                                    currentStep === step.id
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                ]"
                            >
                                <div class="text-sm">Step {{ step.id }}</div>
                                <div class="text-xs mt-1">{{ step.title }}</div>
                            </button>
                            <div v-if="idx < steps.length - 1" class="w-8 flex justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Upload Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ currentStepData.title }}
                        </h3>

                        <!-- Indicator Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Indikator
                            </label>
                            <select
                                v-model="selectedIndicator"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">-- Pilih Indikator --</option>
                                <option v-for="(indicator, idx) in currentStepData.indicators" :key="idx" :value="indicator">
                                    {{ indicator }}
                                </option>
                            </select>
                        </div>

                        <!-- Upload Component -->
                        <FileUploadDragDrop
                            v-if="selectedIndicator"
                            :key="selectedIndicator"
                            :category="currentStepData.category"
                            :indikator="selectedIndicator"
                            :section="currentStepData.section"
                            :onUploadSuccess="handleUploadSuccess"
                            :onUploadError="handleUploadError"
                        />

                        <div v-else class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
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
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">
                                Pilih indikator terlebih dahulu untuk mulai upload
                            </p>
                        </div>
                    </div>

                    <!-- File List Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            File yang Sudah Diupload
                        </h3>
                        <UserFileList
                            :key="refreshKey"
                            :category="currentStepData.category"
                            :onFileDeleted="() => refreshKey++"
                        />
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex justify-between">
                    <button
                        @click="currentStep = Math.max(1, currentStep - 1)"
                        :disabled="currentStep === 1"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        ← Sebelumnya
                    </button>
                    <button
                        @click="currentStep = Math.min(steps.length, currentStep + 1)"
                        :disabled="currentStep === steps.length"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Selanjutnya →
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUploadDragDrop from '@/Components/FileUploadDragDrop.vue';
import UserFileList from '@/Components/UserFileList.vue';

const props = defineProps({
    auth: {
        type: Object,
        required: true
    }
});

const currentStep = ref(1);
const refreshKey = ref(0);
const selectedIndicator = ref('');

const steps = [
    {
        id: 1,
        title: 'Rencana & Evaluasi (A5)',
        category: 'rencana',
        section: 'A5',
        indicators: [
            'Evaluasi Diri Sekolah',
            'Hasil IPMLH',
            'Rencana GPBLHS 4-tahunan',
            'Rencana GPBLHS tahunan',
            'Dokumentasi Penyusunan',
            'SK Tim Sekolah'
        ]
    },
    {
        id: 2,
        title: 'Self-Assessment (A6)',
        category: 'self_assessment',
        section: 'A6',
        indicators: [
            'Bukti Kebijakan Berwawasan Lingkungan',
            'Bukti Pelaksanaan Kurikulum',
            'Bukti Kegiatan Berbasis Lingkungan',
        ]
    },
    {
        id: 3,
        title: 'Pendampingan (A7)',
        category: 'pendampingan',
        section: 'A7',
        indicators: [
            'Dokumentasi Pendampingan',
        ]
    },
    {
        id: 4,
        title: 'Pernyataan & Persetujuan (A8)',
        category: 'pernyataan',
        section: 'A8',
        indicators: [
            'Bukti Persetujuan',
        ]
    },
];

const currentStepData = computed(() => steps.find(s => s.id === currentStep.value) || steps[0]);

const handleUploadSuccess = (data) => {
    alert(`File berhasil diupload: ${data.original_filename}`);
    refreshKey.value++;
    selectedIndicator.value = '';
};

const handleUploadError = (error) => {
    console.error('Upload error:', error);
};
</script>
