<script setup>
import { computed } from 'vue';
import { Check, Lock } from 'lucide-vue-next';

// ✅ Hanya terima props, TIDAK baca page.props lagi
const props = defineProps({
  steps: {
    type: Array,
    required: true,
    default: () => []
  },
  completed: {
    type: Object,
    required: true,
    default: () => ({})
  },
  currentStepIndex: {
    type: Number,
    default: 0
  }
});

// ✅ Emit untuk navigasi (let parent handle routing)
const emit = defineEmits(['navigate']);

// Check if step is accessible
const isStepAccessible = (stepIndex) => {
  // Step pertama selalu accessible
  if (stepIndex === 0) return true;
  
  // Step berikutnya accessible jika step sebelumnya completed
  const previousStep = props.steps[stepIndex - 1];
  return props.completed[previousStep?.id] || false;
};

// Navigate to step
const goToStep = (step, stepIndex) => {
  if (!isStepAccessible(stepIndex)) {
    return; // Don't navigate if not accessible
  }

  // ✅ Emit ke parent, biarkan parent yang handle routing
  emit('navigate', { step, stepIndex });
};

// Get step status
const getStepStatus = (step, stepIndex) => {
  const isCompleted = props.completed[step.id] || false;
  const isCurrent = props.currentStepIndex === stepIndex;
  const isAccessible = isStepAccessible(stepIndex);

  return {
    isCompleted,
    isCurrent,
    isAccessible,
    isLocked: !isAccessible
  };
};
</script>

<template>
  <aside class="hidden lg:block w-80 bg-white border-r border-gray-200 mt-20 sticky top-20 h-[calc(100vh-80px)] overflow-y-auto">
    <div class="p-6">
      <h2 class="text-lg font-bold text-gray-900 mb-6">Progress Administrasi</h2>
      
      <div class="space-y-4">
        <div
          v-for="(step, index) in steps"
          :key="step.id"
          @click="goToStep(step, index)"
          :class="[
            'relative flex items-start gap-4 p-4 rounded-xl transition-all duration-300 cursor-pointer',
            getStepStatus(step, index).isCurrent 
              ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-500 shadow-md' 
              : getStepStatus(step, index).isCompleted
              ? 'bg-green-50/50 border border-green-200 hover:bg-green-50'
              : getStepStatus(step, index).isAccessible
              ? 'bg-gray-50 border border-gray-200 hover:bg-gray-100'
              : 'bg-gray-50/50 border border-gray-200 opacity-60 cursor-not-allowed'
          ]"
        >
          <!-- Step Number/Icon -->
          <div
            :class="[
              'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300',
              getStepStatus(step, index).isCompleted
                ? 'bg-green-500 text-white'
                : getStepStatus(step, index).isCurrent
                ? 'bg-green-600 text-white ring-4 ring-green-100'
                : getStepStatus(step, index).isAccessible
                ? 'bg-white border-2 border-gray-300 text-gray-600'
                : 'bg-gray-200 text-gray-400'
            ]"
          >
            <Check 
              v-if="getStepStatus(step, index).isCompleted" 
              :size="20" 
              class="stroke-[3]"
            />
            <Lock 
              v-else-if="getStepStatus(step, index).isLocked" 
              :size="18"
            />
            <span v-else class="text-lg">{{ step.icon }}</span>
          </div>

          <!-- Step Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h3 
                :class="[
                  'font-semibold text-sm',
                  getStepStatus(step, index).isCurrent
                    ? 'text-green-900'
                    : getStepStatus(step, index).isCompleted
                    ? 'text-green-700'
                    : 'text-gray-700'
                ]"
              >
                {{ step.label }}
              </h3>
              
              <!-- Current Badge -->
              <span
                v-if="getStepStatus(step, index).isCurrent"
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white"
              >
                Sedang Aktif
              </span>
            </div>
            
            <!-- Status Text -->
            <p class="text-xs text-gray-500">
              <span v-if="getStepStatus(step, index).isCompleted" class="text-green-600 font-medium">
                ✓ Selesai
              </span>
              <span v-else-if="getStepStatus(step, index).isCurrent" class="text-green-600 font-medium">
                Sedang dikerjakan
              </span>
              <span v-else-if="getStepStatus(step, index).isLocked" class="text-gray-400">
                Terkunci - Selesaikan step sebelumnya
              </span>
              <span v-else class="text-gray-500">
                Belum dimulai
              </span>
            </p>
          </div>

          <!-- Connector Line -->
          <div
            v-if="index < steps.length - 1"
            :class="[
              'absolute left-[2.25rem] top-[3.5rem] w-0.5 h-8 transition-colors duration-300',
              getStepStatus(step, index).isCompleted
                ? 'bg-green-500'
                : 'bg-gray-200'
            ]"
          ></div>
        </div>
      </div>

      <!-- Progress Summary -->
      <div class="mt-8 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold text-gray-700">Total Progress</span>
          <span class="text-sm font-bold text-green-600">
            {{ Object.values(completed).filter(Boolean).length }}/{{ steps.length }}
          </span>
        </div>
        <div class="w-full bg-green-200/50 rounded-full h-2.5 overflow-hidden">
          <div 
            class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-500"
            :style="{ 
              width: `${(Object.values(completed).filter(Boolean).length / steps.length) * 100}%` 
            }"
          ></div>
        </div>
      </div>
    </div>
  </aside>
</template>

<style scoped>
/* Smooth scrollbar */
aside::-webkit-scrollbar {
  width: 6px;
}

aside::-webkit-scrollbar-track {
  background: #f1f1f1;
}

aside::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>