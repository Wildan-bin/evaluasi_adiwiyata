<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import StepSidebar from './StepSidebar.vue';
import { ChevronLeft, ChevronRight, Save, AlertCircle } from 'lucide-vue-next';

const page = usePage();

const steps = [
  { id: 'a5', label: 'Rencana & Evaluasi PBLHS', icon: '📋' },
  { id: 'a6', label: 'Self Assessment', icon: '✓' },
  { id: 'a7', label: 'Kebutuhan Pendampingan', icon: '🤝' },
  { id: 'a8', label: 'Pernyataan & Persetujuan', icon: '📝' }
];

// Get current step from URL or route name
const currentStepId = computed(() => {
  const routeName = page.component || '';
  const match = routeName.match(/A[5-8]/);
  return match ? `a${match[0].charAt(1).toLowerCase()}` : 'a5';
});

const currentStep = computed(() => {
  return steps.find(s => s.id === currentStepId.value)?.label || 'Loading...';
});

const currentStepIndex = computed(() => {
  return steps.findIndex(s => s.id === currentStepId.value);
});

const completedSteps = computed(() => page.props.completedSteps || {
  a5: false,
  a6: false,
  a7: false,
  a8: false
});

// Navigation helpers
const canGoPrevious = computed(() => currentStepIndex.value > 0);
const canGoNext = computed(() => currentStepIndex.value < steps.length - 1);
const allStepsCompleted = computed(() => {
  return steps.every(s => completedSteps.value[s.id]);
});

const isLoading = ref(false);

// Navigation using router.visit
const goToPrevious = () => {
  if (canGoPrevious.value) {
    const prevStep = steps[currentStepIndex.value - 1];
    isLoading.value = true;
    router.visit(route(`submission.${prevStep.id}`), {
      preserveScroll: true,
      onFinish: () => {
        isLoading.value = false;
      }
    });
  }
};

const goToNext = () => {
  if (canGoNext.value) {
    const nextStep = steps[currentStepIndex.value + 1];
    isLoading.value = true;
    router.visit(route(`submission.${nextStep.id}`), {
      preserveScroll: true,
      onFinish: () => {
        isLoading.value = false;
      }
    });
  }
};

// Emit events untuk parent form component
const emit = defineEmits(['save', 'submit']);

const handleSaveAndContinue = () => {
  emit('save', { goNext: true });
};

const handleSaveDraft = () => {
  emit('save', { goNext: false });
};

const handleFinalSubmit = () => {
  emit('submit');
};

// Calculate progress percentage
const progressPercentage = computed(() => {
  return Math.round(((currentStepIndex.value + 1) / steps.length) * 100);
});
</script>

<template>
  <div class="min-h-screen bg-[#F5F5F5]">
    <!-- Header -->
    <div class="fixed w-full z-40 bg-white/95 backdrop-blur-lg border-b border-gray-200/50 shadow-sm">
      <div class="px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ currentStep }}</h1>
            <p class="text-sm text-gray-500 mt-1">
              Langkah {{ currentStepIndex + 1 }} dari {{ steps.length }}
            </p>
          </div>
          <div class="text-right">
            <p class="text-sm font-semibold text-green-600">{{ progressPercentage }}% Selesai</p>
            <div class="mt-2 w-48 bg-gray-200 rounded-full h-2 overflow-hidden">
              <div 
                class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-500"
                :style="{ width: progressPercentage + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex">
      <!-- Sidebar Progress -->
      <StepSidebar 
        :steps="steps" 
        :completed="completedSteps"
      />

      <!-- Main Content -->
      <main class="flex-1 px-6 lg:px-8 py-8 mt-20">
        <div class="max-w-4xl mx-auto">
          <!-- Loading State -->
          <transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-300"
          >
            <div v-if="isLoading" class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
              <div class="bg-white rounded-xl shadow-2xl p-6 flex flex-col items-center gap-4">
                <div class="w-12 h-12 border-4 border-green-200 border-t-green-600 rounded-full animate-spin"></div>
                <p class="font-semibold text-gray-900">Memuat halaman...</p>
              </div>
            </div>
          </transition>

          <!-- Content Slot -->
          <slot />  
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Smooth transitions */
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Gradient text effect */
.text-gradient {
  background: linear-gradient(to right, #059669, #10b981);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
</style>