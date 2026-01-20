<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    assignedSchool: Object,
    administrasiData: Object,
});

const getFileIcon = (filename) => {
    const ext = filename?.split('.').pop()?.toLowerCase();
    if (['pdf'].includes(ext)) return '📄';
    if (['doc', 'docx'].includes(ext)) return '📝';
    if (['jpg', 'jpeg', 'png'].includes(ext)) return '🖼️';
    if (['xls', 'xlsx'].includes(ext)) return '📊';
    return '📎';
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const previewFile = (path) => {
    if (path) {
        window.open(route('administrasi.file', administrasiData.id), '_blank');
    }
};
</script>

<template>
    <Head title="Administrasi Mentor" />
    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-6">Administrasi Sekolah</h2>
                        
                        <!-- School Info -->
                        <div v-if="assignedSchool" class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="text-lg font-medium mb-2">Sekolah yang Ditugaskan</h3>
                            <p><span class="font-semibold">Nama:</span> {{ assignedSchool.school_name || assignedSchool.school_email }}</p>
                        </div>

                        <!-- No Data State -->
                        <div v-if="!administrasiData" class="p-8 text-center bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-gray-500 font-medium">Belum ada data administrasi</p>
                            <p class="text-sm text-gray-400 mt-1">Sekolah belum mengupload dokumen administrasi</p>
                        </div>

                        <!-- Administrasi Data -->
                        <div v-else class="space-y-6">
                            <!-- Document Preview Card -->
                            <div class="border rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-6 py-4 border-b">
                                    <h3 class="text-lg font-semibold text-gray-900">Dokumen Administrasi</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Diupload pada: {{ new Date(administrasiData.created_at).toLocaleDateString('id-ID', { 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) }}
                                    </p>
                                </div>
                                
                                <div class="p-6">
                                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition cursor-pointer"
                                         @click="previewFile(administrasiData.path_file)">
                                        <div class="flex items-center space-x-4">
                                            <div class="text-4xl">
                                                {{ getFileIcon(administrasiData.path_file) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ administrasiData.path_file?.split('/').pop() || 'Dokumen Administrasi' }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    {{ formatFileSize(administrasiData.file_size) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click.stop="previewFile(administrasiData.path_file)"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                👁️ Lihat
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Verification Status -->
                                    <div class="mt-4 p-4 rounded-lg" 
                                         :class="administrasiData.is_verified ? 'bg-green-50' : 'bg-yellow-50'">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium" :class="administrasiData.is_verified ? 'text-green-800' : 'text-yellow-800'">
                                                    Status: {{ administrasiData.is_verified ? 'Terverifikasi' : 'Menunggu Verifikasi' }}
                                                </p>
                                                <p v-if="administrasiData.admin_note" class="text-sm mt-1 text-gray-700">
                                                    <span class="font-semibold">Catatan:</span> {{ administrasiData.admin_note }}
                                                </p>
                                            </div>
                                            <span class="text-2xl">
                                                {{ administrasiData.is_verified ? '✅' : '⏳' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- School Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border rounded-lg p-6">
                                    <h4 class="font-semibold text-lg mb-4">Informasi Sekolah</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-600">NPSN</p>
                                            <p class="font-medium">{{ administrasiData.npsn || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Nama Sekolah</p>
                                            <p class="font-medium">{{ administrasiData.nama_sekolah || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Alamat</p>
                                            <p class="font-medium">{{ administrasiData.alamat || '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded-lg p-6">
                                    <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-600">Email</p>
                                            <p class="font-medium">{{ administrasiData.email || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">No. Telepon</p>
                                            <p class="font-medium">{{ administrasiData.no_telepon || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Kepala Sekolah</p>
                                            <p class="font-medium">{{ administrasiData.nama_kepala_sekolah || '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>