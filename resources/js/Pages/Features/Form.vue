<script setup>
import { ref, reactive, computed } from 'vue';
import axios from 'axios';
import MainLayout from '@/Layouts/MainLayout.vue';
import WizardLayout from '@/Components/WizardLayout.vue';
import A5 from './Submission/A5.vue';
import A6 from './Submission/A6.vue';
import A7 from './Submission/A7.vue';
import A8 from './Submission/A8.vue';

// ============================================================================
// WIZARD CONFIGURATION
// ============================================================================
const steps = [
  { id: 'a5', label: 'Rencana & Evaluasi PBLHS', icon: '📋', component: A5 },
  { id: 'a6', label: 'Self Assessment', icon: '✓', component: A6 },
  { id: 'a7', label: 'Kebutuhan Pendampingan', icon: '🤝', component: A7 },
  { id: 'a8', label: 'Pernyataan & Persetujuan', icon: '📝', component: A8 }
];

// ============================================================================
// STATE
// ============================================================================
const currentStepIndex = ref(0);
const isSavingAndNavigating = ref(false);
const submitError = ref('');
const completedSteps = reactive({
  a5: false,
  a6: false,
  a7: false,
  a8: false
});

// ============================================================================
// COMPUTED
// ============================================================================
const currentStep = computed(() => steps[currentStepIndex.value]);
const currentStepId = computed(() => currentStep.value?.id);
const canGoNext = computed(() => currentStepIndex.value < steps.length - 1);
const canGoPrevious = computed(() => currentStepIndex.value > 0);
const allStepsCompleted = computed(() => Object.values(completedSteps).every(v => v));

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

// ============================================================================
// METHODS - HANDLE SAVE & CONTINUE (From Child Components)
// ============================================================================

const handleSaveAndContinue = async () => {
  // Mark current step as completed
  completedSteps[currentStepId.value] = true;

  // Navigate to next step
  if (canGoNext.value) {
    goNext();
  }
};

// ============================================================================
// METHODS - HANDLE FINAL SUBMIT (From A8)
// ============================================================================

const handleFinalSubmit = async () => {
  if (!allStepsCompleted.value) {
    submitError.value = '⚠️ Harap selesaikan semua step sebelum pengiriman!';
    return;
  }

  isSavingAndNavigating.value = true;
  submitError.value = '';

  try {
    // Mark A8 as completed
    completedSteps.a8 = true;

    // Optional: Call backend to finalize submission
    // await axios.post(route('form.submit-all'));

    alert('✓ Proposal berhasil dikirim! Terima kasih.');
    window.location.href = route('dashboard');

  } catch (error) {
    console.error('Final submission error:', error);
    submitError.value = 'Gagal mengirim proposal. Silakan coba lagi.';
    isSavingAndNavigating.value = false;
  }
};
</script>

<template>
  <MainLayout>
    <WizardLayout
      :steps="steps"
      :completed="completedSteps"
    >
      <!-- Step Content - Dynamic Component Rendering -->
      <component
        :is="currentStep.component"
        :is-saving="isSavingAndNavigating"
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
          <!-- Submit Button (on A8) -->
          <button
            v-if="currentStepIndex === steps.length - 1"
            @click="handleFinalSubmit"
            :disabled="isSavingAndNavigating"
            type="button"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-white"
            :class="isSavingAndNavigating
              ? 'bg-gray-400 cursor-not-allowed'
              : 'bg-green-600 hover:bg-green-700'"
          >
            ✓ Kirim Proposal
          </button>

          <!-- Next Button (on A5-A7) -->
          <button
            v-else
            @click="handleSaveAndContinue"
            :disabled="isSavingAndNavigating"
            type="button"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold"
            :class="!isSavingAndNavigating
              ? 'bg-green-600 text-white hover:bg-green-700 shadow-lg'
              : 'bg-gray-400 text-white cursor-not-allowed'"
          >
            Simpan & Lanjut →
          </button>
        </div>
      </div>

      <!-- Error Message -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
      >
        <div
          v-if="submitError"
          class="mt-6 p-4 bg-red-50 border border-red-300 rounded-lg text-red-700 font-medium"
        >
          ❌ {{ submitError }}
        </div>
      </transition>
    </WizardLayout>
  </MainLayout>
</template>
