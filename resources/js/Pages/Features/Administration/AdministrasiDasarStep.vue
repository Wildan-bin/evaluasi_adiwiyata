<script setup>
import { reactive, computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { secureUpload } from '@/Composables/useCsrf';
import FileUploadCard from '@/Components/FileUploadCard.vue';
import { CheckCircle, Loader, FileText } from 'lucide-vue-next';

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
const savedDocuments = ref([]);

// Daftar indikator administrasi dasar
const indicators = [
  { key: 'visi_misi', label: 'Visi & Misi Sekolah Berwawasan Lingkungan' },
  { key: 'struktur_kurikulum', label: 'Struktur Kurikulum Berbasis Lingkungan' },
  { key: 'rpp', label: 'RPP Terintegrasi Pendidikan Lingkungan' },
  { key: 'program_kerja', label: 'Program Kerja Tim Adiwiyata' },
  { key: 'sop', label: 'SOP Pengelolaan Lingkungan Sekolah' }
];

const formData = reactive({
  files: {}
});

// Initialize files object
indicators.forEach(ind => {
  formData.files[ind.key] = { indikator: ind.label, file: null };
});

// ============================================================================
// COMPUTED
// ============================================================================
const fileCount = computed(() => {
  return Object.values(formData.files).filter(f => f.file !== null).length;
});

const isFormValid = computed(() => {
  return fileCount.value > 0;
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
    const response = await axios.get(route('administrasi.get-dasar'));
    if (response.data.documents && response.data.documents.length > 0) {
      dataExists.value = true;
      savedDocuments.value = response.data.documents;
    }
  } catch (error) {
    console.error('Error checking administrasi dasar data:', error);
    dataExists.value = false;
  }
};

const continueToNext = () => {
  emit('save-and-continue');
};

const handleFileUpload = (fieldName, file) => {
  formData.files[fieldName].file = file;
};

const saveAdministrasiDasar = async () => {
  if (!isFormValid.value) {
    draftSaveError.value = '⚠️ Harap upload minimal 1 dokumen administrasi dasar';
    return;
  }

  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const formDataObj = new FormData();

    Object.entries(formData.files).forEach(([fieldName, data]) => {
      if (data.file instanceof File) {
        formDataObj.append(`files[${fieldName}][file]`, data.file);
        formDataObj.append(`files[${fieldName}][indikator]`, data.indikator);
      }
    });

    const response = await secureUpload(route('administrasi.administrasi-dasar.store'), formDataObj);

    draftSaveMessage.value = '✓ Administrasi Dasar berhasil disimpan!';
    dataExists.value = true;

    setTimeout(() => {
      emit('save-and-continue');
    }, 1000);

  } catch (error) {
    console.error('Save administrasi dasar error:', error.response);
    draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan administrasi dasar';
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
          <h3 class="text-lg font-bold text-green-900">✓ Administrasi Dasar Sudah Lengkap</h3>
        </div>
        <p class="text-sm text-green-800">{{ savedDocuments.length }} dokumen administrasi dasar sudah tersimpan. Proses pengisian administrasi selesai!</p>
      </div>

      <!-- Display Saved Documents -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <h3 class="text-xl font-bold text-gray-900 border-b pb-3">Dokumen Tersimpan</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div 
            v-for="(doc, index) in savedDocuments" 
            :key="index"
            class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg hover:shadow-md transition-shadow"
          >
            <FileText class="w-10 h-10 text-blue-600 flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-900 truncate">{{ doc.indikator }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ doc.path_file }}</p>
            </div>
            <a 
              :href="`/storage/${doc.path_file}`"
              target="_blank"
              class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors flex-shrink-0"
            >
              Lihat
            </a>
          </div>
        </div>
      </div>

      <button
        @click="continueToNext"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all hover:bg-green-700"
      >
        ✓ Selesai
      </button>
    </div>

    <!-- Form (Hanya muncul jika data belum ada) -->
    <div v-else class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">📄 Administrasi Dasar</h2>
        <p class="text-gray-600">Upload dokumen administrasi dasar sekolah (PDF)</p>
      </div>

      <!-- File Upload Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <FileUploadCard
          v-for="indicator in indicators"
          :key="indicator.key"
          :label="indicator.label"
          description="Format: PDF, Max: 10MB"
          :model-value="formData.files[indicator.key].file"
          @update:model-value="handleFileUpload(indicator.key, $event)"
          :disabled="isSavingDraft || isSaving"
          accept="application/pdf"
        />
      </div>

      <!-- Progress -->
      <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center justify-between text-sm">
          <span class="text-blue-700 font-medium">Progress Upload:</span>
          <span class="text-blue-900 font-bold">{{ fileCount }} / {{ indicators.length }} dokumen</span>
        </div>
        <div class="mt-2 w-full bg-blue-200 rounded-full h-2">
          <div 
            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${(fileCount / indicators.length) * 100}%` }"
          ></div>
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
        @click="saveAdministrasiDasar"
        :disabled="!isFormValid || isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="isFormValid && !isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan Administrasi Dasar</span>
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
