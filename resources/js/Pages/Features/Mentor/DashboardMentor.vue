<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    assignedSchool: Object,
    schoolProfile: Object,
});
</script>

<template>
    <Head title="Dashboard Mentor" />
    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-2xl font-semibold mb-6">Dashboard Mentor</h2>
                        
                        <!-- Mentor Info -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="text-lg font-medium mb-2">Informasi Mentor</h3>
                            <p><span class="font-semibold">Nama:</span> {{ user.name }}</p>
                            <p><span class="font-semibold">Email:</span> {{ user.email }}</p>
                        </div>

                        <!-- Assigned School Profile -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-4">Profil Sekolah yang Ditugaskan</h3>
                            
                            <div v-if="!assignedSchool" class="p-8 text-center bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <p class="mt-2 text-gray-500">Belum ada sekolah yang ditugaskan</p>
                            </div>

                            <div v-else-if="!schoolProfile" class="p-8 text-center bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-gray-500">Sekolah belum melengkapi data administrasi</p>
                                <p class="text-sm text-gray-400 mt-1">
                                    Sekolah yang ditugaskan: {{ assignedSchool.school_name || assignedSchool.school_email }}
                                </p>
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- School Basic Info -->
                                <div class="bg-gradient-to-br from-green-50 to-blue-50 p-6 rounded-lg shadow">
                                    <h4 class="font-semibold text-lg mb-4 text-gray-800">Informasi Sekolah</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-600">Nama Sekolah</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.nama_sekolah || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">NPSN</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.npsn || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Alamat</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.alamat || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Kelurahan</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.kelurahan || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Kecamatan</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.kecamatan || '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-lg shadow">
                                    <h4 class="font-semibold text-lg mb-4 text-gray-800">Kontak</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-600">Email</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.email || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">No. Telepon</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.no_telepon || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Kepala Sekolah</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.nama_kepala_sekolah || '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">NIP Kepala Sekolah</p>
                                            <p class="font-medium text-gray-900">{{ schoolProfile.nip_kepala_sekolah || '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assignment Info -->
                                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 rounded-lg shadow md:col-span-2">
                                    <h4 class="font-semibold text-lg mb-4 text-gray-800">Status Pendampingan</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Mulai Pendampingan</p>
                                            <p class="font-medium text-gray-900">
                                                {{ assignedSchool.assign_time_begin ? new Date(assignedSchool.assign_time_begin).toLocaleDateString('id-ID') : '-' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Selesai Pendampingan</p>
                                            <p class="font-medium text-gray-900">
                                                {{ assignedSchool.assign_time_finished ? new Date(assignedSchool.assign_time_finished).toLocaleDateString('id-ID') : 'Sedang Berlangsung' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Status</p>
                                            <span v-if="!assignedSchool.assign_time_finished" 
                                                  class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                            <span v-else 
                                                  class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Selesai
                                            </span>
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