<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import WizardLayoutAdministrasi from '@/Components/WizardLayoutAdministrasi.vue';
import SekolahStep from './Administration/SekolahStep.vue';
import TimStep from './Administration/TimStep.vue';
import AdministrasiDasarStep from './Administration/AdministrasiDasarStep.vue';
import { Eye } from 'lucide-vue-next';

// ============================================================================
// WIZARD CONFIGURATION
// ============================================================================
const steps = [
  { id: 'dataSekolah', label: 'Data Sekolah', icon: '🏫', component: SekolahStep },
  { id: 'skTim', label: 'SK Tim & Struktur', icon: '👥', component: TimStep },
  { id: 'administrasiDasar', label: 'Administrasi Dasar', icon: '📄', component: AdministrasiDasarStep }
];

// ============================================================================
// STATE
// ============================================================================
const page = usePage();
const currentStepIndex = ref(0);
const isSavingAndNavigating = ref(false);
const submitError = ref('');

// ✅ Gunakan completedAdministrasi (bukan completedSteps)
const completedSteps = reactive({
  dataSekolah: page.props.completedAdministrasi?.dataSekolah || false,
  skTim: page.props.completedAdministrasi?.skTim || false,
  administrasiDasar: page.props.completedAdministrasi?.administrasiDasar || false
});

// ✅ Watch untuk update dari Inertia
watch(() => page.props.completedAdministrasi, (newValue) => {
  if (newValue) {
    Object.assign(completedSteps, newValue);
  }
}, { deep: true });

// ============================================================================
// LIFECYCLE - FETCH INITIAL STATUS
// ============================================================================
onMounted(async () => {
  await fetchStepStatus();
});

// ============================================================================
// METHODS - FETCH STEP STATUS
// ============================================================================
const fetchStepStatus = async () => {
  try {
    const response = await axios.get(route('administrasi.get-status'));
    
    if (response.data.completedSteps) {
      Object.assign(completedSteps, response.data.completedSteps);
    }
  } catch (error) {
    console.error('Error fetching step status:', error);
  }
};

// ============================================================================
// COMPUTED
// ============================================================================
const currentStep = computed(() => steps[currentStepIndex.value]);
const currentStepId = computed(() => currentStep.value?.id);
const canGoNext = computed(() => currentStepIndex.value < steps.length - 1);
const canGoPrevious = computed(() => currentStepIndex.value > 0);
const allStepsCompleted = computed(() => Object.values(completedSteps).every(v => v));

// ✅ Cek apakah ada data untuk ditampilkan
const hasAnyData = computed(() => Object.values(completedSteps).some(v => v));

// ============================================================================
// METHODS - NAVIGATION
// ============================================================================

const goNext = () => {
  if (canGoNext.value) {
    currentStepIndex.value++;
  }
};

const goPrevious = () => {
  if (canGoPrevious.value) {
    currentStepIndex.value--;
  }
};

// ✅ Handle navigation dari StepSidebar
const handleNavigate = ({ stepIndex }) => {
  currentStepIndex.value = stepIndex;
};

// ✅ Navigate ke halaman File Administrasi
const goToFileAdministrasi = () => {
  router.visit(route('file-administrasi'));
};

// ============================================================================
// METHODS - HANDLE SAVE & CONTINUE
// ============================================================================

const handleSaveAndContinue = async (formData) => {
  try {
    isSavingAndNavigating.value = true;
    submitError.value = '';
    
    // ✅ Refresh status dari backend
    await fetchStepStatus();
    
    // ✅ Navigate to next step
    if (canGoNext.value) {
      goNext();
    }
  } catch (error) {
    console.error('Save error:', error);
    submitError.value = 'Gagal menyimpan data. Silakan coba lagi.';
  } finally {
    isSavingAndNavigating.value = false;
  }
};

// ============================================================================
// METHODS - HANDLE FINAL SUBMIT
// ============================================================================

const handleFinalSubmit = async () => {
  try {
    isSavingAndNavigating.value = true;
    submitError.value = '';
    
    // Refresh status terakhir kali
    await fetchStepStatus();
    
    // Cek apakah semua step sudah completed
    if (!allStepsCompleted.value) {
      submitError.value = '⚠️ Harap selesaikan semua step sebelum pengiriman!';
      return;
    }
    
    // Navigate to dashboard
    router.visit(route('dashboard'));

  } catch (error) {
    console.error('Final submission error:', error);
    submitError.value = 'Gagal menyelesaikan proses. Silakan coba lagi.';
  } finally {
    isSavingAndNavigating.value = false;
  }
};
</script>

<template>
  <MainLayout>
    <!-- ✅ Pass currentStepIndex dan completedSteps -->
    <WizardLayoutAdministrasi
      :steps="steps"
      :completed="completedSteps"
      :current-step-index="currentStepIndex"
      @navigate="handleNavigate"
    >
      <!-- ✅ Button Lihat File (hanya muncul jika ada data) -->
      <div v-if="hasAnyData" class="mb-6">
        <button
          @click="goToFileAdministrasi"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors"
        >
          <Eye class="w-4 h-4" />
          Lihat File Administrasi
        </button>
      </div>

      <!-- Step Content - Pass isCompleted ke component -->
      <component
        :is="currentStep.component"
        :is-saving="isSavingAndNavigating"
        :is-completed="completedSteps[currentStepId]"
        @save-and-continue="handleSaveAndContinue"
        @final-submit="handleFinalSubmit"
      />

      <!-- Navigation Footer -->
      <div class="mt-12 pt-8 border-t border-gray-200 flex items-center justify-between gap-4">
        <!-- Previous Button -->
        <button
          @click="goPrevious"
          :disabled="!canGoPrevious || isSavingAndNavigating"
          type="button"
          class="flex items-center gap-2 px-6 py-3 rounded-lg font-semibold transition-all"
          :class="canGoPrevious && !isSavingAndNavigating
            ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
        >
          ← Sebelumnya
        </button>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-3">
          <!-- Finish Button (on last step) -->
          <button
            v-if="currentStepIndex === steps.length - 1"
            @click="handleFinalSubmit"
            :disabled="isSavingAndNavigating || !allStepsCompleted"
            type="button"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-white transition-colors"
            :class="allStepsCompleted && !isSavingAndNavigating
              ? 'bg-blue-600 hover:bg-blue-700'
              : 'bg-gray-400 cursor-not-allowed'"
          >
            {{ isSavingAndNavigating ? 'Memproses...' : 'Selesai' }}
          </button>

          <!-- Next Button (on other steps) -->
          <button
            v-else
            @click="goNext"
            :disabled="!canGoNext || isSavingAndNavigating"
            type="button"
            class="flex items-center gap-2 px-6 py-3 rounded-lg font-semibold transition-all"
            :class="canGoNext && !isSavingAndNavigating
              ? 'bg-green-600 text-white hover:bg-green-700'
              : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
          >
            Lanjut →
          </button>
        </div>
      </div>

      <!-- Error Message -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
      >
        <div v-if="submitError" class="mt-4 p-4 bg-red-50 border border-red-300 rounded-lg text-red-700 font-medium">
          {{ submitError }}
        </div>
      </transition>
    </WizardLayoutAdministrasi>
  </MainLayout>
</template>
