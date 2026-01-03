<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Check, Clock, Circle } from 'lucide-vue-next';

const props = defineProps({
  steps: {
    type: Array,
    required: true
  },
  completed: {
    type: Object,
    default: () => ({})
  }
});

const page = usePage();

// Get completedSteps from Inertia shared data (from middleware)
const completedSteps = computed(() => {
  // Prioritas: props.completed (jika diberikan dari parent), 
  // fallback ke page.props.completedSteps dari middleware
  const propsCompleted = props.completed || {};
  const sharedCompleted = page.props.completedSteps || {};
  
  // Merge: jika props.completed punya nilai true, gunakan itu; 
  // jika tidak, gunakan dari sharedCompleted
  return {
    a5: propsCompleted.a5 || sharedCompleted.a5 || false,
    a6: propsCompleted.a6 || sharedCompleted.a6 || false,
    a7: propsCompleted.a7 || sharedCompleted.a7 || false,
    a8: propsCompleted.a8 || sharedCompleted.a8 || false,
  };
});

// Extract step ID dari component name
const currentStepId = computed(() => {
  const componentName = page.component || '';
  // Match A5, A6, A7, A8 dari component name
  const match = componentName.match(/A[5-8]/);
  return match ? `a${match[0].charAt(1).toLowerCase()}` : 'a5';
});

const getStepStatus = (stepId) => {
  if (completedSteps.value[stepId]) return 'completed';
  if (currentStepId.value === stepId) return 'active';
  return 'pending';
};

const completedCount = computed(() => {
  return Object.values(completedSteps.value).filter(v => v).length;
});

// Navigate to step using router.visit
const navigateToStep = (stepId) => {
  router.visit(route(`submission.${stepId}`), {
    preserveScroll: true,
    preserveState: false
  });
};
</script>

<template>
  <aside class="hidden lg:block w-72 bg-white border-r border-gray-200 sticky top-20 h-[calc(100vh-80px)] overflow-y-auto">
    <div class="p-6 space-y-4">
      <!-- Progress Overview -->
      <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
        <p class="text-sm text-gray-600 mb-2">Kemajuan Pengisian</p>
        <p class="text-3xl font-bold text-green-600">{{ completedCount }}/{{ steps.length }}</p>
        <div class="mt-3 bg-white rounded-full h-2 overflow-hidden">
          <div 
            class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-500"
            :style="{ width: (completedCount / steps.length * 100) + '%' }"
          ></div>
        </div>
      </div>

      <!-- Steps List -->
      <div class="space-y-2">
        <template v-for="(step, index) in steps" :key="step.id">
          <button
            @click="navigateToStep(step.id)"
            type="button"
            class="w-full flex items-start gap-3 p-4 rounded-lg transition-all duration-200 group"
            :class="{
              'bg-green-50 border-2 border-green-200': getStepStatus(step.id) === 'active',
              'bg-gray-50 hover:bg-gray-100': getStepStatus(step.id) !== 'active',
              'opacity-60 cursor-not-allowed': index > completedCount
            }"
            :disabled="index > completedCount"
          >
            <!-- Status Icon -->
            <div
              class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5 transition-all duration-200"
              :class="{
                'bg-green-600 text-white shadow-md': getStepStatus(step.id) === 'completed',
                'bg-green-100 text-green-600 border-2 border-green-600 animate-pulse': getStepStatus(step.id) === 'active',
                'bg-gray-300 text-gray-500': getStepStatus(step.id) === 'pending'
              }"
            >
              <Check v-if="getStepStatus(step.id) === 'completed'" class="w-5 h-5" />
              <Clock v-else-if="getStepStatus(step.id) === 'active'" class="w-5 h-5" />
              <Circle v-else class="w-5 h-5" />
            </div>

            <!-- Step Info -->
            <div class="text-left flex-1">
              <p class="font-semibold text-gray-900 text-sm">{{ step.label }}</p>
              <p class="text-xs text-gray-500 mt-0.5">Step {{ index + 1 }}</p>
            </div>

            <!-- Icon -->
            <span class="text-xl group-hover:scale-110 transition-transform duration-200">{{ step.icon }}</span>
          </button>
        </template>
      </div>

      <!-- Status Message -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-300"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="completedCount === steps.length" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
          <p class="text-sm font-semibold text-green-700">✓ Siap untuk Pengiriman!</p>
          <p class="text-xs text-green-600 mt-1">Semua step telah selesai diisi</p>
        </div>
      </transition>

      <!-- Divider -->
      <div class="border-t border-gray-200 pt-4 mt-4">
        <p class="text-xs text-gray-600 px-2">
          💡 <strong>Tips:</strong> Selesaikan setiap langkah untuk melanjutkan ke step berikutnya.
        </p>
      </div>
    </div>
  </aside>
</template>

<style scoped>
aside::-webkit-scrollbar {
  width: 6px;
}

aside::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #059669, #10b981);
  border-radius: 10px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

/* Pulse animation untuk active step */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>