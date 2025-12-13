<script setup>
import { reactive, computed, ref, onMounted } from 'vue';
import axios from 'axios';
import FileUploadCard from '@/Components/FileUploadCard.vue';
import { CheckCircle, Loader, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
  isSaving: Boolean
});

const emit = defineEmits(['final-submit']);

// ============================================================================
// STATE
// ============================================================================
const isSavingDraft = ref(false);
const draftSaveMessage = ref('');
const draftSaveError = ref('');
const dataExists = ref(false);
const isLoading = ref(true);

const formData = reactive({
  pernyataan_data: 'benar',
  persetujuan_publikasi: 'setuju',
  bukti_persetujuan: null
});

// ============================================================================
// COMPUTED
// ============================================================================
const isFormValid = computed(() => {
  return formData.pernyataan_data &&
         formData.persetujuan_publikasi &&
         formData.bukti_persetujuan;
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
    const response = await axios.get(route('form.get-a8'));
    dataExists.value = response.data.data_exists;
  } catch (error) {
    console.error('Error checking A8 data:', error);
    dataExists.value = false;
  }
};

const handleFileUpload = (file) => {
  formData.bukti_persetujuan = file;
};

const saveA8 = async () => {
  if (!isFormValid.value) {
    draftSaveError.value = '⚠️ Harap isi semua field dengan benar';
    return;
  }

  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const formDataObj = new FormData();
    formDataObj.append('pernyataan_data', formData.pernyataan_data);
    formDataObj.append('persetujuan_publikasi', formData.persetujuan_publikasi);

    if (formData.bukti_persetujuan instanceof File) {
      formDataObj.append('bukti_persetujuan', formData.bukti_persetujuan);
    }

    const response = await axios.post(route('form.save-a8'), formDataObj);

    draftSaveMessage.value = '✓ A8 berhasil disimpan!';
    dataExists.value = true;

    setTimeout(() => {
      emit('final-submit');
    }, 1000);

  } catch (error) {
    console.error('Save A8 error:', error.response);

    if (error.response?.status === 422 && error.response?.data?.data_exists) {
      dataExists.value = true;
      draftSaveMessage.value = '✓ A8 berhasil disimpan!';

      setTimeout(() => {
        emit('final-submit');
      }, 1000);
    } else {
      draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan A8';
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
          <h3 class="text-lg font-bold text-green-900">Anda Sudah Mengisi Semua Bagian</h3>
        </div>
        <p class="text-sm text-green-800">Semua data telah tersimpan. Silakan klik tombol di bawah untuk mengirim proposal Anda.</p>
      </div>
    </div>

    <!-- Form -->
    <div v-else class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">📝 Pernyataan & Persetujuan</h2>
        <p class="text-gray-600">Langkah 4 dari 4 - Konfirmasi kebenaran data dan persetujuan publikasi</p>
      </div>

      <!-- Pernyataan Data -->
      <div class="space-y-4">
        <label class="block text-sm font-semibold text-gray-900">Pernyataan Kebenaran Data <span class="text-red-500">*</span></label>
        <div class="space-y-2">
          <button
            v-for="option in [{ value: 'benar', label: 'Data yang saya masukkan adalah benar' }, { value: 'tidak benar', label: 'Ada data yang tidak benar' }]"
            :key="option.value"
            @click="formData.pernyataan_data = option.value"
            class="w-full flex items-start gap-4 p-4 border-2 rounded-lg transition-all text-left"
            :class="[
              formData.pernyataan_data === option.value
                ? 'border-green-500 bg-green-50'
                : 'border-gray-200 bg-white hover:border-gray-300'
            ]"
            :disabled="isSavingDraft || isSaving"
          >
            <input
              type="radio"
              :value="option.value"
              v-model="formData.pernyataan_data"
              class="w-5 h-5 mt-1"
              :disabled="isSavingDraft || isSaving"
            />
            <span class="font-semibold text-gray-900">{{ option.label }}</span>
          </button>
        </div>
      </div>

      <!-- Persetujuan Publikasi -->
      <div class="space-y-4">
        <label class="block text-sm font-semibold text-gray-900">Persetujuan Publikasi <span class="text-red-500">*</span></label>
        <div class="space-y-2">
          <button
            v-for="option in [{ value: 'setuju', label: 'Saya setuju data dapat dipublikasikan' }, { value: 'tidak setuju', label: 'Saya tidak setuju data dipublikasikan' }]"
            :key="option.value"
            @click="formData.persetujuan_publikasi = option.value"
            class="w-full flex items-start gap-4 p-4 border-2 rounded-lg transition-all text-left"
            :class="[
              formData.persetujuan_publikasi === option.value
                ? 'border-blue-500 bg-blue-50'
                : 'border-gray-200 bg-white hover:border-gray-300'
            ]"
            :disabled="isSavingDraft || isSaving"
          >
            <input
              type="radio"
              :value="option.value"
              v-model="formData.persetujuan_publikasi"
              class="w-5 h-5 mt-1"
              :disabled="isSavingDraft || isSaving"
            />
            <span class="font-semibold text-gray-900">{{ option.label }}</span>
          </button>
        </div>
      </div>

      <!-- Bukti Persetujuan -->
      <div>
        <FileUploadCard
          label="Dokumen Persetujuan"
          description="Upload surat persetujuan kepala sekolah. Format: PDF, Max: 5MB"
          :model-value="formData.bukti_persetujuan"
          @update:model-value="handleFileUpload"
          :disabled="isSavingDraft || isSaving"
        />
      </div>

      <!-- Warning Box -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
      >
        <div v-if="!isFormValid" class="p-4 bg-orange-50 border-2 border-orange-200 rounded-lg flex gap-3">
          <AlertCircle class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" />
          <p class="text-sm text-orange-700">⚠️ Semua field harus diisi sebelum pengiriman proposal.</p>
        </div>
      </transition>

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
        @click="saveA8"
        :disabled="!isFormValid || isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="isFormValid && !isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan A8</span>
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
