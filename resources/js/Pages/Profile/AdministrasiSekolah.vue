<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    existingData: {
        type: Object,
        default: null,
    },
});

// Initialize form with existing data or empty
const form = useForm({
    // Identitas Sekolah
    npsn: props.existingData?.npsn || '',
    nama_sekolah: props.existingData?.nama_sekolah || '',
    jenjang: props.existingData?.jenjang || '',
    status: props.existingData?.status || '',
    provinsi: props.existingData?.provinsi || '',
    kota: props.existingData?.kota || '',
    kecamatan: props.existingData?.kecamatan || '',
    kelurahan: props.existingData?.kelurahan || '',
    alamat: props.existingData?.alamat || '',
    kode_pos: props.existingData?.kode_pos || '',

    // Koordinat
    maps_link: props.existingData?.google_maps_url || '',
    latitude: props.existingData?.latitude || '',
    longitude: props.existingData?.longitude || '',

    // Kontak
    nama_kepala_sekolah: props.existingData?.nama_kepala_sekolah || '',
    nip_kepala_sekolah: props.existingData?.nip_kepala_sekolah || '',
    telp_kepala_sekolah: props.existingData?.telp_kepala_sekolah || '',
    email: props.existingData?.email || '',
    telepon: props.existingData?.telepon || '',
    website: props.existingData?.website || '',

    // Tim Adiwiyata (akan di-populate dari existingData jika ada)
    tim_adiwiyata: props.existingData?.tim_adiwiyata || [],
});

// Maps helpers
function setExtractedCoordinates(lat, lng, source) {
    form.latitude = Number(lat).toFixed(6);
    form.longitude = Number(lng).toFixed(6);

    const info = document.getElementById('maps-extracted');
    if (info) {
        info.textContent = `Koordinat diisi dari ${source}: ${form.latitude}, ${form.longitude}`;
        info.classList.remove('text-gray-500');
        info.classList.add('text-green-600', 'font-medium');
    }
}

function fillGeolocation() {
    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung pada browser ini.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function (pos) {
            setExtractedCoordinates(pos.coords.latitude, pos.coords.longitude, 'Geolocation');
        },
        function (err) {
            alert('Gagal mengambil lokasi: ' + (err.message || 'Permission denied'));
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

function parseGoogleMapsUrl(url) {
    if (!url) return null;
    let m = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
    m = url.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
    m = url.match(/3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
    if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
    return null;
}

function parseMapsLink() {
    const url = form.maps_link.trim();
    if (!url) {
        alert('Tempel tautan Google Maps terlebih dahulu pada kolom tautan.');
        return;
    }
    const coords = parseGoogleMapsUrl(url);
    if (coords) {
        setExtractedCoordinates(coords.lat, coords.lng, 'tautan Google Maps');
    } else {
        alert('Tidak dapat menemukan koordinat dalam tautan. Gunakan opsi Bagikan → Salin tautan pada Google Maps.');
    }
}

// Dynamic anggota tim
const anggotaList = ref([]);
let anggotaCounter = 0;

function addAnggotaRow() {
    anggotaCounter++;
    anggotaList.value.push({
        id: anggotaCounter,
        nama: '',
        peran: '',
    });
}

function removeAnggotaRow(id) {
    const index = anggotaList.value.findIndex((item) => item.id === id);
    if (index > -1) {
        anggotaList.value.splice(index, 1);
    }
}

// Submit form
function submitForm() {
    // Sync anggotaList ke form.tim_adiwiyata
    form.tim_adiwiyata = anggotaList.value.map((anggota) => ({
        nama: anggota.nama,
        peran: anggota.peran,
    }));

    form.post(route('administrasi-store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
}

// Initialize anggotaList dari existing data
onMounted(() => {
    if (props.existingData?.tim_adiwiyata && Array.isArray(props.existingData.tim_adiwiyata)) {
        anggotaCounter = 0;
        anggotaList.value = props.existingData.tim_adiwiyata.map((anggota) => {
            anggotaCounter++;
            return {
                id: anggotaCounter,
                nama: anggota.nama || '',
                peran: anggota.peran || '',
            };
        });
    }
});
</script>

<template>
    <MainLayout>
        <Head title="Administrasi Sekolah" />

        <Header
            title="Administrasi Sekolah"
            description="Lengkapi data dan profil sekolah Anda"
            color="green"
        />

        <main class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12">
            <div class="container mx-auto px-4 max-w-4xl">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b">
                        Identitas Sekolah
                    </h2>

                    <!-- Form -->
                    <form @submit.prevent="submitForm" class="space-y-8">
                        <!-- A1. Informasi Sekolah -->
                        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Sekolah</h3>

                            <!-- NPSN -->
                            <div class="max-w-2xl">
                                <label for="npsn" class="block text-sm font-medium text-gray-700 mb-1">
                                    NPSN <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="npsn"
                                    v-model="form.npsn"
                                    type="text"
                                    required
                                    placeholder="Contoh: 12345678"
                                    maxlength="8"
                                    pattern="[0-9]{8}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                    :class="{ 'border-red-500': form.errors.npsn }"
                                />
                                <p v-if="form.errors.npsn" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.npsn }}
                                </p>
                                <p class="text-xs text-gray-500 mt-2">
                                    Masukkan Nomor Pokok Sekolah Nasional sesuai Dapodik (8 digit)
                                </p>
                            </div>

                            <!-- Nama Sekolah -->
                            <div>
                                <label for="nama-sekolah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="nama-sekolah"
                                    v-model="form.nama_sekolah"
                                    type="text"
                                    required
                                    placeholder="Nama lengkap sesuai Dapodik"
                                    class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                    :class="{ 'border-red-500': form.errors.nama_sekolah }"
                                />
                                <p v-if="form.errors.nama_sekolah" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.nama_sekolah }}
                                </p>
                            </div>

                            <!-- Provinsi & Kota -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">
                                        Provinsi <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="provinsi"
                                        v-model="form.provinsi"
                                        type="text"
                                        required
                                        placeholder="Nama Provinsi"
                                        class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                    />
                                </div>
                                <div>
                                    <label for="kota" class="block text-sm font-medium text-gray-700 mb-1">
                                        Kabupaten / Kota <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="kota"
                                        v-model="form.kota"
                                        type="text"
                                        required
                                        placeholder="Nama Kabupaten / Kota"
                                        class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                    />
                                </div>
                            </div>

                            <!-- Kecamatan & Kelurahan -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">
                                        Kecamatan <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="kecamatan"
                                        v-model="form.kecamatan"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                    />
                                </div>
                                <div>
                                    <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">
                                        Kelurahan <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="kelurahan"
                                        v-model="form.kelurahan"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                    />
                                </div>
                            </div>

                            <!-- Alamat Lengkap -->
                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="alamat"
                                    v-model="form.alamat"
                                    rows="3"
                                    required
                                    placeholder="Contoh: Jl. Pendidikan No. 123"
                                    class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                ></textarea>
                            </div>

                            <!-- Google Maps -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tautan Google Maps (opsional)
                                </label>
                                <div class="space-y-2">
                                    <input
                                        id="maps_link"
                                        v-model="form.maps_link"
                                        type="url"
                                        placeholder="https://www.google.com/maps/place/..."
                                        class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    />
                                    <div class="flex gap-2 items-center">
                                        <button
                                            type="button"
                                            @click="parseMapsLink"
                                            class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition"
                                        >
                                            Ambil Koordinat dari Tautan
                                        </button>
                                        <button
                                            type="button"
                                            @click="fillGeolocation"
                                            class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition"
                                        >
                                            Lokasi Saat Ini
                                        </button>
                                    </div>
                                    <p id="maps-extracted" class="text-xs text-gray-500 mt-1"></p>
                                </div>
                            </div>

                            <!-- Kepala Sekolah -->
                            <div>
                                <label for="kepala-sekolah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Kepala Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="kepala-sekolah"
                                    v-model="form.nama_kepala_sekolah"
                                    type="text"
                                    required
                                    placeholder="Nama lengkap"
                                    class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Kontak -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Kontak <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        placeholder="email@sekolah.sch.id"
                                        class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    />
                                </div>
                                <div>
                                    <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">
                                        Telepon Kontak <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="telepon"
                                        v-model="form.telepon"
                                        type="tel"
                                        required
                                        placeholder="081234567890"
                                        class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- A2. Tim Adiwiyata -->
                        <h2 class="text-2xl font-bold text-green-800 mb-4 pb-4 border-b">
                            Tim Adiwiyata Sekolah
                        </h2>
                        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
                            <div class="flex justify-between items-center">
                                <h4 class="font-medium text-gray-700">Anggota Tim</h4>
                                <button
                                    type="button"
                                    @click="addAnggotaRow"
                                    class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition"
                                >
                                    + Tambah Anggota
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="anggota in anggotaList"
                                    :key="anggota.id"
                                    class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-lg border"
                                >
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1"
                                            >Nama Lengkap</label
                                        >
                                        <input
                                            v-model="anggota.nama"
                                            type="text"
                                            class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1"
                                            >Peran/Tanggung Jawab</label
                                        >
                                        <input
                                            v-model="anggota.peran"
                                            type="text"
                                            class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-colors"
                                        />
                                    </div>

                                    <div class="flex items-end">
                                        <button
                                            type="button"
                                            @click="removeAnggotaRow(anggota.id)"
                                            class="px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="pt-6 border-t">
                            <div class="flex flex-wrap gap-3 justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-semibold"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </MainLayout>
</template>
