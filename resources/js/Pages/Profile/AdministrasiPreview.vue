<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    administrasi: {
        type: Object,
        required: true,
    },
});

// Computed
const isVerified = computed(() => props.administrasi.verified);
const hasEditRequest = computed(() => props.administrasi.edit_requested);

// Data untuk tabel 2 kolom
const identitasSekolah = computed(() => [
    { label: 'NPSN', value: props.administrasi.npsn },
    { label: 'Nama Sekolah', value: props.administrasi.nama_sekolah },
    { label: 'Provinsi', value: props.administrasi.provinsi },
    { label: 'Kabupaten/Kota', value: props.administrasi.kota },
    { label: 'Kecamatan', value: props.administrasi.kecamatan },
    { label: 'Kelurahan', value: props.administrasi.kelurahan },
    { label: 'Alamat Lengkap', value: props.administrasi.alamat },
    { label: 'Kode Pos', value: props.administrasi.kode_pos || '-' },
    { label: 'Email', value: props.administrasi.email || '-' },
    { label: 'Telepon', value: props.administrasi.telepon || '-' },
    { label: 'Website', value: props.administrasi.website || '-' },
]);

const kepalaSekolah = computed(() => [
    { label: 'Nama Kepala Sekolah', value: props.administrasi.nama_kepala_sekolah },
    { label: 'NIP', value: props.administrasi.nip_kepala_sekolah || '-' },
    { label: 'Telepon', value: props.administrasi.telp_kepala_sekolah || '-' },
]);

const ketuaTim = computed(() => {
    if (!props.administrasi.ketua) return [];
    return [
        { label: 'Nama Ketua', value: props.administrasi.ketua.nama },
        { label: 'NIP', value: props.administrasi.ketua.nip || '-' },
        { label: 'Email', value: props.administrasi.ketua.email || '-' },
        { label: 'Nomor Telepon', value: props.administrasi.ketua.nomor_telepon || '-' },
    ];
});

const anggotaTim = computed(() => {
    if (!props.administrasi.ketua?.anggota) return [];
    return props.administrasi.ketua.anggota;
});
</script>

<template>
    <MainLayout>
        <Head title="Preview - Administrasi Sekolah" />

        <Header
            title="Preview Data Administrasi Sekolah"
            :description="`Data dari ${administrasi.user?.name || 'User'}`"
            color="green"
        />

        <main class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12">
            <div class="container mx-auto px-4 max-w-5xl">
                <!-- Status & Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Status Badges -->
                        <div class="flex items-center gap-3">
                            <span
                                v-if="isVerified"
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800"
                            >
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800"
                            >
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Unverified
                            </span>

                            <span
                                v-if="hasEditRequest"
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800"
                            >
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                                Edit Request Pending
                            </span>
                        </div>

                        <!-- Action Button -->
                        <Link
                            :href="route('administrasi-sekolah')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                        >
                            ← Kembali ke Form
                        </Link>
                    </div>

                    <!-- Verified Info -->
                    <div v-if="isVerified && administrasi.verified_at" class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Diverifikasi pada:</span>
                            {{ new Date(administrasi.verified_at).toLocaleString('id-ID', {
                                dateStyle: 'long',
                                timeStyle: 'short'
                            }) }}
                        </p>
                        <p v-if="administrasi.verified_by_admin" class="text-sm text-gray-600">
                            <span class="font-medium">Oleh:</span>
                            {{ administrasi.verified_by_admin?.name || 'Admin' }}
                        </p>
                    </div>

                    <!-- Admin Note -->
                    <div v-if="administrasi.admin_note" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm font-semibold text-blue-900 mb-1">Catatan Admin:</p>
                        <p class="text-sm text-blue-800 whitespace-pre-line">{{ administrasi.admin_note }}</p>
                    </div>

                    <!-- Edit Request Info -->
                    <div v-if="hasEditRequest" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm font-semibold text-yellow-900 mb-1">Alasan Permintaan Edit:</p>
                        <p class="text-sm text-yellow-800 whitespace-pre-line">
                            {{ administrasi.edit_request_reason || 'Tidak ada alasan yang diberikan' }}
                        </p>
                        <p class="text-xs text-yellow-600 mt-2">
                            Diajukan pada: {{ new Date(administrasi.edit_requested_at).toLocaleString('id-ID') }}
                        </p>
                    </div>
                </div>

                <!-- Identitas Sekolah -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-green-800 mb-6 pb-4 border-b">
                        Identitas Sekolah
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="(item, index) in identitasSekolah"
                                    :key="index"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td class="py-4 px-4 font-semibold text-gray-700 bg-gray-50 w-1/3">
                                        {{ item.label }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-900">
                                        {{ item.value }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Google Maps Link -->
                    <div v-if="administrasi.google_maps_url" class="mt-6 pt-6 border-t">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Lokasi di Maps</h3>
                        <a
                            :href="administrasi.google_maps_url"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>

                <!-- Kepala Sekolah -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-green-800 mb-6 pb-4 border-b">
                        Data Kepala Sekolah
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="(item, index) in kepalaSekolah"
                                    :key="index"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td class="py-4 px-4 font-semibold text-gray-700 bg-gray-50 w-1/3">
                                        {{ item.label }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-900">
                                        {{ item.value }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ketua Tim Greenedu -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-green-800 mb-6 pb-4 border-b">
                        Ketua Tim Greenedu
                    </h2>

                    <div v-if="ketuaTim.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="(item, index) in ketuaTim"
                                    :key="index"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td class="py-4 px-4 font-semibold text-gray-700 bg-gray-50 w-1/3">
                                        {{ item.label }}
                                    </td>
                                    <td class="py-4 px-4 text-gray-900">
                                        {{ item.value }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-gray-500 italic">Data ketua belum diisi</p>
                </div>

                <!-- Anggota Tim -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-green-800 mb-6 pb-4 border-b">
                        Daftar Anggota Tim ({{ anggotaTim.length }})
                    </h2>

                    <div v-if="anggotaTim.length > 0" class="space-y-4">
                        <div
                            v-for="(anggota, index) in anggotaTim"
                            :key="index"
                            class="p-4 bg-gray-50 rounded-lg border border-gray-200"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Nama</p>
                                    <p class="font-semibold text-gray-900">{{ anggota.nama }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">NIP</p>
                                    <p class="text-gray-900">{{ anggota.nip || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Email</p>
                                    <p class="text-gray-900">{{ anggota.email || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                                    <p class="text-gray-900">{{ anggota.nomor_telepon || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 italic">Belum ada anggota tim</p>
                </div>
            </div>
        </main>
    </MainLayout>
</template>
