<script setup>
import { reactive, computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { secureUpload } from '@/Composables/useCsrf';
import FileUploadCard from '@/Components/FileUploadCard.vue';
import { CheckCircle, Loader } from 'lucide-vue-next';

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

const indicators = [
  { key: 'evaluasi_diri_sekolah', label: 'Evaluasi Diri Sekolah' },
  { key: 'hasil_ipmlh', label: 'Hasil IPMLH' },
  { key: 'rencana_gpblhs_4_tahunan', label: 'Rencana GPBLHS 4-tahunan' },
  { key: 'rencana_gpblhs_tahunan', label: 'Rencana GPBLHS tahunan' },
  { key: 'dokumentasi_penyusunan', label: 'Dokumentasi Penyusunan' },
  { key: 'sk_tim_sekolah', label: 'SK Tim Sekolah' }
];

const formData = reactive({
  files: {}
});

// Initialize files object
indicators.forEach(ind => {
  formData.files[ind.key] = null;
});

// ============================================================================
// COMPUTED
// ============================================================================
const fileCount = computed(() => {
  return Object.values(formData.files).filter(f => f !== null).length;
});

const isFormValid = computed(() => {
  return fileCount.value > 0; // Minimal 1 file saja
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
    const response = await axios.get(route('form.get-a5'));
    dataExists.value = response.data.data_exists;
  } catch (error) {
    console.error('Error checking A5 data:', error);
    dataExists.value = false;
  }
};

const continueToNext = () => {
  emit('save-and-continue');
};

const handleFileUpload = (fieldName, file) => {
  formData.files[fieldName] = file;
};

const saveA5 = async () => {
  if (!isFormValid.value) {
    draftSaveError.value = '⚠️ Harap upload minimal 1 file';
    return;
  }

  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const formDataObj = new FormData();

    // Append files
    Object.entries(formData.files).forEach(([fieldName, file]) => {
      if (file instanceof File) {
        formDataObj.append(fieldName, file);
      }
    });

    // Gunakan secureUpload untuk refresh CSRF sebelum upload
    const response = await secureUpload(route('form.save-a5'), formDataObj);

    draftSaveMessage.value = '✓ A5 berhasil disimpan!';
    dataExists.value = true;

    // Emit ke parent untuk navigate
    setTimeout(() => {
      emit('save-and-continue');
    }, 1000);

  } catch (error) {
    console.error('Save A5 error:', error.response);

    if (error.response?.status === 422 && error.response?.data?.data_exists) {
      dataExists.value = true;
      draftSaveMessage.value = '✓ A5 berhasil disimpan!';

      setTimeout(() => {
        emit('save-and-continue');
      }, 1000);
    } else {
      draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan A5';
    }
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
          <h3 class="text-lg font-bold text-green-900">Anda Sudah Mengisi Bagian Ini</h3>
        </div>
        <p class="text-sm text-green-800">Data A5 (Rencana & Evaluasi PBLHS) sudah tersimpan. Silakan lanjut ke tahap berikutnya.</p>
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
        <h2 class="text-2xl font-bold text-gray-900 mb-2">📋 Rencana & Evaluasi PBLHS</h2>
        <p class="text-gray-600">Langkah 1 dari 4 - Upload 6 file dokumen</p>
      </div>

      <!-- File Upload Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <FileUploadCard
          v-for="indicator in indicators"
          :key="indicator.key"
          :label="indicator.label"
          description="Format: PDF, Max: 10MB"
          :model-value="formData.files[indicator.key]"
          @update:model-value="handleFileUpload(indicator.key, $event)"
          :disabled="isSavingDraft || isSaving"
        />
      </div>

      <!-- Progress -->
      <div class="text-sm text-gray-600">
        {{ fileCount }} / {{ indicators.length }} file terupload
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
        @click="saveA5"
        :disabled="!isFormValid || isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="isFormValid && !isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan A5</span>
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
