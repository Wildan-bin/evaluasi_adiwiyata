<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { router, usePage } from '@inertiajs/vue3';
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
const page = usePage();
const currentStepIndex = ref(0);
const isSavingAndNavigating = ref(false);
const submitError = ref('');

// Get initial completed steps from Inertia props
const completedSteps = reactive({
  a5: page.props.completedSteps?.a5 || false,
  a6: page.props.completedSteps?.a6 || false,
  a7: page.props.completedSteps?.a7 || false,
  a8: page.props.completedSteps?.a8 || false
});

// Watch untuk update dari Inertia (ketika page dirender ulang)
watch(() => page.props.completedSteps, (newValue) => {
  if (newValue) {
    Object.assign(completedSteps, newValue);
  }
}, { deep: true });

// ============================================================================
// CHECK USER ROLE - REDIRECT ADMIN TO FORM ADMIN PAGE
// ============================================================================
onMounted(() => {
    // If user is admin, redirect to form admin dashboard
    if (page.props.auth?.user?.role === 'admin') {
        router.visit(route('form-admin.index'));
    }
    
    fetchStepStatus();
});

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
    const response = await axios.get(route('form.get-status'));
    
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

const handleSaveAndContinue = async (formData) => {
  try {
    isSavingAndNavigating.value = true;
    submitError.value = '';
    
    // Refresh status dari backend
    await fetchStepStatus();
    
    // FORCE UPDATE: Trigger Inertia to update shared props
    // Ini akan membuat StepSidebar reactive update
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Navigate to next step
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
// METHODS - HANDLE FINAL SUBMIT (From A8)
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
    
    // Alert success
    // alert('✓ Proposal berhasil dikirim! Terima kasih.');
    
    // Navigate to FileUser page instead of dashboard
    router.visit(route('file-user.index'));

  } catch (error) {
    console.error('Final submission error:', error);
    submitError.value = 'Gagal mengirim proposal. Silakan coba lagi.';
  } finally {
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
            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ isSavingAndNavigating ? 'Memproses...' : 'Lihat File' }}
          </button>

          <!-- Next Button (on A5-A7) -->
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
    </WizardLayout>
  </MainLayout>
</template>
