<script setup>
import { computed } from 'vue';
import StepSidebarAdministrasi from './StepSidebarAdministrasi.vue';

// ✅ Terima props dari parent (Administration.vue)
const props = defineProps({
  steps: {
    type: Array,
    required: true
  },
  completed: {
    type: Object,
    required: true
  },
  currentStepIndex: {
    type: Number,
    required: true,
    default: 0
  }
});

// ✅ Emit navigate event ke parent
const emit = defineEmits(['navigate']);

const handleNavigate = (payload) => {
  emit('navigate', payload);
};

const currentStep = computed(() => {
  return props.steps[props.currentStepIndex]?.label || 'Loading...';
});

// ✅ Calculate progress percentage
const progressPercentage = computed(() => {
  const completedCount = Object.values(props.completed).filter(Boolean).length;
  return Math.round((completedCount / props.steps.length) * 100);
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

    <!-- Main Layout -->
    <div class="flex min-h-[calc(100vh-80px)]">
      <!-- Sidebar Progress - ✅ Pass currentStepIndex juga -->
      <StepSidebarAdministrasi 
        :steps="steps" 
        :completed="completed"
        :current-step-index="currentStepIndex"
        @navigate="handleNavigate"
      />

      <!-- Main Content -->
      <main class="flex-1 px-6 lg:px-8 py-8 mt-20">
        <div class="max-w-4xl mx-auto">
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
</style>