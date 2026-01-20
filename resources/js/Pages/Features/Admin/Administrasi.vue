<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
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
    X
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
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Kembali ke Dashboard
                    </button>
                </div>

                <!-- User Info Card -->
                <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <UsersIcon class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ user?.name }}</h2>
                            <p class="text-sm text-gray-500">{{ user?.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- A5 Card - Rencana & Evaluasi PBLHS -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-emerald-500 to-green-600">
                            <div class="flex items-center gap-3">
                                <ClipboardList class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">A5 - Rencana & Evaluasi PBLHS</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasA5" class="space-y-3">
                                <div 
                                    v-for="file in a5_files" 
                                    :key="file.id"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <FileText class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ file.indikator }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ getFileName(file.path_file) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <button
                                            @click="openPreview(file, 'a5')"
                                            class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                            title="Preview"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="downloadFileFromCard(file.id, 'a5')"
                                            class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                            title="Download"
                                        >
                                            <Download class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <XCircle class="w-12 h-12 mb-2" />
                                <p class="text-sm">Belum ada data</p>
                            </div>
                        </div>
                    </div>

                    <!-- A6 Card - Bukti Self Assessment -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600">
                            <div class="flex items-center gap-3">
                                <ClipboardCheck class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">A6 - Bukti Self Assessment</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasA6" class="space-y-3 max-h-80 overflow-y-auto">
                                <div 
                                    v-for="file in a6_files" 
                                    :key="file.id"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <FileText class="w-5 h-5 text-blue-600 flex-shrink-0" />
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ file.indikator }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ getFileName(file.path_file) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <button
                                            @click="openPreview(file, 'a6')"
                                            class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                            title="Preview"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="downloadFileFromCard(file.id, 'a6')"
                                            class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                            title="Download"
                                        >
                                            <Download class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <XCircle class="w-12 h-12 mb-2" />
                                <p class="text-sm">Belum ada data</p>
                            </div>
                        </div>
                    </div>

                    <!-- A7 Card - Kebutuhan Pendampingan -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-amber-500 to-orange-600">
                            <div class="flex items-center gap-3">
                                <UsersIcon class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">A7 - Kebutuhan Pendampingan</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasA7" class="space-y-3">
                                <div 
                                    v-for="item in a7_data" 
                                    :key="item.id"
                                    class="p-3 bg-gray-50 rounded-lg"
                                >
                                    <div class="flex items-start gap-3">
                                        <CheckCircle class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ item.kebutuhan }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Waktu: {{ item.waktu_pendampingan || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <XCircle class="w-12 h-12 mb-2" />
                                <p class="text-sm">Belum ada data</p>
                            </div>
                        </div>
                    </div>

                    <!-- A8 Card - Pernyataan & Persetujuan -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-purple-500 to-violet-600">
                            <div class="flex items-center gap-3">
                                <FileCheck class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">A8 - Pernyataan & Persetujuan</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasA8" class="space-y-4">
                                <!-- Pernyataan Data -->
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Pernyataan Data</p>
                                    <div class="flex items-center gap-2">
                                        <CheckCircle v-if="a8_data.pernyataan_data === 'benar'" class="w-4 h-4 text-green-600" />
                                        <XCircle v-else class="w-4 h-4 text-red-600" />
                                        <span class="text-sm font-medium text-gray-900 capitalize">{{ a8_data.pernyataan_data }}</span>
                                    </div>
                                </div>

                                <!-- Persetujuan Publikasi -->
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Persetujuan Publikasi</p>
                                    <div class="flex items-center gap-2">
                                        <CheckCircle v-if="a8_data.persetujuan_publikasi === 'setuju'" class="w-4 h-4 text-green-600" />
                                        <XCircle v-else class="w-4 h-4 text-red-600" />
                                        <span class="text-sm font-medium text-gray-900 capitalize">{{ a8_data.persetujuan_publikasi }}</span>
                                    </div>
                                </div>

                                <!-- Bukti Persetujuan File -->
                                <div v-if="a8_data.bukti_persetujuan" class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Bukti Persetujuan</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <FileText class="w-4 h-4 text-purple-600" />
                                            <span class="text-sm text-gray-700 truncate">{{ getFileName(a8_data.bukti_persetujuan) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button
                                                @click="openPreview(a8_data, 'a8')"
                                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                                title="Preview"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="downloadFileFromCard(a8_data.id, 'a8')"
                                                class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                                title="Download"
                                            >
                                                <Download class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <XCircle class="w-12 h-12 mb-2" />
                                <p class="text-sm">Belum ada data</p>
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
                class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 md:p-6"
                role="dialog"
                aria-modal="true"
                aria-label="PDF Preview"
            >
                <div
                    @click.stop
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl h-[90vh] flex flex-col overflow-hidden"
                >
                    <!-- HEADER -->
                    <div class="flex-shrink-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 md:px-8 py-4 flex items-center justify-between border-b border-blue-800">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg md:text-xl font-bold text-white truncate">
                                {{ selectedFile?.indikator }}
                            </h2>
                            <p class="text-blue-100 text-xs md:text-sm mt-1 truncate">
                                {{ selectedFile?.name }}
                            </p>
                        </div>
                        <button
                            @click="closePreview"
                            class="flex-shrink-0 ml-4 p-2 hover:bg-blue-500 rounded-lg transition-colors duration-200"
                            aria-label="Close preview"
                            title="Close (Esc)"
                        >
                            <X class="w-6 h-6 text-white" />
                        </button>
                    </div>

                    <!-- CONTENT -->
                    <div class="flex-1 overflow-auto bg-gray-900 relative flex items-center justify-center">
                        <div
                            v-if="isLoading"
                            class="absolute inset-0 flex items-center justify-center bg-gray-900/80 z-10"
                        >
                            <div class="text-center">
                                <Loader class="w-12 h-12 text-blue-500 animate-spin mx-auto mb-3" />
                                <p class="text-white font-semibold">Memuat preview...</p>
                            </div>
                        </div>
                        <iframe
                            v-if="previewUrl"
                            :src="previewUrl"
                            class="w-full h-full border-0"
                            title="PDF Preview"
                            @load="isLoading = false"
                        ></iframe>
                    </div>

                    <!-- FOOTER -->
                    <div class="flex-shrink-0 bg-gray-50 px-6 md:px-8 py-4 border-t border-gray-200 flex items-center justify-between gap-3">
                        <p class="text-sm text-gray-600 hidden sm:block">
                            Press <kbd class="px-2 py-1 bg-gray-200 rounded text-gray-800 font-mono text-xs">Esc</kbd> to close
                        </p>
                        <div class="flex items-center gap-3">
                            <button
                                @click="openInNewTab"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-all duration-200"
                                title="Open in new tab"
                            >
                                <ExternalLink class="w-4 h-4" />
                                <span class="hidden sm:inline">Open in Tab</span>
                            </button>
                            <button
                                @click="downloadFile"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                title="Download file"
                            >
                                <Download class="w-4 h-4" />
                                <span class="hidden sm:inline">Download</span>
                            </button>
                            <button
                                @click="closePreview"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 font-semibold hover:bg-red-200 transition-all duration-200"
                                title="Close preview"
                            >
                                <X class="w-4 h-4" />
                                <span class="hidden sm:inline">Close</span>
                            </button>
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