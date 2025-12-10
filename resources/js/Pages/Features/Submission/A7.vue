<script setup>
import { reactive, computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { CheckCircle, Loader, Check } from 'lucide-vue-next';

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

const kebutuhanOptions = [
  { key: 'kurikulum_terintegrasi', label: 'Kurikulum terintegrasi PRLH' },
  { key: 'pengelolaan_sampah', label: 'Pengelolaan sampah & bank sampah' },
  { key: 'konservasi_air', label: 'Konservasi air' },
  { key: 'konservasi_energi', label: 'Konservasi energi' },
  { key: 'inovasi_lingkungan', label: 'Perancangan inovasi lingkungan' },
  { key: 'pelaporan_monev', label: 'Pelaporan & monev (dashboard)' }
];

const formData = reactive({
  kebutuhan_pendampingan: [],
  permintaan_tim: '',
  waktu_pendampingan: ''
});

// ============================================================================
// COMPUTED
// ============================================================================
const isFormValid = computed(() => {
  return formData.kebutuhan_pendampingan.length > 0 && 
         formData.permintaan_tim.trim() && 
         formData.waktu_pendampingan;
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
    const response = await axios.get(route('form.get-a7'));
    dataExists.value = response.data.data_exists;
  } catch (error) {
    console.error('Error checking A7 data:', error);
    dataExists.value = false;
  }
};

const toggleSelection = (key) => {
  const index = formData.kebutuhan_pendampingan.indexOf(key);
  if (index > -1) {
    formData.kebutuhan_pendampingan.splice(index, 1);
  } else {
    formData.kebutuhan_pendampingan.push(key);
  }
};

const isSelected = (key) => {
  return formData.kebutuhan_pendampingan.includes(key);
};

const saveA7 = async () => {
  if (!isFormValid.value) {
    draftSaveError.value = '⚠️ Harap isi semua field dengan benar';
    return;
  }

  draftSaveError.value = '';
  draftSaveMessage.value = '';
  isSavingDraft.value = true;

  try {
    const payload = {
      kebutuhan_pendampingan: formData.kebutuhan_pendampingan,
      permintaan_tim: formData.permintaan_tim,
      waktu_pendampingan: formData.waktu_pendampingan
    };

    const response = await axios.post(route('form.save-a7'), payload);

    draftSaveMessage.value = '✓ A7 berhasil disimpan!';
    dataExists.value = true;

    setTimeout(() => {
      emit('save-and-continue');
    }, 1000);

  } catch (error) {
    console.error('Save A7 error:', error.response);
    
    if (error.response?.status === 422 && error.response?.data?.data_exists) {
      dataExists.value = true;
      draftSaveMessage.value = '✓ A7 berhasil disimpan!';
      
      setTimeout(() => {
        emit('save-and-continue');
      }, 1000);
    } else {
      draftSaveError.value = error.response?.data?.message || 'Gagal menyimpan A7';
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
        <p class="text-sm text-green-800">Data A7 (Kebutuhan Pendampingan) sudah tersimpan. Silakan lanjut ke tahap berikutnya.</p>
      </div>
    </div>

    <!-- Form -->
    <div v-else class="space-y-6">
      <div class="border-b border-gray-200 pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">🤝 Kebutuhan Pendampingan</h2>
        <p class="text-gray-600">Langkah 3 dari 4 - Pilih kebutuhan pendampingan dan jelaskan</p>
      </div>

      <!-- Kebutuhan Selection -->
      <div class="space-y-3">
        <label class="block text-sm font-semibold text-gray-900">Pilih Kebutuhan Pendampingan <span class="text-red-500">*</span></label>
        <div class="space-y-3">
          <button
            v-for="option in kebutuhanOptions"
            :key="option.key"
            @click="toggleSelection(option.key)"
            class="w-full flex items-start gap-4 p-4 border-2 rounded-lg transition-all text-left"
            :class="[
              isSelected(option.key)
                ? 'border-green-500 bg-green-50'
                : 'border-gray-200 bg-white hover:border-gray-300'
            ]"
            :disabled="isSavingDraft || isSaving"
          >
            <div
              class="flex-shrink-0 w-6 h-6 rounded border-2 flex items-center justify-center mt-0.5"
              :class="[
                isSelected(option.key)
                  ? 'border-green-600 bg-green-600'
                  : 'border-gray-300 bg-white'
              ]"
            >
              <Check v-if="isSelected(option.key)" class="w-4 h-4 text-white" />
            </div>
            <span class="font-semibold text-gray-900">{{ option.label }}</span>
          </button>
        </div>
      </div>

      <!-- Permintaan Tim -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">Permintaan Tim <span class="text-red-500">*</span></label>
        <textarea
          v-model="formData.permintaan_tim"
          rows="4"
          placeholder="Jelaskan permintaan pendampingan dari tim sekolah..."
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:outline-none resize-none"
          :disabled="isSavingDraft || isSaving"
        ></textarea>
      </div>

      <!-- Waktu Pendampingan -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">Waktu Pendampingan <span class="text-red-500">*</span></label>
        <input
          v-model="formData.waktu_pendampingan"
          type="date"
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:outline-none"
          :disabled="isSavingDraft || isSaving"
        />
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
        @click="saveA7"
        :disabled="!isFormValid || isSavingDraft || isSaving"
        class="w-full px-6 py-4 bg-green-600 text-white font-bold rounded-lg transition-all"
        :class="isFormValid && !isSavingDraft && !isSaving ? 'hover:bg-green-700' : 'opacity-50 cursor-not-allowed'"
      >
        <span v-if="isSavingDraft || isSaving" class="flex items-center justify-center gap-2">
          <Loader class="w-5 h-5 animate-spin" /> Menyimpan...
        </span>
        <span v-else>✓ Simpan A7</span>
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