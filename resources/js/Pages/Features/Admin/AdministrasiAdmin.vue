<script setup>
import { ref, computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { 
    FileText, 
    Eye, 
    Download, 
    CheckCircle, 
    XCircle, 
    Clock, 
    User, 
    School,
    ChevronDown,
    ChevronUp,
    Search
} from 'lucide-vue-next';

// Props from backend
const props = defineProps({
    allUploadedFiles: {
        type: Object,
        default: () => ({})
    }
});

// State
const searchQuery = ref('');
const expandedUsers = ref({});
const showPreview = ref(false);
const previewUrl = ref('');
const selectedFile = ref(null);

// Labels for indikator
const administrasiLabels = {
    'a1_sk': 'A1 - SK Tim Adiwiyata',
    'a2_tim': 'A2 - Struktur Tim Adiwiyata',
    'a3_rencana': 'A3 - Rencana Kegiatan',
    'a4_anggaran': 'A4 - Anggaran Program',
    'a5_pblhs': 'A5 - Rencana & Evaluasi PBLHS',
    'a6_self_assessment': 'A6 - Bukti Self Assessment',
    'a7_pendampingan': 'A7 - Kebutuhan Pendampingan',
    'a8_pernyataan': 'A8 - Pernyataan & Persetujuan'
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

// Get status icon
const getStatusIcon = (status) => {
    switch(status) {
        case 'approved': return CheckCircle;
        case 'rejected': return XCircle;
        default: return Clock;
    }
};

// Toggle expanded user
const toggleUser = (userId) => {
    expandedUsers.value[userId] = !expandedUsers.value[userId];
};

// Filter users based on search
const filteredUsers = computed(() => {
    if (!props.allUploadedFiles) return {};
    
    if (!searchQuery.value) return props.allUploadedFiles;
    
    const filtered = {};
    const query = searchQuery.value.toLowerCase();
    
    Object.entries(props.allUploadedFiles).forEach(([userId, files]) => {
        const userName = files[0]?.user?.name?.toLowerCase() || '';
        if (userName.includes(query)) {
            filtered[userId] = files;
        }
    });
    
    return filtered;
});

// Count total users
const totalUsers = computed(() => Object.keys(props.allUploadedFiles || {}).length);

// Count total files
const totalFiles = computed(() => {
    let count = 0;
    Object.values(props.allUploadedFiles || {}).forEach(files => {
        count += files.length;
    });
    return count;
});

// Preview file
const previewFile = (file) => {
    selectedFile.value = file;
    previewUrl.value = `/api/file-upload/${file.id}/preview`;
    showPreview.value = true;
};

// Download file
const downloadFile = (fileId) => {
    window.location.href = `/api/file-upload/${fileId}/download`;
};

// Close preview
const closePreview = () => {
    showPreview.value = false;
    previewUrl.value = '';
    selectedFile.value = null;
};
</script>

<template>
    <MainLayout>
        <div class="p-6 max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Administrasi Sekolah
                </h1>
                <p class="text-gray-600">
                    Lihat dan review dokumen administrasi yang diupload oleh sekolah
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <School class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Sekolah</p>
                            <p class="text-2xl font-bold text-gray-900">{{ totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <FileText class="w-6 h-6 text-green-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total File</p>
                            <p class="text-2xl font-bold text-gray-900">{{ totalFiles }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <Clock class="w-6 h-6 text-yellow-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Menunggu Review</p>
                            <p class="text-2xl font-bold text-gray-900">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-6">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama sekolah..."
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                    />
                </div>
            </div>

            <!-- No Data -->
            <div v-if="Object.keys(filteredUsers).length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <FileText class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Data</h3>
                <p class="text-gray-500">Belum ada file administrasi yang diupload oleh sekolah</p>
            </div>

            <!-- User List -->
            <div v-else class="space-y-4">
                <div 
                    v-for="(files, userId) in filteredUsers" 
                    :key="userId"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                >
                    <!-- User Header -->
                    <button
                        @click="toggleUser(userId)"
                        class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ files[0]?.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                            </div>
                            <div class="text-left">
                                <h3 class="font-semibold text-gray-900">{{ files[0]?.user?.name || 'Unknown User' }}</h3>
                                <p class="text-sm text-gray-500">{{ files.length }} file diupload</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                {{ files.length }}/8 dokumen
                            </span>
                            <ChevronDown v-if="!expandedUsers[userId]" class="w-5 h-5 text-gray-400" />
                            <ChevronUp v-else class="w-5 h-5 text-gray-400" />
                        </div>
                    </button>

                    <!-- Files List -->
                    <transition
                        enter-active-class="transition-all duration-300"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-[1000px]"
                        leave-active-class="transition-all duration-300"
                        leave-from-class="opacity-100 max-h-[1000px]"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-if="expandedUsers[userId]" class="border-t border-gray-200">
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div 
                                    v-for="file in files" 
                                    :key="file.id"
                                    class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition"
                                >
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                <FileText class="w-5 h-5 text-red-600" />
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ getAdministrasiLabel(file.indikator) }}</p>
                                                <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ file.original_filename }}</p>
                                            </div>
                                        </div>
                                        <span 
                                            class="text-xs px-2 py-1 rounded-full"
                                            :class="getStatusBadgeClass(file.status)"
                                        >
                                            {{ getStatusText(file.status) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                        <span>{{ formatFileSize(file.file_size) }}</span>
                                        <span>{{ new Date(file.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="previewFile(file)"
                                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition text-sm font-medium"
                                        >
                                            <Eye class="w-4 h-4" />
                                            Preview
                                        </button>
                                        <button
                                            @click="downloadFile(file.id)"
                                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition text-sm font-medium"
                                        >
                                            <Download class="w-4 h-4" />
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
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
                            <p class="text-sm text-gray-500">{{ selectedFile?.original_filename }}</p>
                        </div>
                        <button
                            @click="closePreview"
                            class="p-2 hover:bg-gray-200 rounded-lg transition"
                        >
                            <XCircle class="w-6 h-6 text-gray-500" />
                        </button>
                    </div>

                    <!-- PDF Viewer -->
                    <div class="h-[70vh]">
                        <iframe
                            :src="previewUrl"
                            class="w-full h-full"
                            frameborder="0"
                        ></iframe>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 bg-gray-50">
                        <button
                            @click="closePreview"
                            class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-lg transition"
                        >
                            Tutup
                        </button>
                        <button
                            v-if="selectedFile"
                            @click="downloadFile(selectedFile.id)"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg transition"
                        >
                            <Download class="w-4 h-4" />
                            Download
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </MainLayout>
</template>
