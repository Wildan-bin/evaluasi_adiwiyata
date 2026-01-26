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
    Building2,
    Users as UsersIcon,
    FileCheck,
    ExternalLink,
    X,
    ArrowRight,
    MapPin,
    Phone,
    Mail,
    Globe
} from 'lucide-vue-next';

// Props dari controller
const props = defineProps({
    sekolah: Object,
    skTim: Object,
    ketua: Object,
    anggota: Array,
    administrasiDasar: Array,
    user: Object
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
const hasSekolah = computed(() => props.sekolah !== null);
const hasSkTim = computed(() => props.skTim !== null && props.ketua !== null);
const hasAdministrasiDasar = computed(() => props.administrasiDasar && props.administrasiDasar.length > 0);

// ============================================================================
// METHODS
// ============================================================================

// Kembali ke administrasi
const goBack = () => {
    router.visit(route('administrasi'));
};

// Kembali ke dashboard
const goBackDashboard = () => {
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
        name: getFileName(file.path_file),
        indikator: file.indikator || type === 'skTim' ? 'SK Tim Adiwiyata' : file.indikator,
        type: type
    };
    
    // Set preview URL dengan parameter type
    previewUrl.value = route('file-administrasi.preview', { type: type, id: file.id });
    downloadUrl.value = route('file-administrasi.download', { type: type, id: file.id });
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
    window.open(route('file-administrasi.download', { type: type, id: fileId }), '_blank');
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
        <Head title="File Administrasi Saya" />

        <Header
            title="File Administrasi Saya"
            description="Lihat semua data dan file administrasi yang telah Anda upload"
            color="emerald"
        />

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Back Button -->
                <div class="mb-6 flex gap-4">
                    <button
                        @click="goBack"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Kembali ke Administrasi
                    </button>
                    <button
                        @click="goBackDashboard"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Dashboard
                        <ArrowRight class="w-4 h-4" />
                    </button>
                </div>

                <!-- User Info Card -->
                <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                            <Building2 class="w-6 h-6 text-emerald-600" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Data Administrasi Sekolah</h2>
                            <p class="text-sm text-gray-500">Lihat dan kelola semua data administrasi yang telah diupload</p>
                        </div>
                    </div>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Data Sekolah Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-emerald-500 to-green-600">
                            <div class="flex items-center gap-3">
                                <Building2 class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">Data Sekolah</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasSekolah" class="space-y-4">
                                <!-- Identitas Sekolah -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Identitas Sekolah</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500 mb-1">Nama Sekolah</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ sekolah?.nama_sekolah || '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-500 mb-1">NPSN</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ sekolah?.npsn || '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Alamat</h4>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <MapPin class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" />
                                            <div>
                                                <p class="text-sm text-gray-900">{{ sekolah?.alamat || '-' }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ sekolah?.kelurahan }}, {{ sekolah?.kecamatan }}, 
                                                    {{ sekolah?.kota }}, {{ sekolah?.provinsi }} 
                                                    {{ sekolah?.kode_pos }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontak -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Kontak</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-gray-50 p-3 rounded-lg flex items-center gap-2">
                                            <Phone class="w-4 h-4 text-gray-400" />
                                            <span class="text-sm text-gray-900">{{ sekolah?.telepon || '-' }}</span>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg flex items-center gap-2">
                                            <Mail class="w-4 h-4 text-gray-400" />
                                            <span class="text-sm text-gray-900 truncate">{{ sekolah?.email || '-' }}</span>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg flex items-center gap-2 col-span-2">
                                            <Globe class="w-4 h-4 text-gray-400" />
                                            <span class="text-sm text-gray-900 truncate">{{ sekolah?.website || '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kepala Sekolah -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Kepala Sekolah</h4>
                                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                                        <p class="text-sm font-semibold text-gray-900">{{ sekolah?.nama_kepala_sekolah || '-' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            NIP: {{ sekolah?.nip_kepala_sekolah || '-' }} • 
                                            Telp: {{ sekolah?.telp_kepala_sekolah || '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <XCircle class="w-12 h-12 mb-2" />
                                <p class="text-sm">Belum ada data sekolah</p>
                            </div>
                        </div>
                    </div>

                    <!-- SK Tim & Struktur Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600">
                            <div class="flex items-center gap-3">
                                <UsersIcon class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">SK Tim & Struktur</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasSkTim" class="space-y-4 max-h-[500px] overflow-y-auto">
                                <!-- SK Tim File -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">SK Tim</h4>
                                    <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <FileText class="w-5 h-5 text-blue-600 flex-shrink-0" />
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900">Surat Keputusan Tim</p>
                                                <p class="text-xs text-gray-500 truncate">{{ getFileName(skTim?.path_file) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                            <button
                                                @click="openPreview(skTim, 'skTim')"
                                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                                title="Preview"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="downloadFileFromCard(skTim.id, 'skTim')"
                                                class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                                title="Download"
                                            >
                                                <Download class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ketua -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Ketua Tim</h4>
                                    <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-200">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                                                {{ ketua?.nama?.charAt(0) || 'K' }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ ketua?.nama || '-' }}</p>
                                                <p class="text-xs text-gray-500">
                                                    NIP: {{ ketua?.nip || '-' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ ketua?.email || '-' }} • {{ ketua?.nomor_telepon || '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Anggota -->
                                <div v-if="anggota && anggota.length > 0" class="space-y-3">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Anggota Tim ({{ anggota.length }})</h4>
                                    <div class="space-y-2">
                                        <div 
                                            v-for="(member, index) in anggota" 
                                            :key="member.id"
                                            class="p-3 bg-gray-50 rounded-lg border border-gray-200"
                                        >
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-medium text-sm">
                                                    {{ index + 1 }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">{{ member.nama }}</p>
                                                    <p class="text-xs text-gray-500 truncate">
                                                        NIP: {{ member.nip || '-' }} • {{ member.email || '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <XCircle class="w-12 h-12 mb-2" />
                                <p class="text-sm">Belum ada data tim</p>
                            </div>
                        </div>
                    </div>

                    <!-- Administrasi Dasar Card (Full Width) -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-amber-500 to-orange-600">
                            <div class="flex items-center gap-3">
                                <FileCheck class="w-6 h-6 text-white" />
                                <h3 class="text-lg font-bold text-white">Administrasi Dasar</h3>
                            </div>
                        </div>
                        <div class="p-4">
                            <div v-if="hasAdministrasiDasar" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div 
                                    v-for="doc in administrasiDasar" 
                                    :key="doc.id"
                                    class="flex items-center justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg hover:shadow-md transition-shadow"
                                >
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <FileText class="w-5 h-5 text-amber-600 flex-shrink-0" />
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ doc.indikator }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ getFileName(doc.path_file) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                        <button
                                            @click="openPreview(doc, 'administrasiDasar')"
                                            class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors"
                                            title="Preview"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="downloadFileFromCard(doc.id, 'administrasiDasar')"
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
                                <p class="text-sm">Belum ada dokumen administrasi dasar</p>
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
                    <div class="flex-shrink-0 bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 md:px-8 py-4 flex items-center justify-between border-b border-emerald-800">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg md:text-xl font-bold text-white truncate">
                                {{ selectedFile?.indikator }}
                            </h2>
                            <p class="text-emerald-100 text-xs md:text-sm mt-1 truncate">
                                {{ selectedFile?.name }}
                            </p>
                        </div>
                        <button
                            @click="closePreview"
                            class="flex-shrink-0 ml-4 p-2 hover:bg-emerald-500 rounded-lg transition-colors duration-200"
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
                                <Loader class="w-12 h-12 text-emerald-500 animate-spin mx-auto mb-3" />
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

.max-h-\[500px\]::-webkit-scrollbar {
    width: 6px;
}

.max-h-\[500px\]::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.max-h-\[500px\]::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.max-h-\[500px\]::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

kbd {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    border-bottom: 2px solid currentColor;
    box-shadow: 0 2px 0 currentColor;
}
</style>