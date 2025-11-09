<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

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
            title="Dashboard Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />
        <main class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12">
  <div class="container mx-auto px-4 max-w-4xl">
    <!-- Pengajuan Penilaian Section -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b">Identitas Sekolah</h2>
      
      <!-- Form Pengajuan -->
      <form class="space-y-8">
        <!-- Informasi Sekolah (Identitas Sekolah) -->
        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Sekolah</h3>
          
          <!-- NPSN (wajib) -->
          <div class="max-w-2xl">
            <label for="npsn" class="block text-sm font-medium text-gray-700 mb-1">NPSN <span class="text-red-500">*</span></label>
            <input id="npsn" 
                   name="npsn" 
                   type="text" 
                   required 
                   placeholder="Contoh: 12345678" 
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
                   required 
                   placeholder="Nama lengkap sesuai Dapodik" 
                   class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" />
          </div>

          <!-- Jenjang (wajib) & Status (wajib) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="jenjang" class="block text-sm font-medium text-gray-700 mb-1">Jenjang <span class="text-red-500">*</span></label>
              <select id="jenjang" 
                      name="jenjang" 
                      required 
                      class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors bg-white">
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
                      required 
                      class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors bg-white">
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
                     required 
                     placeholder="Nama Provinsi" 
                     class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" />
            </div>
            <div>
              <label for="kabkota" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten / Kota <span class="text-red-500">*</span></label>
              <input id="kabkota" 
                     name="kabkota" 
                     type="text" 
                     required 
                     placeholder="Nama Kabupaten / Kota" 
                     class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" />
            </div>
          </div>

          <!-- Alamat Lengkap -->
          <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea id="alamat" 
                      name="alamat" 
                      rows="3" 
                      required 
                      class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" 
                      placeholder="Contoh: Jl. Pendidikan No. 123, Desa Sejahtera, Kec. Cerdas, 12345"></textarea>
            <p class="text-xs text-gray-500 mt-2">Masukkan alamat lengkap termasuk nama jalan, desa/kelurahan, kecamatan, dan kode pos</p>
          </div>

          <!-- Tautan Google Maps (opsional) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tautan Google Maps (opsional)</label>
            <div class="space-y-2">
              <input id="maps_link" name="maps_link" type="url" placeholder="https://www.google.com/maps/place/..." class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
              <div class="flex gap-2 items-center">
                <button type="button" id="parse-maps-btn" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md">Ambil Koordinat dari Tautan</button>
                <button type="button" onclick="fillGeolocation()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md">Lokasi Saat Ini</button>
              </div>
              <p id="maps-extracted" class="text-xs text-gray-500 mt-1"></p>
              <!-- Hidden latitude/longitude fields (keperluan server) -->
              <input type="hidden" id="lat" name="latitude">
              <input type="hidden" id="lng" name="longitude">
            </div>
            <p class="text-xs text-gray-500 mt-1">Salin tautan lokasi dari Google Maps (Bagikan → Salin tautan) lalu tempel. Klik "Ambil Koordinat dari Tautan" untuk mengekstrak latitude/longitude otomatis. Jika tidak tersedia, sekolah dapat memasukkan tautan saja.</p>
          </div>

          <!-- Nama Kepala Sekolah (wajib) -->
          <div>
            <label for="kepala-sekolah" class="block text-sm font-medium text-gray-700 mb-1">Nama Kepala Sekolah <span class="text-red-500">*</span></label>
            <input id="kepala-sekolah" name="kepala_sekolah" type="text" required placeholder="Nama lengkap" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
          </div>

          <!-- Kontak Utama (email & WhatsApp) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Kontak <span class="text-red-500">*</span></label>
              <input id="email" name="email" type="email" required placeholder="email@sekolah.sch.id" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
            </div>
            <div>
              <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Kontak <span class="text-red-500">*</span></label>
              <input id="whatsapp" name="whatsapp" type="tel" required placeholder="62812xxxx (gunakan kode negara tanpa +)" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
            </div>
          </div>

          <!-- Tautan Website / Media Sosial (opsional) -->
          <div>
            <label for="tautan" class="block text-sm font-medium text-gray-700 mb-1">Tautan Website / Media Sosial (opsional)</label>
            <input id="tautan" name="tautan" type="url" placeholder="https://..." class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
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
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                  <label for="sk-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                    <span>Upload file</span>
                    <input id="sk-file" name="sk_file" type="file" class="sr-only" accept=".pdf" required>
                  </label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Ketua Tim -->
          <div class="space-y-4">
            <h4 class="font-medium text-gray-700">Ketua Tim</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="ketua-nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="ketua-nama" name="ketua_nama" required class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="ketua-jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" id="ketua-jabatan" name="ketua_jabatan" required class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="ketua-wa" class="block text-sm font-medium text-gray-700 mb-1">No. HP/WA <span class="text-red-500">*</span></label>
                <input type="tel" id="ketua-wa" name="ketua_wa" required pattern="62[0-9]{9,}" placeholder="62812xxxxx" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="ketua-email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="ketua-email" name="ketua_email" required class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
            </div>
          </div>

          <!-- Anggota Tim (Dynamic) -->
          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <h4 class="font-medium text-gray-700">Anggota Tim</h4>
              <button type="button" onclick="addAnggotaRow()" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
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
                <input type="number" id="jumlah-kader" name="jumlah_kader" min="0" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
              </div>
              <div>
                <label for="komposisi-kader" class="block text-sm font-medium text-gray-700 mb-1">Komposisi/Sebaran Kelas</label>
                <input type="text" id="komposisi-kader" name="komposisi_kader" placeholder="Contoh: 10 IPA, 11 IPS" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
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
            <select id="peringkat-target" name="peringkat_target" required class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors bg-white">
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
            <input type="text" id="tahun-target" name="tahun_target" required placeholder="YYYY" pattern="[0-9]{4}" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
          </div>

          <!-- Alasan & Kesiapan -->
          <div>
            <label for="alasan-kesiapan" class="block text-sm font-medium text-gray-700 mb-1">Alasan & Kesiapan</label>
            <textarea id="alasan-kesiapan" name="alasan_kesiapan" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm px-4 py-3 border focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors" placeholder="Uraikan kondisi awal, prestasi yang sudah dicapai, serta jenis dukungan yang dibutuhkan untuk mencapai target (maks. 500 kata)."></textarea>
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
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="sk-sebelumnya-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="sk-sebelumnya-file" name="sk_sebelumnya_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Piagam Penghargaan Tingkat Sebelumnya -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Piagam Penghargaan Tingkat Sebelumnya (jika ada)</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="piagam-sebelumnya-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="piagam-sebelumnya-file" name="piagam_sebelumnya_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Profil Sekolah -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Profil Sekolah</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="profil-sekolah-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="profil-sekolah-file" name="profil_sekolah_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Dokumen Kurikulum -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen Kurikulum (KTSP/Kurikulum Satuan Pendidikan) Terkini</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="kurikulum-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="kurikulum-file" name="kurikulum_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- RPP terintegrasi PRLH -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RPP terintegrasi PRLH</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="rpp-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="rpp-file" name="rpp_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB — minimal 2 mata pelajaran per jenjang</p>
              </div>
            </div>
          </div>
        </div>

        <!-- A5. Rencana & Evaluasi PBLHS -->
         <h2 class="text-2xl font-bold text-green-800 mb-8 pb-4 border-b">Rencana & Evaluasi PBLHS</h2>
        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-green-700">Rencana & Evaluasi PBLHS</h3>
            <p class="text-sm text-gray-600 mt-1">Berisi dokumen perencanaan dan hasil evaluasi pelaksanaan Gerakan Peduli dan Berbudaya Lingkungan Hidup di Sekolah (PBLHS).</p>
          </div>

          <!-- Evaluasi Diri Sekolah (EDS) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Evaluasi Diri Sekolah (EDS) – aspek lingkungan</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="eds-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="eds-file" name="eds_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Hasil IPMLH -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasil IPMLH (Identifikasi Potensi & Masalah LH)</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="ipmlh-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="ipmlh-file" name="ipmlh_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Rencana GPBLHS 4 Tahunan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rencana GPBLHS 4 Tahunan</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="gpblhs-4-tahunan-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="gpblhs-4-tahunan-file" name="gpblhs_4_tahunan_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Rencana GPBLHS Tahunan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rencana GPBLHS Tahunan (Tahun berjalan)</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="gpblhs-tahunan-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="gpblhs-tahunan-file" name="gpblhs_tahunan_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>

          <!-- Dokumentasi Penyusunan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokumentasi Penyusunan (undangan, daftar hadir, notulensi, foto)</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
              <div class="space-y-2 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <div class="flex text-sm text-gray-600">
                  <label for="dokumentasi-penyusunan-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none"><span>Upload file</span><input id="dokumentasi-penyusunan-file" name="dokumentasi_penyusunan_file" type="file" class="sr-only" accept=".pdf"></label>
                  <p class="pl-1">atau drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF maksimal 10MB</p>
              </div>
            </div>
          </div>
        </div>

        

          <!-- Form actions: Save Draft & Submit -->
          <div class="pt-6 border-t">
            <div class="flex flex-wrap gap-3 justify-end">
              <input type="hidden" id="save-action" name="save_action" value="submit">
              <button type="button" id="save-draft-btn" class="px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 border border-yellow-200 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-300">Simpan sebagai Draft</button>
              <button type="submit" id="submit-btn" class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Ajukan Penilaian</button>
            </div>
          </div>

        </form>

        

        </div>
  </div>
</main>
    </MainLayout>
</template>