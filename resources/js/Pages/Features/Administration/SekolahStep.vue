<script setup>
import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';
import { Loader, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
  isSaving: Boolean
});

const emit = defineEmits(['save-and-continue']);

// ============================================================================
// STATE
// ============================================================================
const isSavingDraft = ref(false);
const draftSaveMessage = ref('');
const draftSaveError = ref('');
const dataExists = ref(false);
const isLoading = ref(true);
const savedData = ref(null);

const formData = reactive({
  nama_sekolah: '',
  npsn: '',
  alamat: '',
  kelurahan: '',
  kecamatan: '',
  kota: '',
  provinsi: '',
  kode_pos: '',
  telepon: '',
  email: '',
  website: '',
  latitude: '',
  longitude: '',
  google_maps_url: '',
  nama_kepala_sekolah: '',
  nip_kepala_sekolah: '',
  telp_kepala_sekolah: ''
});

// ============================================================================
// LIFECYCLE
// ============================================================================
onMounted(async () => {
  await checkDataExists();
  isLoading.value = false;
});

// ============================================================================
// METHODS
// ============================================================================
const checkDataExists = async () => {
  try {
    const response = await axios.get(route('administrasi.get-sekolah'));
    if (response.data.sekolah) {
      dataExists.value = true;
      savedData.value = response.data.sekolah;
      Object.assign(formData, response.data.sekolah);
    }
  } catch (error) {
    console.error('Error checking sekolah data:', error);
    dataExists.value = false;
  }
};

const continueToNext = () => {
  emit('save-and-continue');
};

const saveSekolah = async () => {
  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const response = await axios.post(route('administrasi.sekolah.store'), formData);

    draftSaveMessage.value = '✓ Data sekolah berhasil disimpan!';
    dataExists.value = true;

    setTimeout(() => {
      emit('save-and-continue');
    }, 1000);

  } catch (error) {
    console.error('Save sekolah error:', error.response);
    draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan data sekolah';
  } finally {
    isSavingDraft.value = false;
  }
};
</script>

<template>
  <div class="space-y-8">
    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-12">
      <Loader class="w-8 h-8 animate-spin text-green-600" />
    </div>

    <!-- Data Already Exists - READ ONLY VIEW -->
    <div v-else-if="dataExists" class="space-y-6">
      <div class="p-6 bg-green-50 border-2 border-green-500 rounded-xl">
        <div class="flex items-center gap-3 mb-2">
          <CheckCircle class="w-6 h-6 text-green-600" />
          <h3 class="text-lg font-bold text-green-900">✓ Data Sekolah Sudah Tersimpan</h3>
        </div>
        <p class="text-sm text-green-800">Data administrasi sekolah sudah lengkap. Silakan lanjut ke tahap berikutnya.</p>
      </div>

      <!-- Display Saved Data -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        <h3 class="text-xl font-bold text-gray-900 border-b pb-3">Informasi Sekolah</h3>

        <!-- Identitas Sekolah -->
        <div class="space-y-4">
          <h4 class="text-sm font-semibold text-gray-500 uppercase">Identitas Sekolah</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Nama Sekolah</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.nama_sekolah || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">NPSN</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.npsn || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Alamat -->
        <div class="space-y-4">
          <h4 class="text-sm font-semibold text-gray-500 uppercase">Alamat</h4>
          <div class="bg-gray-50 p-3 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p>
            <p class="text-sm font-semibold text-gray-900">{{ savedData?.alamat || '-' }}</p>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Kelurahan</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.kelurahan || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Kecamatan</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.kecamatan || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Kota/Kab</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.kota || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Provinsi</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.provinsi || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Kontak -->
        <div class="space-y-4">
          <h4 class="text-sm font-semibold text-gray-500 uppercase">Kontak</h4>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Kode Pos</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.kode_pos || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Telepon</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.telepon || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Email</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.email || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Website</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.website || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Kepala Sekolah -->
        <div class="space-y-4">
          <h4 class="text-sm font-semibold text-gray-500 uppercase">Kepala Sekolah</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Nama</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.nama_kepala_sekolah || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">NIP</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.nip_kepala_sekolah || '-' }}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
              <p class="text-sm font-semibold text-gray-900">{{ savedData?.telp_kepala_sekolah || '-' }}</p>
            </div>
          </div>
        </div>
      </div>

      <button
        @click="continueToNext"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all hover:bg-green-700"
      >
        Lanjut ke Tahap Berikutnya →
      </button>
    </div>

    <!-- Form (Hanya muncul jika data belum ada) -->
    <div v-else class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">🏫 Data Sekolah</h2>
        <p class="text-gray-600">Lengkapi informasi administrasi sekolah</p>
      </div>

      <!-- Identitas Sekolah -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Identitas Sekolah</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah *</label>
            <input
              v-model="formData.nama_sekolah"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="Nama lengkap sekolah"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">NPSN *</label>
            <input
              v-model="formData.npsn"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="Nomor Pokok Sekolah Nasional"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
          <textarea
            v-model="formData.alamat"
            rows="3"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="Alamat lengkap sekolah"
          ></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kelurahan *</label>
            <input
              v-model="formData.kelurahan"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan *</label>
            <input
              v-model="formData.kecamatan"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten *</label>
            <input
              v-model="formData.kota"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi *</label>
            <input
              v-model="formData.provinsi"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Kontak -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Kontak</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
            <input
              v-model="formData.kode_pos"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
            <input
              v-model="formData.telepon"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="formData.email"
              type="email"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
            <input
              v-model="formData.website"
              type="url"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Koordinat -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Koordinat Lokasi</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
            <input
              v-model="formData.latitude"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="-6.200000"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
            <input
              v-model="formData.longitude"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="106.816666"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Google Maps URL</label>
            <input
              v-model="formData.google_maps_url"
              type="url"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Kepala Sekolah -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Data Kepala Sekolah</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kepala Sekolah *</label>
            <input
              v-model="formData.nama_kepala_sekolah"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
            <input
              v-model="formData.nip_kepala_sekolah"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
            <input
              v-model="formData.telp_kepala_sekolah"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
      >
        <div v-if="draftSaveMessage" class="p-4 bg-green-50 border border-green-300 rounded-lg text-green-700 font-medium">
          {{ draftSaveMessage }}
        </div>
      </transition>

      <!-- Error Message -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
      >
        <div v-if="draftSaveError" class="p-4 bg-red-50 border border-red-300 rounded-lg text-red-700 font-medium">
          {{ draftSaveError }}
        </div>
      </transition>

      <!-- Save Button -->
      <button
        @click="saveSekolah"
        :disabled="isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="!isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan Data Sekolah</span>
      </button>
    </div>
  </div>
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
</style>
