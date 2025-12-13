<script setup>
import { ref, computed } from 'vue';
import { ChevronDown, AlertCircle } from 'lucide-vue-next';
import FileUploadCard from './FileUploadCard.vue';

const props = defineProps({
  index: Number,
  title: String,
  description: String,
  modelValue: {
    type: Object,
    default: () => ({ skor: null, narasi: '', bukti: null })
  },
  required: Boolean,
  errors: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:modelValue']);

const isExpanded = ref(false);

const scoreOptions = [
  { value: 1, label: 'Tidak Ada (1)', color: 'red', description: 'Belum ada kegiatan/data' },
  { value: 2, label: 'Dalam Perencanaan (2)', color: 'orange', description: 'Masih dalam tahap rencana' },
  { value: 3, label: 'Kurang (3)', color: 'yellow', description: 'Baru dimulai/pelaksanaan terbatas' },
  { value: 4, label: 'Cukup (4)', color: 'blue', description: 'Sudah berjalan cukup baik' },
  { value: 5, label: 'Baik (5)', color: 'green', description: 'Sudah berjalan dengan baik' }
];

const updateField = (field, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value
  });
};

const scoreLabel = computed(() => {
  return scoreOptions.find(o => o.value === props.modelValue?.skor)?.label || 'Pilih Skor';
});

const scoreColor = computed(() => {
  const option = scoreOptions.find(o => o.value === props.modelValue?.skor);
  return option?.color || 'gray';
});

const scoreDescription = computed(() => {
  const option = scoreOptions.find(o => o.value === props.modelValue?.skor);
  return option?.description || '';
});

const isComplete = computed(() => {
  return props.modelValue?.skor !== null && 
         props.modelValue?.narasi?.trim() !== '' && 
         props.modelValue?.bukti !== null;
});

const getColorClasses = (color) => {
  const colorMap = {
    red: 'border-red-300 bg-red-50 text-red-700',
    orange: 'border-orange-300 bg-orange-50 text-orange-700',
    yellow: 'border-yellow-300 bg-yellow-50 text-yellow-700',
    blue: 'border-blue-300 bg-blue-50 text-blue-700',
    green: 'border-green-300 bg-green-50 text-green-700',
    gray: 'border-gray-300 bg-gray-50 text-gray-700'
  };
  return colorMap[color] || colorMap.gray;
};

const getScoreBadgeClasses = (color) => {
  const colorMap = {
    red: 'bg-red-100 text-red-700',
    orange: 'bg-orange-100 text-orange-700',
    yellow: 'bg-yellow-100 text-yellow-700',
    blue: 'bg-blue-100 text-blue-700',
    green: 'bg-green-100 text-green-700',
    gray: 'bg-gray-100 text-gray-600'
  };
  return colorMap[color] || colorMap.gray;
};
</script>

<template>
  <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200 bg-white">
    <!-- Header (Always Visible) -->
    <button
      @click="isExpanded = !isExpanded"
      class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors"
    >
      <div class="text-left flex-1">
        <div class="flex items-center gap-4">
          <!-- Index Badge -->
          <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 border-2 border-green-300">
            <span class="font-bold text-green-700 text-sm">{{ index }}</span>
          </div>

          <!-- Title & Description -->
          <div class="min-w-0">
            <h3 class="font-semibold text-gray-900 text-base">{{ title }}</h3>
            <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ description }}</p>
          </div>
        </div>
      </div>

      <!-- Status & Score Badge -->
      <div class="flex items-center gap-4 flex-shrink-0 ml-4">
        <!-- Completion Indicator -->
        <div v-if="isComplete" class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 border border-green-300">
          <span class="inline-flex items-center justify-center w-4 h-4 bg-green-600 rounded-full">
            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </span>
          <span class="text-xs font-semibold text-green-700">Lengkap</span>
        </div>

        <!-- Score Badge -->
        <span 
          v-if="modelValue?.skor !== null"
          class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold"
          :class="getScoreBadgeClasses(scoreColor)"
        >
          {{ scoreLabel }}
        </span>
        <span 
          v-else
          class="inline-flex px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-600"
        >
          Belum Diisi
        </span>

        <!-- Chevron Icon -->
        <ChevronDown 
          class="w-5 h-5 text-gray-600 transition-transform flex-shrink-0"
          :class="{ 'rotate-180': isExpanded }"
        />
      </div>
    </button>

    <!-- Expandable Content -->
    <transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="max-h-0 opacity-0 overflow-hidden"
      enter-to-class="max-h-[2000px] opacity-100 overflow-visible"
      leave-active-class="transition-all duration-300 ease-out"
      leave-from-class="max-h-[2000px] opacity-100"
      leave-to-class="max-h-0 opacity-0"
    >
      <div v-if="isExpanded" class="border-t border-gray-200 p-6 bg-gradient-to-br from-gray-50 to-white space-y-6">
        
        <!-- Score Selection Section -->
        <div class="space-y-3">
          <label class="block text-sm font-semibold text-gray-900">
            Skor Pencapaian
            <span class="text-red-500">*</span>
          </label>
          
          <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
            <button
              v-for="option in scoreOptions"
              :key="option.value"
              @click="updateField('skor', option.value)"
              class="relative p-3 rounded-lg border-2 font-semibold text-sm transition-all duration-200 group"
              :class="[
                modelValue?.skor === option.value
                  ? `border-${option.color}-500 bg-${option.color}-50 text-${option.color}-700 shadow-md`
                  : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50'
              ]"
              :title="option.description"
            >
              <div class="text-center">
                <p class="font-bold text-base">{{ option.value }}</p>
                <p class="text-xs mt-0.5">{{ option.label.split('(')[0] }}</p>
              </div>
              
              <!-- Tooltip on hover -->
              <div class="hidden group-hover:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap z-10">
                {{ option.description }}
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
              </div>
            </button>
          </div>

          <!-- Error Message for Score -->
          <div v-if="errors?.skor" class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
            <AlertCircle class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-red-700">{{ errors.skor }}</p>
          </div>

          <!-- Score Description -->
          <div v-if="scoreDescription" class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700 font-medium">{{ scoreDescription }}</p>
          </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200"></div>

        <!-- Narrative/Explanation Section -->
        <div class="space-y-3">
          <label class="block text-sm font-semibold text-gray-900">
            Narasi & Penjelasan
            <span class="text-red-500">*</span>
          </label>
          
          <textarea
            :value="modelValue?.narasi || ''"
            @input="updateField('narasi', $event.target.value)"
            placeholder="Jelaskan kondisi, tantangan, dan bukti pencapaian untuk indikator ini..."
            rows="5"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:outline-none resize-none transition-colors"
            :class="{
              'border-red-300 focus:border-red-500': errors?.narasi
            }"
          ></textarea>

          <div class="flex items-center justify-between text-xs text-gray-600">
            <span>Minimal 50 karakter, minimal 1 paragraf</span>
            <span>{{ (modelValue?.narasi || '').length }} / 1000</span>
          </div>

          <!-- Error Message for Narasi -->
          <div v-if="errors?.narasi" class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
            <AlertCircle class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-red-700">{{ errors.narasi }}</p>
          </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200"></div>

        <!-- File Upload Section -->
        <div class="space-y-3">
          <FileUploadCard
            label="Upload Bukti (PDF)"
            description="Upload dokumen bukti pendukung untuk indikator ini. Format: PDF, Maksimal: 5 MB"
            accept="application/pdf"
            :max-size="5 * 1024 * 1024"
            :model-value="modelValue?.bukti"
            @update:model-value="updateField('bukti', $event)"
            :required="required"
            :error="errors?.bukti"
          />
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200"></div>

        <!-- Summary Box -->
        <div 
          class="p-4 rounded-lg border-2 transition-all"
          :class="getColorClasses(scoreColor)"
        >
          <h4 class="font-semibold mb-2">Ringkasan Indikator</h4>
          <ul class="space-y-2 text-sm">
            <li class="flex items-center gap-2">
              <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white bg-opacity-50 text-xs font-bold">
                ✓
              </span>
              <span><strong>Skor:</strong> {{ scoreLabel }}</span>
            </li>
            <li class="flex items-center gap-2">
              <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white bg-opacity-50 text-xs font-bold">
                ✓
              </span>
              <span><strong>Narasi:</strong> {{ (modelValue?.narasi || '').length > 0 ? 'Sudah diisi' : 'Belum diisi' }}</span>
            </li>
            <li class="flex items-center gap-2">
              <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white bg-opacity-50 text-xs font-bold">
                ✓
              </span>
              <span><strong>Bukti:</strong> {{ modelValue?.bukti ? modelValue.bukti.name : 'Belum diupload' }}</span>
            </li>
          </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-2">
          <button
            @click="isExpanded = false"
            class="flex-1 px-4 py-2.5 rounded-lg font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors"
          >
            Tutup
          </button>
          <button
            v-if="isComplete"
            @click="isExpanded = false"
            class="flex-1 px-4 py-2.5 rounded-lg font-semibold bg-green-600 text-white hover:bg-green-700 transition-colors shadow-lg"
          >
            Selesai ✓
          </button>
          <button
            v-else
            disabled
            class="flex-1 px-4 py-2.5 rounded-lg font-semibold bg-gray-100 text-gray-400 cursor-not-allowed"
          >
            Lengkapi Data
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
/* Smooth transitions */
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hide scrollbar untuk tooltip */
::-webkit-scrollbar {
  display: none;
}
</style>