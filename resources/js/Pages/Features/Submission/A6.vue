<script setup>
import { reactive, computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
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
  { key: 'kebersihan', label: 'Kebersihan, Sanitasi, Drainase' },
  { key: 'kelola_sampah', label: 'Pengelolaan Sampah (3R + bank sampah)' },
  { key: 'pemeliharaan_pohon', label: 'Pemeliharaan Pohon/Tanaman' },
  { key: 'konservasi_air', label: 'Konservasi Air' },
  { key: 'konservasi_energi', label: 'Konservasi Energi' },
  { key: 'inovasi_prlh', label: 'Inovasi PRLH' },
  { key: 'penerapan_prlh', label: 'Penerapan PRLH di Masyarakat' },
  { key: 'jejaring', label: 'Jejaring dan Komunikasi' },
  { key: 'kampanye_media', label: 'Kampanye & Media Publikasi' },
  { key: 'pembinaan_kader', label: 'Pembentukan & Pembinaan Kader' },
  { key: 'pemantauan_evaluasi', label: 'Pemantauan & Evaluasi' }
];

const formData = reactive({
  indicators: {}
});

// Initialize indicators object
indicators.forEach(ind => {
  formData.indicators[ind.key] = {
    file: null,
    penjelasan: ''
  };
});

// ============================================================================
// COMPUTED
// ============================================================================
const completedCount = computed(() => {
  return Object.values(formData.indicators).filter(ind => ind.file && ind.penjelasan.trim()).length;
});

const isFormValid = computed(() => {
  return completedCount.value === indicators.length;
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
    const response = await axios.get(route('form.get-a6'));
    dataExists.value = response.data.data_exists;
  } catch (error) {
    console.error('Error checking A6 data:', error);
    dataExists.value = false;
  }
};

const handleFileUpload = (indicatorKey, file) => {
  formData.indicators[indicatorKey].file = file;
};

const handlePenjelasan = (indicatorKey, value) => {
  formData.indicators[indicatorKey].penjelasan = value;
};

const saveA6 = async () => {
  if (!isFormValid.value) {
    draftSaveError.value = '⚠️ Semua indikator harus memiliki file dan penjelasan';
    return;
  }

  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const formDataObj = new FormData();

    // Build FormData
    Object.entries(formData.indicators).forEach(([key, data]) => {
      if (data.file instanceof File) {
        formDataObj.append(`bukti_${key}`, data.file);
      }
      formDataObj.append(`penjelasan_${key}`, data.penjelasan);
    });

    // Gunakan secureUpload untuk refresh CSRF sebelum upload
    const response = await secureUpload(route('form.save-a6'), formDataObj);

    draftSaveMessage.value = '✓ A6 berhasil disimpan!';
    dataExists.value = true;

    setTimeout(() => {
      emit('save-and-continue');
      router.reload({
        preserveScroll: true,
        only: ['completedSteps']
      });
    }, 1000);

  } catch (error) {
    console.error('Save A6 error:', error.response);
    
    if (error.response?.status === 422 && error.response?.data?.data_exists) {
      dataExists.value = true;
      draftSaveMessage.value = '✓ A6 berhasil disimpan!';
      
      setTimeout(() => {
        emit('save-and-continue');
        router.reload({
          preserveScroll: true,
          only: ['completedSteps']
        });
      }, 1000);
    } else {
      draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan A6';
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
        <p class="text-sm text-green-800">Data A6 (Self Assessment) sudah tersimpan. Silakan lanjut ke tahap berikutnya.</p>
      </div>
    </div>

    <!-- Form -->
    <div v-else class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">✓ Self Assessment</h2>
        <p class="text-gray-600">Langkah 2 dari 4 - Upload bukti dan penjelasan untuk 11 indikator</p>
      </div>

      <!-- Progress Bar -->
      <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold text-gray-700">Progress: {{ completedCount }}/{{ indicators.length }}</span>
          <span class="text-sm font-semibold text-green-600">{{ Math.round((completedCount / indicators.length) * 100) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
          <div 
            class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-500"
            :style="{ width: Math.round((completedCount / indicators.length) * 100) + '%' }"
          ></div>
        </div>
      </div>

      <!-- Indicators -->
      <div class="space-y-6">
        <div v-for="(indicator, idx) in indicators" :key="indicator.key" class="p-6 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-bold text-sm">
              {{ idx + 1 }}
            </div>

            <div class="flex-1 space-y-4">
              <h3 class="font-semibold text-gray-900">{{ indicator.label }}</h3>

              <!-- File Upload -->
              <FileUploadCard
                label="Upload Bukti"
                description="Format: PDF, Max: 10MB"
                :model-value="formData.indicators[indicator.key].file"
                @update:model-value="handleFileUpload(indicator.key, $event)"
                :disabled="isSavingDraft || isSaving"
              />

              <!-- Penjelasan -->
              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Penjelasan</label>
                <textarea
                  :value="formData.indicators[indicator.key].penjelasan"
                  @input="handlePenjelasan(indicator.key, $event.target.value)"
                  rows="3"
                  placeholder="Jelaskan bukti dan pencapaian indikator ini..."
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:outline-none resize-none"
                  :disabled="isSavingDraft || isSaving"
                ></textarea>
              </div>

              <!-- Status Badge -->
              <div v-if="formData.indicators[indicator.key].file && formData.indicators[indicator.key].penjelasan.trim()" class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                <CheckCircle class="w-4 h-4" />
                Selesai
              </div>
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
        @click="saveA6"
        :disabled="!isFormValid || isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="isFormValid && !isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan A6</span>
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