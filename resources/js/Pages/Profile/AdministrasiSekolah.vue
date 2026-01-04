<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

// Props
const props = defineProps({
  previewMode: {
    type: Boolean,
    default: false
  },
  administrasiData: {
    type: Object,
    default: null
  }
});

// Check if current user is admin
const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

// --- Maps helpers ---
function setExtractedCoordinates(lat, lng, source) {
  const latVal = Number(lat).toFixed(6);
  const lngVal = Number(lng).toFixed(6);
  const latEl = document.getElementById('lat');
  const lngEl = document.getElementById('lng');
  if (latEl) latEl.value = latVal;
  if (lngEl) lngEl.value = lngVal;
  const info = document.getElementById('maps-extracted');
  if (info) info.textContent = `Koordinat diisi dari ${source}: ${latVal}, ${lngVal}`;
}

function fillGeolocation() {
  if (!navigator.geolocation) {
    alert('Geolocation tidak didukung pada browser ini.');
    return;
  }
  navigator.geolocation.getCurrentPosition(function(pos) {
    setExtractedCoordinates(pos.coords.latitude, pos.coords.longitude, 'Geolocation');
  }, function(err) {
    alert('Gagal mengambil lokasi: ' + (err.message || 'Permission denied'));
  }, { enableHighAccuracy: true, timeout: 10000 });
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

// --- Dynamic anggota rows ---
let anggotaCounter = 0;
function addAnggotaRow() {
  const container = document.getElementById('anggota-container');
  if (!container) return;
  anggotaCounter++;

  const row = document.createElement('div');
  row.className = 'grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-lg border anggota-row';
  row.id = `anggota-${anggotaCounter}`;

  const col1 = document.createElement('div');
  col1.innerHTML = `
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
    <input type="text" name="anggota_nama[]" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
  `;

  const col2 = document.createElement('div');
  col2.innerHTML = `
    <label class="block text-sm font-medium text-gray-700 mb-1">Peran/Tanggung Jawab</label>
    <input type="text" name="anggota_peran[]" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
  `;

  const col3 = document.createElement('div');
  col3.className = 'flex items-end';
  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none';
  btn.textContent = 'Hapus';
  btn.addEventListener('click', () => removeAnggotaRow(anggotaCounter));
  col3.appendChild(btn);

  row.appendChild(col1);
  row.appendChild(col2);
  row.appendChild(col3);
  container.appendChild(row);
}

function removeAnggotaRow(id) {
  const row = document.getElementById(`anggota-${id}`);
  if (row) row.remove();
}

// --- Form save draft handling (wire up on mounted) ---
onMounted(() => {
  const parseBtn = document.getElementById('parse-maps-btn');
  if (parseBtn) {
    parseBtn.addEventListener('click', function() {
      const url = (document.getElementById('maps_link') || {}).value || '';
      if (!url) {
        alert('Tempel tautan Google Maps terlebih dahulu pada kolom tautan.');
        return;
      }
      const coords = parseGoogleMapsUrl(url.trim());
      if (coords) {
        setExtractedCoordinates(coords.lat, coords.lng, 'tautan Google Maps');
      } else {
        alert('Tidak dapat menemukan koordinat dalam tautan. Gunakan opsi Bagikan → Salin tautan pada Google Maps atau tempel tautan yang mengandung @lat,lng atau !3d..!4d..');
      }
    });
  }

  const form = document.querySelector('form');
  const saveBtn = document.getElementById('save-draft-btn');
  const saveAction = document.getElementById('save-action');
  if (saveBtn && form && saveAction) {
    saveBtn.addEventListener('click', function() {
      saveAction.value = 'draft';
      form.noValidate = true;
      form.submit();
    });

    form.addEventListener('submit', function(e) {
      if (saveAction.value === 'draft') {
        return;
      }
      form.noValidate = false;
    });
  }
});
</script>

<template>

    <MainLayout>
        <Head title="Dashboard" />
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <Header
            :title="previewMode ? 'Preview Administrasi Sekolah' : 'Administrasi Sekolah'"
            :description="previewMode ? 'Mode Preview - Hanya untuk Admin' : 'Kelola dan monitor program lingkungan sekolah Anda'"
            color="green"
        />

        <!-- Preview Mode Badge -->
        <div v-if="previewMode && isAdmin" class="container mx-auto px-4 pt-20">
          <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded">
            <p class="font-bold">Mode Preview</p>
            <p>Anda sedang melihat data administrasi sekolah dalam mode read-only.</p>
          </div>
        </div>
<!-- Progress Bar Fixed at Top -->
<div class="fixed top-0 left-0 right-0 bg-white shadow-md z-50">
    <div class="container mx-auto px-4 py-2">
        <div class="flex items-center justify-between">
            <div class="w-full max-w-xl">
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span id="completion-percentage" class="text-xs font-semibold inline-block text-green-800">
                                0% Selesai
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-green-600">
                                Minimum 80%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                        <div id="progress-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500 w-0 transition-all duration-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12 mt-16">
  <div class="container mx-auto px-4 max-w-4xl">
    <!-- Pengajuan Penilaian Section -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b">Identitas Sekolah</h2>

      <!-- Form Pengajuan -->
      <form class="space-y-8" :class="{ 'pointer-events-none opacity-75': previewMode }">
        <!-- Informasi Sekolah (Identitas Sekolah) -->
        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Sekolah</h3>

          <!-- NPSN (wajib) -->
          <div class="max-w-2xl">
            <label for="npsn" class="block text-sm font-medium text-gray-700 mb-1">NPSN <span class="text-red-500">*</span></label>
            <input id="npsn"
                   name="npsn"
                   type="text"
                   :value="administrasiData?.npsn || ''"
                   required
                   :readonly="previewMode"
                   placeholder="Contoh: 12345678"
                   :class="previewMode ? 'bg-gray-100' : ''"
                   class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                   maxlength="8"
                   pattern="[0-9]{8}"
            />
            <p class="text-xs text-gray-500 mt-2">Masukkan Nomor Pokok Sekolah Nasional sesuai Dapodik (8 digit)</p>
          </div>

          <!-- Nama Sekolah (wajib) -->
          <div>
            <label for="nama-sekolah" class="block text-sm font-medium text-gray-700 mb-1">Nama Sekolah <span class="text-red-500">*</span></label>
            <input id="nama-sekolah"
                   name="nama_sekolah"
                   type="text"
                   :value="administrasiData?.nama_sekolah || ''"
                   required
                   :readonly="previewMode"
                   placeholder="Nama lengkap sesuai Dapodik"
                   :class="previewMode ? 'bg-gray-100' : ''"
                   class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" />
          </div>

          <!-- Jenjang (wajib) & Status (wajib) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="jenjang" class="block text-sm font-medium text-gray-700 mb-1">Jenjang <span class="text-red-500">*</span></label>
              <select id="jenjang"
                      name="jenjang"
                      :value="administrasiData?.jenjang || ''"
                      :disabled="previewMode"
                      required
                      :class="previewMode ? 'bg-gray-100' : 'bg-white'"
                      class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                <option value="">Pilih Jenjang</option>
                <option>SD/MI</option>
                <option>SMP/MTs</option>
                <option>SMA/MA</option>
                <option>SMK</option>
                <option>SLB</option>
              </select>
            </div>

            <div>
              <label for="status-sekolah" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
              <select id="status-sekolah"
                      name="status"
                      :value="administrasiData?.status || ''"
                      :disabled="previewMode"
                      required
                      :class="previewMode ? 'bg-gray-100' : 'bg-white'"
                      class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                <option value="">Pilih Status</option>
                <option>Negeri</option>
                <option>Swasta</option>
              </select>
            </div>
          </div>

          <!-- Provinsi & Kabupaten/Kota (wajib) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
              <input id="provinsi"
                     name="provinsi"
                     type="text"
                     :value="administrasiData?.provinsi || ''"
                     :readonly="previewMode"
                     required
                     placeholder="Nama Provinsi"
                     :class="previewMode ? 'bg-gray-100' : ''"
                     class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" />
            </div>
            <div>
              <label for="kabkota" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten / Kota <span class="text-red-500">*</span></label>
              <input id="kabkota"
                     name="kabkota"
                     type="text"
                     :value="administrasiData?.kabkota || ''"
                     :readonly="previewMode"
                     required
                     placeholder="Nama Kabupaten / Kota"
                     :class="previewMode ? 'bg-gray-100' : ''"
                     class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" />
            </div>
          </div>

          <!-- Alamat Lengkap -->
          <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea id="alamat"
                      name="alamat"
                      rows="3"
                      :value="administrasiData?.alamat || ''"
                      :readonly="previewMode"
                      required
                      :class="previewMode ? 'bg-gray-100' : ''"
                      class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                      placeholder="Contoh: Jl. Pendidikan No. 123, Desa Sejahtera, Kec. Cerdas, 12345"></textarea>
            <p class="text-xs text-gray-500 mt-2">Masukkan alamat lengkap termasuk nama jalan, desa/kelurahan, kecamatan, dan kode pos</p>
          </div>

          <!-- Tautan Google Maps (opsional) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tautan Google Maps (opsional)</label>
            <div class="space-y-2">
              <input id="maps_link" name="maps_link" type="url" :value="administrasiData?.maps_link || ''" :readonly="previewMode" placeholder="https://www.google.com/maps/place/..." :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
              <div v-if="!previewMode" class="flex gap-2 items-center">
                <button type="button" id="parse-maps-btn" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md">Ambil Koordinat dari Tautan</button>
                <button type="button" onclick="fillGeolocation()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md">Lokasi Saat Ini</button>
              </div>
              <p id="maps-extracted" class="text-xs text-gray-500 mt-1"></p>
              <!-- Hidden latitude/longitude fields (keperluan server) -->
              <input type="hidden" id="lat" name="latitude" :value="administrasiData?.latitude || ''">
              <input type="hidden" id="lng" name="longitude" :value="administrasiData?.longitude || ''">
            </div>
            <p class="text-xs text-gray-500 mt-1">Salin tautan lokasi dari Google Maps (Bagikan → Salin tautan) lalu tempel. Klik "Ambil Koordinat dari Tautan" untuk mengekstrak latitude/longitude otomatis. Jika tidak tersedia, sekolah dapat memasukkan tautan saja.</p>
          </div>

          <!-- Nama Kepala Sekolah (wajib) -->
          <div>
            <label for="kepala-sekolah" class="block text-sm font-medium text-gray-700 mb-1">Nama Kepala Sekolah <span class="text-red-500">*</span></label>
            <input id="kepala-sekolah" name="kepala_sekolah" type="text" :value="administrasiData?.kepala_sekolah || ''" :readonly="previewMode" required placeholder="Nama lengkap" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
          </div>

          <!-- Kontak Utama (email & WhatsApp) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Kontak <span class="text-red-500">*</span></label>
              <input id="email" name="email" type="email" :value="administrasiData?.email || ''" :readonly="previewMode" required placeholder="email@sekolah.sch.id" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
            </div>
            <div>
              <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Kontak <span class="text-red-500">*</span></label>
              <input id="whatsapp" name="whatsapp" type="tel" :value="administrasiData?.whatsapp || ''" :readonly="previewMode" required placeholder="62812xxxx (gunakan kode negara tanpa +)" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
            </div>
          </div>

          <!-- Tautan Website / Media Sosial (opsional) -->
          <div>
            <label for="tautan" class="block text-sm font-medium text-gray-700 mb-1">Tautan Website / Media Sosial (opsional)</label>
            <input id="tautan" name="tautan" type="url" :value="administrasiData?.tautan || ''" :readonly="previewMode" placeholder="https://..." :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
          </div>


        </div>

        <!-- A2. Tim Adiwiyata Sekolah -->
         <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b"> Tim Adiwiyata Sekolah</h2>
        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-green-700">Tim Adiwiyata Sekolah</h3>
            <p class="text-sm text-gray-600 mt-1">Data ini menjelaskan struktur organisasi pelaksana program Adiwiyata di tingkat sekolah, berguna bagi pendamping UM untuk menilai kesiapan internal.</p>
          </div>

          <!-- SK Tim Sekolah Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SK Tim Sekolah <span class="text-red-500">*</span></label>
            <div class="relative border-2 border-dashed rounded-lg p-6 transition-all duration-200 border-gray-300 hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
              <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-600">
                    <path d="M12 3v12"></path>
                    <path d="m17 8-5-5-5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  </svg>
                </div>
                <label for="sk-file" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-900 mb-1">Klik atau drag PDF di sini</p>
                  <p class="text-xs text-gray-600">Max: 10MB</p>
                  <input id="sk-file" name="sk_file" type="file" class="sr-only" accept=".pdf" required>
                </label>
              </div>
            </div>
          </div>

          <!-- Ketua Tim -->
          <div class="space-y-4">
            <h4 class="font-medium text-gray-700">Ketua Tim</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="ketua-nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="ketua-nama" name="ketua_nama" :value="administrasiData?.ketua_nama || ''" :readonly="previewMode" required :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="ketua-jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" id="ketua-jabatan" name="ketua_jabatan" :value="administrasiData?.ketua_jabatan || ''" :readonly="previewMode" required :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="ketua-wa" class="block text-sm font-medium text-gray-700 mb-1">No. HP/WA <span class="text-red-500">*</span></label>
                <input type="tel" id="ketua-wa" name="ketua_wa" :value="administrasiData?.ketua_wa || ''" :readonly="previewMode" required pattern="62[0-9]{9,}" placeholder="62812xxxxx" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="ketua-email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="ketua-email" name="ketua_email" :value="administrasiData?.ketua_email || ''" :readonly="previewMode" required :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
            </div>
          </div>

          <!-- Anggota Tim (Dynamic) -->
          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <h4 class="font-medium text-gray-700">Anggota Tim</h4>
              <button v-if="!previewMode" type="button" onclick="addAnggotaRow()" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                + Tambah Anggota
              </button>
            </div>
            <div id="anggota-container" class="space-y-4">
              <!-- Anggota rows will be added here -->
            </div>
          </div>

          <!-- Kader Adiwiyata -->
          <div class="space-y-4">
            <h4 class="font-medium text-gray-700">Kader Adiwiyata (Siswa)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="jumlah-kader" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kader Aktif</label>
                <input type="number" id="jumlah-kader" name="jumlah_kader" :value="administrasiData?.jumlah_kader || ''" :readonly="previewMode" min="0" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="komposisi-kader" class="block text-sm font-medium text-gray-700 mb-1">Komposisi/Sebaran Kelas</label>
                <input type="text" id="komposisi-kader" name="komposisi_kader" :value="administrasiData?.komposisi_kader || ''" :readonly="previewMode" placeholder="Contoh: 10 IPA, 11 IPS" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
            </div>
          </div>
        </div>

        <!-- A3. Target Peringkat Adiwiyata -->
         <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b">Target Capaian Sekolah</h2>
        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-green-700">Target Peringkat Adiwiyata</h3>
            <p class="text-sm text-gray-600 mt-1">Sekolah menentukan target capaian program tahun berjalan agar pendamping UM dapat menyesuaikan strategi bimbingan.</p>
          </div>

          <!-- Peringkat yang Ditargetkan -->
          <div>
            <label for="peringkat-target" class="block text-sm font-medium text-gray-700 mb-1">Peringkat yang Ditargetkan (tahun berjalan) <span class="text-red-500">*</span></label>
            <select id="peringkat-target" name="peringkat_target" :value="administrasiData?.peringkat_target || ''" :disabled="previewMode" required :class="previewMode ? 'bg-gray-100' : 'bg-white'" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              <option value="">Pilih Peringkat</option>
              <option value="Kabupaten/Kota">Kabupaten/Kota</option>
              <option value="Provinsi">Provinsi</option>
              <option value="Nasional">Nasional</option>
              <option value="Mandiri">Mandiri</option>
              <option value="ASEAN">ASEAN (opsional)</option>
            </select>
          </div>

          <!-- Tahun Target -->
          <div>
            <label for="tahun-target" class="block text-sm font-medium text-gray-700 mb-1">Tahun Target (YYYY) <span class="text-red-500">*</span></label>
            <input type="text" id="tahun-target" name="tahun_target" :value="administrasiData?.tahun_target || ''" :readonly="previewMode" required placeholder="YYYY" pattern="[0-9]{4}" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
          </div>

          <!-- Alasan & Kesiapan -->
          <div>
            <label for="alasan-kesiapan" class="block text-sm font-medium text-gray-700 mb-1">Alasan & Kesiapan</label>
            <textarea id="alasan-kesiapan" name="alasan_kesiapan" rows="4" :value="administrasiData?.alasan_kesiapan || ''" :readonly="previewMode" :class="previewMode ? 'bg-gray-100' : ''" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" placeholder="Uraikan kondisi awal, prestasi yang sudah dicapai, serta jenis dukungan yang dibutuhkan untuk mencapai target (maks. 500 kata)."></textarea>
          </div>
        </div>

        <!-- A4. Administrasi Dasar (Checklist & Unggahan) -->
         <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b">Administrasi Sekolah</h2>
        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-green-700">Administrasi Dasar (Checklist & Unggahan)</h3>
            <p class="text-sm text-gray-600 mt-1">Bagian ini memastikan kelengkapan dokumen administratif sebagai syarat awal mengikuti penilaian Adiwiyata.</p>
          </div>

          <!-- SK Penetapan Adiwiyata Tingkat Sebelumnya -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SK Penetapan Adiwiyata Tingkat Sebelumnya (jika ada)</label>
            <div class="relative border-2 border-dashed rounded-lg p-6 transition-all duration-200 border-gray-300 hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
              <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-600">
                    <path d="M12 3v12"></path>
                    <path d="m17 8-5-5-5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  </svg>
                </div>
                <label for="sk-sebelumnya-file" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-900 mb-1">Klik atau drag PDF di sini</p>
                  <p class="text-xs text-gray-600">Max: 10MB</p>
                  <input id="sk-sebelumnya-file" name="sk_sebelumnya_file" type="file" class="sr-only" accept=".pdf">
                </label>
              </div>
            </div>
          </div>

          <!-- Piagam Penghargaan Tingkat Sebelumnya -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Piagam Penghargaan Tingkat Sebelumnya (jika ada)</label>
            <div class="relative border-2 border-dashed rounded-lg p-6 transition-all duration-200 border-gray-300 hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
              <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-600">
                    <path d="M12 3v12"></path>
                    <path d="m17 8-5-5-5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  </svg>
                </div>
                <label for="piagam-sebelumnya-file" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-900 mb-1">Klik atau drag PDF di sini</p>
                  <p class="text-xs text-gray-600">Max: 10MB</p>
                  <input id="piagam-sebelumnya-file" name="piagam_sebelumnya_file" type="file" class="sr-only" accept=".pdf">
                </label>
              </div>
            </div>
          </div>

          <!-- Profil Sekolah -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Profil Sekolah</label>
            <div class="relative border-2 border-dashed rounded-lg p-6 transition-all duration-200 border-gray-300 hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
              <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-600">
                    <path d="M12 3v12"></path>
                    <path d="m17 8-5-5-5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  </svg>
                </div>
                <label for="profil-sekolah-file" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-900 mb-1">Klik atau drag PDF di sini</p>
                  <p class="text-xs text-gray-600">Max: 10MB</p>
                  <input id="profil-sekolah-file" name="profil_sekolah_file" type="file" class="sr-only" accept=".pdf">
                </label>
              </div>
            </div>
          </div>

          <!-- Dokumen Kurikulum -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen Kurikulum (KTSP/Kurikulum Satuan Pendidikan) Terkini</label>
            <div class="relative border-2 border-dashed rounded-lg p-6 transition-all duration-200 border-gray-300 hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
              <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-600">
                    <path d="M12 3v12"></path>
                    <path d="m17 8-5-5-5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  </svg>
                </div>
                <label for="kurikulum-file" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-900 mb-1">Klik atau drag PDF di sini</p>
                  <p class="text-xs text-gray-600">Max: 10MB</p>
                  <input id="kurikulum-file" name="kurikulum_file" type="file" class="sr-only" accept=".pdf">
                </label>
              </div>
            </div>
          </div>

          <!-- RPP terintegrasi PRLH -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RPP terintegrasi PRLH</label>
            <div class="relative border-2 border-dashed rounded-lg p-6 transition-all duration-200 border-gray-300 hover:border-blue-400 hover:bg-blue-50 cursor-pointer">
              <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-gray-600">
                    <path d="M12 3v12"></path>
                    <path d="m17 8-5-5-5 5"></path>
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  </svg>
                </div>
                <label for="rpp-file" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-900 mb-1">Klik atau drag PDF di sini</p>
                  <p class="text-xs text-gray-600">Max: 10MB — minimal 2 mata pelajaran per jenjang</p>
                  <input id="rpp-file" name="rpp_file" type="file" class="sr-only" accept=".pdf">
                </label>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!previewMode" class="mt-8 text-center">
          <button type="button" id="save-progress-btn" class="bg-green-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:bg-green-700 transition duration-300">
            Simpan Administrasi
          </button>
        </div>

        <!-- Back button untuk preview mode -->
        <div v-else class="mt-8 text-center">
          <Link
            href="/dashboard"
            class="inline-block bg-gray-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:bg-gray-700 transition duration-300"
          >
            Kembali ke Dashboard
          </Link>
        </div>
      </form>
      </div>
    </div>
    </main>
    </MainLayout>
</template>
