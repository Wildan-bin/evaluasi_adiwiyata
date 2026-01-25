<script setup>
import { reactive, computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { secureUpload } from '@/Composables/useCsrf';
import FileUploadCard from '@/Components/FileUploadCard.vue';
import { CheckCircle, Loader, Plus, Trash2 } from 'lucide-vue-next';

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

const formData = reactive({
  sk_tim_file: null,
  ketua: {
    nama: '',
    nip: '',
    email: '',
    nomor_telepon: ''
  },
  anggota: [
    { nama: '', nip: '', email: '', nomor_telepon: '' }
  ]
});

// ============================================================================
// COMPUTED
// ============================================================================
const hasSkTimFile = computed(() => formData.sk_tim_file !== null);

const isFormValid = computed(() => {
  return hasSkTimFile.value && 
         formData.ketua.nama.trim() !== '';
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
    const response = await axios.get(route('administrasi.get-tim'));
    if (response.data.data_exists) {
      dataExists.value = true;
      if (response.data.data) {
        Object.assign(formData, response.data.data);
      }
    }
  } catch (error) {
    console.error('Error checking tim data:', error);
    dataExists.value = false;
  }
};

const continueToNext = () => {
  emit('save-and-continue');
};

const handleFileUpload = (file) => {
  formData.sk_tim_file = file;
};

const addAnggota = () => {
  formData.anggota.push({ nama: '', nip: '', email: '', nomor_telepon: '' });
};

const removeAnggota = (index) => {
  if (formData.anggota.length > 1) {
    formData.anggota.splice(index, 1);
  }
};

const saveTim = async () => {
  if (!isFormValid.value) {
    draftSaveError.value = '⚠️ Harap upload SK Tim dan isi data Ketua';
    return;
  }

  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const formDataObj = new FormData();

    // Append SK Tim file
    if (formData.sk_tim_file instanceof File) {
      formDataObj.append('sk_tim_file', formData.sk_tim_file);
    }

    // Append ketua data
    formDataObj.append('ketua', JSON.stringify(formData.ketua));

    // Append anggota data
    formDataObj.append('anggota', JSON.stringify(formData.anggota));

    const response = await secureUpload(route('administrasi.tim.store'), formDataObj);

    draftSaveMessage.value = '✓ Data Tim berhasil disimpan!';
    dataExists.value = true;

    setTimeout(() => {
      emit('save-and-continue');
    }, 1000);

  } catch (error) {
    console.error('Save tim error:', error.response);
    draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan data tim';
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

    <!-- Data Already Exists -->
    <div v-else-if="dataExists" class="space-y-6">
      <div class="p-6 bg-green-50 border-2 border-green-500 rounded-xl">
        <div class="flex items-center gap-3 mb-2">
          <CheckCircle class="w-6 h-6 text-green-600" />
          <h3 class="text-lg font-bold text-green-900">Data Tim Sudah Tersimpan</h3>
        </div>
        <p class="text-sm text-green-800">SK Tim, Ketua, dan Anggota sudah lengkap. Silakan lanjut ke tahap berikutnya.</p>
      </div>

      <button
        @click="continueToNext"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all hover:bg-green-700"
      >
        Lanjut ke Tahap Berikutnya
      </button>
    </div>

    <!-- Form -->
    <div v-else class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">👥 SK Tim & Struktur</h2>
        <p class="text-gray-600">Upload SK Tim dan lengkapi data Ketua & Anggota</p>
      </div>

      <!-- SK Tim Upload -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">SK Tim Adiwiyata (PDF) *</h3>
        <FileUploadCard
          label="SK Tim Adiwiyata"
          description="Format: PDF, Max: 10MB"
          :model-value="formData.sk_tim_file"
          @update:model-value="handleFileUpload"
          :disabled="isSavingDraft || isSaving"
          accept="application/pdf"
        />
      </div>

      <!-- Ketua -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">Data Ketua Tim *</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Ketua *</label>
            <input
              v-model="formData.ketua.nama"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="Nama lengkap ketua"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
            <input
              v-model="formData.ketua.nip"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="NIP ketua"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="formData.ketua.email"
              type="email"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="email@example.com"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
            <input
              v-model="formData.ketua.nomor_telepon"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="08xxxxxxxxxx"
            />
          </div>
        </div>
      </div>

      <!-- Anggota -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800">Data Anggota Tim</h3>
          <button
            @click="addAnggota"
            type="button"
            class="flex items-center gap-2 px-4 py-2 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors"
          >
            <Plus class="w-4 h-4" /> Tambah Anggota
          </button>
        </div>

        <div v-for="(anggota, index) in formData.anggota" :key="index" class="relative p-4 bg-gray-50 rounded-lg border border-gray-200">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-600">Anggota {{ index + 1 }}</span>
            <button
              v-if="formData.anggota.length > 1"
              @click="removeAnggota(index)"
              type="button"
              class="p-1 text-red-600 hover:bg-red-100 rounded transition-colors"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
              <input
                v-model="anggota.nama"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="Nama anggota"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
              <input
                v-model="anggota.nip"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="NIP anggota"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                v-model="anggota.email"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="email@example.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
              <input
                v-model="anggota.nomor_telepon"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="08xxxxxxxxxx"
              />
            </div>
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
        @click="saveTim"
        :disabled="!isFormValid || isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="isFormValid && !isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan Data Tim</span>
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
