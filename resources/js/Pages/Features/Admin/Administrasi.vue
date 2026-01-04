<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import FileUploadDragDrop from '@/Components/FileUploadDragDrop.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    FileText, 
    Download, 
    Eye, 
    ArrowLeft, 
    Loader, 
    CheckCircle, 
    XCircle,
    ClipboardList,
    ClipboardCheck,
    Users as UsersIcon,
    FileCheck,
    ExternalLink,
    X,
    Upload
} from 'lucide-vue-next';

// Props dari controller
const props = defineProps({
    user: Object,
    a5_files: Array,
    a6_files: Array,
    a7_data: Array,
    a8_data: Object
});

// ============================================================================
// STATE
// ============================================================================
const selectedFile = ref(null);
const showPreview = ref(false);
const previewUrl = ref('');
const downloadUrl = ref('');
const isLoading = ref(false);
const showUploadModal = ref(false);
const uploadCategory = ref('');
const uploadIndikator = ref('');
const selectedIndicators = ref([]);
const refreshKey = ref(0);

// Helper untuk mendapatkan nama file dari path
const getFileName = (path) => {
    if (!path) return 'Unknown';
    return path.split('/').pop();
};

// Cek apakah ada data di setiap section
const hasA5 = computed(() => props.a5_files && props.a5_files.length > 0);
const hasA6 = computed(() => props.a6_files && props.a6_files.length > 0);
const hasA7 = computed(() => props.a7_data && props.a7_data.length > 0);
const hasA8 = computed(() => props.a8_data !== null);

// ============================================================================
// METHODS
// ============================================================================

// Kembali ke dashboard
const goBack = () => {
    router.visit(route('dashboard'));
};

/**
 * Open preview modal
 */
const openPreview = async (file, type) => {
    isLoading.value = true;
    
    // Set selected file info
    selectedFile.value = {
        id: file.id,
        name: getFileName(type === 'a8' ? file.bukti_persetujuan : file.path_file),
        indikator: file.indikator || 'Bukti Persetujuan',
        type: type
    };
    
    // Set preview URL dengan parameter type
    previewUrl.value = route('file-evidence.preview', { type: type, id: file.id });
    downloadUrl.value = route('file-evidence.download', { type: type, id: file.id });
    showPreview.value = true;
    
    // Simulate loading delay
    await new Promise(resolve => setTimeout(resolve, 300));
    isLoading.value = false;
};

/**
 * Close preview modal
 */
const closePreview = () => {
    showPreview.value = false;
    isLoading.value = false;
    selectedFile.value = null;
    previewUrl.value = '';
    downloadUrl.value = '';
};

/**
 * Open PDF in new tab
 */
const openInNewTab = () => {
    window.open(previewUrl.value, '_blank');
};

/**
 * Download file - dari card langsung
 */
const downloadFileFromCard = (fileId, type) => {
    window.open(route('file-evidence.download', { type: type, id: fileId }), '_blank');
};

/**
 * Download file - dari modal
 */
const downloadFile = () => {
    if (downloadUrl.value) {
        window.open(downloadUrl.value, '_blank');
    }
};

/**
 * Handle keyboard event (close on Escape)
 */
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        closePreview();
    }
};

/**
 * Open upload modal
 */
const openUploadModal = (category, section) => {
    uploadCategory.value = category;
    
    // Set indicators based on category
    if (category === 'rencana') {
        selectedIndicators.value = [
            'Evaluasi Diri Sekolah',
            'Hasil IPMLH',
            'Rencana GPBLHS 4-tahunan',
            'Rencana GPBLHS tahunan',
            'Dokumentasi Penyusunan',
            'SK Tim Sekolah'
        ];
    } else if (category === 'self_assessment') {
        selectedIndicators.value = [
            'Bukti Kebijakan Berwawasan Lingkungan',
            'Bukti Pelaksanaan Kurikulum',
            'Bukti Kegiatan Berbasis Lingkungan',
        ];
    } else if (category === 'pernyataan') {
        selectedIndicators.value = ['Bukti Persetujuan'];
    }
    
    uploadIndikator.value = '';
    showUploadModal.value = true;
};

/**
 * Close upload modal
 */
const closeUploadModal = () => {
    showUploadModal.value = false;
    uploadCategory.value = '';
    uploadIndikator.value = '';
    selectedIndicators.value = [];
};

/**
 * Handle upload success
 */
const handleUploadSuccess = (data) => {
    alert(`File berhasil diupload: ${data.original_filename}`);
    refreshKey.value++;
    // Refresh page data
    router.reload({ only: ['a5_files', 'a6_files', 'a8_data'] });
};

/**
 * Handle upload error
 */
const handleUploadError = (error) => {
    alert(`Upload gagal: ${error}`);
};
</script>

<template>
    <MainLayout>
        <Head :title="`File Submission - ${user?.name}`" />

        <Header
            title="Administrasi File Submission"
            :description="`Melihat file submission dari: ${user?.name}`"
            color="blue"
        />

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Back Button -->
                <div class="mb-6">
                    <button
                        @click="goBack"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Kembali ke Dashboard
                    </button>
                </div>

                <!-- User Info Card -->
                <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-600">Nama Pengguna</p>
                    <p class="text-2xl font-bold text-gray-900">{{ user?.name }}</p>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- A5 - Rencana -->
                    <div v-if="hasA5" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-blue-50 border-b border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <ClipboardList class="w-5 h-5 text-blue-600" />
                                    <h3 class="font-bold text-lg text-gray-900">A5 - Rencana & Evaluasi PBLHS</h3>
                                </div>
                                <button
                                    @click="openUploadModal('rencana', 'A5')"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition"
                                >
                                    <Upload class="w-4 h-4" />
                                    Upload
                                </button>
                            </div>
                        </div>
                        <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                            <div v-for="file in a5_files" :key="file.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <FileText class="w-4 h-4 text-gray-600 flex-shrink-0" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ file.indikator }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ getFileName(file.path_file) }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button
                                        @click="openPreview(file, 'a5')"
                                        class="p-2 hover:bg-blue-100 rounded transition"
                                        title="Preview"
                                    >
                                        <Eye class="w-4 h-4 text-blue-600" />
                                    </button>
                                    <button
                                        @click="downloadFileFromCard(file.id, 'a5')"
                                        class="p-2 hover:bg-green-100 rounded transition"
                                        title="Download"
                                    >
                                        <Download class="w-4 h-4 text-green-600" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- A6 - Bukti Self Assessment -->
                    <div v-if="hasA6" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-green-50 border-b border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <CheckCircle class="w-5 h-5 text-green-600" />
                                    <h3 class="font-bold text-lg text-gray-900">A6 - Self Assessment</h3>
                                </div>
                                <button
                                    @click="openUploadModal('self_assessment', 'A6')"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition"
                                >
                                    <Upload class="w-4 h-4" />
                                    Upload
                                </button>
                            </div>
                        </div>
                        <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                            <div v-for="file in a6_files" :key="file.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <FileText class="w-4 h-4 text-gray-600 flex-shrink-0" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ file.indikator }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ getFileName(file.path_file) }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button
                                        @click="openPreview(file, 'a6')"
                                        class="p-2 hover:bg-blue-100 rounded transition"
                                        title="Preview"
                                    >
                                        <Eye class="w-4 h-4 text-blue-600" />
                                    </button>
                                    <button
                                        @click="downloadFileFromCard(file.id, 'a6')"
                                        class="p-2 hover:bg-green-100 rounded transition"
                                        title="Download"
                                    >
                                        <Download class="w-4 h-4 text-green-600" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- A7 - Kebutuhan Pendampingan -->
                    <div v-if="hasA7" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-amber-50 border-b border-gray-200 p-4">
                            <div class="flex items-center gap-2">
                                <UsersIcon class="w-5 h-5 text-amber-600" />
                                <h3 class="font-bold text-lg text-gray-900">A7 - Kebutuhan Pendampingan</h3>
                            </div>
                        </div>
                        <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                            <div v-for="item in a7_data" :key="item.id" class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-900">{{ item.kebutuhan }}</p>
                                <p class="text-xs text-gray-500 mt-1">Prioritas: <span class="font-semibold">{{ item.prioritas }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- A8 - Pernyataan & Persetujuan -->
                    <div v-if="hasA8" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-purple-50 border-b border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <FileCheck class="w-5 h-5 text-purple-600" />
                                    <h3 class="font-bold text-lg text-gray-900">A8 - Pernyataan & Persetujuan</h3>
                                </div>
                                <button
                                    @click="openUploadModal('pernyataan', 'A8')"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition"
                                >
                                    <Upload class="w-4 h-4" />
                                    Upload
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <FileText class="w-4 h-4 text-gray-600 flex-shrink-0" />
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Bukti Persetujuan</p>
                                        <p class="text-xs text-gray-500">{{ getFileName(a8_data?.bukti_persetujuan) }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button
                                        @click="openPreview(a8_data, 'a8')"
                                        class="p-2 hover:bg-blue-100 rounded transition"
                                        title="Preview"
                                    >
                                        <Eye class="w-4 h-4 text-blue-600" />
                                    </button>
                                    <button
                                        @click="downloadFileFromCard(a8_data.id, 'a8')"
                                        class="p-2 hover:bg-green-100 rounded transition"
                                        title="Download"
                                    >
                                        <Download class="w-4 h-4 text-green-600" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================== -->
        <!-- PDF PREVIEW MODAL -->
        <!-- ================================================================== -->
        <transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showPreview"
                @click="closePreview"
                @keydown="handleKeydown"
                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
                aria-label="PDF Preview"
            >
                <div
                    @click.stop
                    class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">{{ selectedFile?.name }}</h3>
                            <p class="text-sm text-gray-500">{{ selectedFile?.indikator }}</p>
                        </div>
                        <button
                            @click="closePreview"
                            class="p-2 hover:bg-gray-200 rounded transition"
                            title="Close"
                        >
                            <X class="w-5 h-5 text-gray-600" />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-hidden flex flex-col">
                        <div v-if="isLoading" class="flex items-center justify-center h-full">
                            <Loader class="w-8 h-8 animate-spin text-blue-600" />
                        </div>
                        <iframe
                            v-else
                            :src="previewUrl"
                            class="w-full h-full border-0"
                        ></iframe>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between p-6 border-t border-gray-200 bg-gray-50">
                        <p class="text-sm text-gray-600">
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded text-xs font-mono">ESC</kbd>
                            untuk tutup
                        </p>
                        <div class="flex gap-3">
                            <button
                                @click="openInNewTab"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
                            >
                                <ExternalLink class="w-4 h-4" />
                                Buka di Tab Baru
                            </button>
                            <button
                                @click="downloadFile"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition"
                            >
                                <Download class="w-4 h-4" />
                                Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ================================================================== -->
        <!-- UPLOAD MODAL -->
        <!-- ================================================================== -->
        <transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showUploadModal"
                @click="closeUploadModal"
                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            >
                <div
                    @click.stop
                    class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">Upload File</h3>
                            <p class="text-sm text-gray-500">Pilih indikator dan upload file PDF</p>
                        </div>
                        <button
                            @click="closeUploadModal"
                            class="p-2 hover:bg-gray-200 rounded transition"
                        >
                            <X class="w-5 h-5 text-gray-600" />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <!-- Indicator Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Indikator
                            </label>
                            <select
                                v-model="uploadIndikator"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">-- Pilih Indikator --</option>
                                <option v-for="(indicator, idx) in selectedIndicators" :key="idx" :value="indicator">
                                    {{ indicator }}
                                </option>
                            </select>
                        </div>

                        <!-- Upload Component -->
                        <FileUploadDragDrop
                            v-if="uploadIndikator"
                            :key="uploadIndikator + refreshKey"
                            :category="uploadCategory"
                            :indikator="uploadIndikator"
                            :onUploadSuccess="handleUploadSuccess"
                            :onUploadError="handleUploadError"
                        />

                        <div v-else class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <Upload class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                            <p class="text-sm text-gray-500">
                                Pilih indikator terlebih dahulu untuk mulai upload
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
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

.max-h-80::-webkit-scrollbar {
    width: 6px;
}

.max-h-80::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.max-h-80::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.max-h-80::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

kbd {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    border-bottom: 2px solid currentColor;
    box-shadow: 0 2px 0 currentColor;
}
</style>
