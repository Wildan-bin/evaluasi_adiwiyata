<script setup>
import { ref, computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { router } from '@inertiajs/vue3';
import { 
    FileText, 
    Eye, 
    CheckCircle, 
    XCircle, 
    Clock, 
    School,
    TrendingUp,
    AlertTriangle,
    ChevronRight,
    RefreshCw,
    Check,
    X
} from 'lucide-vue-next';
import axios from 'axios';

// Props from backend
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_files: 0,
            pending: 0,
            approved: 0,
            rejected: 0,
            total_schools: 0
        })
    },
    pending_files: {
        type: Array,
        default: () => []
    },
    recent_reviewed: {
        type: Array,
        default: () => []
    }
});

// State
const isReviewing = ref(false);
const reviewingId = ref(null);
const showPreview = ref(false);
const previewUrl = ref('');
const selectedFile = ref(null);

// Labels for indikator
const administrasiLabels = {
    'a1_sk': 'A1 - SK Tim Adiwiyata',
    'a2_tim': 'A2 - Struktur Tim',
    'a3_rencana': 'A3 - Rencana Kegiatan',
    'a4_anggaran': 'A4 - Anggaran',
    'a5_pblhs': 'A5 - PBLHS',
    'a6_self_assessment': 'A6 - Self Assessment',
    'a7_pendampingan': 'A7 - Pendampingan',
    'a8_pernyataan': 'A8 - Pernyataan'
};

const getAdministrasiLabel = (indikator) => {
    return administrasiLabels[indikator] || indikator;
};

// Format file size
const formatFileSize = (size) => {
    if (!size) return '0 B';
    if (typeof size === 'string') return size;
    const bytes = Number(size);
    if (isNaN(bytes)) return '0 B';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${sizes[i]}`;
};

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Preview file
const previewFile = (file) => {
    selectedFile.value = file;
    previewUrl.value = `/api/file-upload/${file.id}/preview`;
    showPreview.value = true;
};

// Close preview
const closePreview = () => {
    showPreview.value = false;
    previewUrl.value = '';
    selectedFile.value = null;
};

// Approve file
const approveFile = async (fileId) => {
    isReviewing.value = true;
    reviewingId.value = fileId;
    
    try {
        await axios.post(`/api/file-upload/${fileId}/review`, {
            status: 'approved',
            notes: ''
        });
        
        // Refresh page
        router.reload();
    } catch (error) {
        console.error('Error approving file:', error);
        alert('Gagal menyetujui file');
    } finally {
        isReviewing.value = false;
        reviewingId.value = null;
    }
};

// Reject file
const rejectFile = async (fileId) => {
    const reason = prompt('Masukkan alasan penolakan:');
    if (!reason) return;
    
    isReviewing.value = true;
    reviewingId.value = fileId;
    
    try {
        await axios.post(`/api/file-upload/${fileId}/review`, {
            status: 'rejected',
            notes: reason
        });
        
        // Refresh page
        router.reload();
    } catch (error) {
        console.error('Error rejecting file:', error);
        alert('Gagal menolak file');
    } finally {
        isReviewing.value = false;
        reviewingId.value = null;
    }
};

// Go to administrasi page
const goToAdministrasi = () => {
    router.visit('/administrasi-sekolah');
};

// Refresh data
const refreshData = () => {
    router.reload();
};
</script>

<template>
    <MainLayout>
        <div class="p-6 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Dashboard Review
                    </h1>
                    <p class="text-gray-600">
                        Pantau dan review submission dokumen dari sekolah
                    </p>
                </div>
                <button
                    @click="refreshData"
                    class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition"
                >
                    <RefreshCw class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <!-- Total Sekolah -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Total Sekolah</p>
                            <p class="text-3xl font-bold mt-1">{{ stats.total_schools }}</p>
                        </div>
                        <School class="w-10 h-10 text-blue-200" />
                    </div>
                </div>

                <!-- Total File -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Total File</p>
                            <p class="text-3xl font-bold mt-1">{{ stats.total_files }}</p>
                        </div>
                        <FileText class="w-10 h-10 text-purple-200" />
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm">Menunggu Review</p>
                            <p class="text-3xl font-bold mt-1">{{ stats.pending }}</p>
                        </div>
                        <Clock class="w-10 h-10 text-yellow-200" />
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Disetujui</p>
                            <p class="text-3xl font-bold mt-1">{{ stats.approved }}</p>
                        </div>
                        <CheckCircle class="w-10 h-10 text-green-200" />
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm">Ditolak</p>
                            <p class="text-3xl font-bold mt-1">{{ stats.rejected }}</p>
                        </div>
                        <XCircle class="w-10 h-10 text-red-200" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pending Files - Quick Review -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <AlertTriangle class="w-6 h-6 text-white" />
                            <h2 class="text-lg font-bold text-white">Perlu Review</h2>
                        </div>
                        <span class="bg-white/20 text-white text-sm px-3 py-1 rounded-full">
                            {{ pending_files.length }} file
                        </span>
                    </div>
                    
                    <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                        <div v-if="pending_files.length === 0" class="p-8 text-center text-gray-500">
                            <CheckCircle class="w-12 h-12 text-green-300 mx-auto mb-3" />
                            <p class="font-medium">Semua file sudah direview! 🎉</p>
                        </div>
                        
                        <div 
                            v-for="file in pending_files" 
                            :key="file.id"
                            class="p-4 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <FileText class="w-5 h-5 text-red-600" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 truncate">{{ getAdministrasiLabel(file.indikator) }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ file.user_name }} • {{ formatFileSize(file.file_size) }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ formatDate(file.created_at) }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <!-- Preview -->
                                    <button
                                        @click="previewFile(file)"
                                        class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition"
                                        title="Preview"
                                    >
                                        <Eye class="w-5 h-5" />
                                    </button>
                                    
                                    <!-- Approve -->
                                    <button
                                        @click="approveFile(file.id)"
                                        :disabled="isReviewing && reviewingId === file.id"
                                        class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition disabled:opacity-50"
                                        title="Setujui"
                                    >
                                        <Check class="w-5 h-5" />
                                    </button>
                                    
                                    <!-- Reject -->
                                    <button
                                        @click="rejectFile(file.id)"
                                        :disabled="isReviewing && reviewingId === file.id"
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition disabled:opacity-50"
                                        title="Tolak"
                                    >
                                        <X class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View All -->
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                        <button
                            @click="goToAdministrasi"
                            class="w-full flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition"
                        >
                            Lihat Semua File
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Recent Reviewed -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700">
                        <div class="flex items-center gap-3">
                            <TrendingUp class="w-6 h-6 text-white" />
                            <h2 class="text-lg font-bold text-white">Review Terbaru</h2>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                        <div v-if="recent_reviewed.length === 0" class="p-8 text-center text-gray-500">
                            <Clock class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p>Belum ada file yang direview</p>
                        </div>
                        
                        <div 
                            v-for="file in recent_reviewed" 
                            :key="file.id"
                            class="p-4 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-start gap-3">
                                <div 
                                    class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                    :class="file.status === 'approved' ? 'bg-green-100' : 'bg-red-100'"
                                >
                                    <CheckCircle v-if="file.status === 'approved'" class="w-4 h-4 text-green-600" />
                                    <XCircle v-else class="w-4 h-4 text-red-600" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ getAdministrasiLabel(file.indikator) }}</p>
                                    <p class="text-xs text-gray-500">{{ file.user_name }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ formatDate(file.updated_at) }}</p>
                                </div>
                                <span 
                                    class="text-xs px-2 py-1 rounded-full flex-shrink-0"
                                    :class="file.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                >
                                    {{ file.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showPreview" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ selectedFile ? getAdministrasiLabel(selectedFile.indikator) : 'Preview' }}</h3>
                            <p class="text-sm text-gray-500">{{ selectedFile?.user_name }} • {{ selectedFile?.original_filename }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Quick Actions in Modal -->
                            <button
                                v-if="selectedFile"
                                @click="approveFile(selectedFile.id); closePreview();"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg transition"
                            >
                                <Check class="w-4 h-4" />
                                Setujui
                            </button>
                            <button
                                v-if="selectedFile"
                                @click="rejectFile(selectedFile.id); closePreview();"
                                class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg transition"
                            >
                                <X class="w-4 h-4" />
                                Tolak
                            </button>
                            <button
                                @click="closePreview"
                                class="p-2 hover:bg-gray-200 rounded-lg transition"
                            >
                                <XCircle class="w-6 h-6 text-gray-500" />
                            </button>
                        </div>
                    </div>

                    <!-- PDF Viewer -->
                    <div class="h-[70vh]">
                        <iframe
                            :src="previewUrl"
                            class="w-full h-full"
                            frameborder="0"
                        ></iframe>
                    </div>
                </div>
            </div>
        </transition>
    </MainLayout>
</template>
