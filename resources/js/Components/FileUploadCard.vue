<script setup>
import { ref, computed } from 'vue';
import { Upload, X, File, CheckCircle, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
  label: {
    type: String,
    default: 'Upload File'
  },
  description: {
    type: String,
    default: ''
  },
  desc: {
    type: String,
    default: ''
  },
  accept: {
    type: String,
    default: 'application/pdf'
  },
  maxSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB
  },
  // FIX: Gunakan [Object, null] bukan [File, Object, null]
  modelValue: {
    type: [Object, null],
    default: null
  },
  required: Boolean,
  error: String,
  disabled: Boolean
});

const emit = defineEmits(['update:modelValue']);

// ============================================================================
// STATE - Only UI state (drag visual feedback)
// ============================================================================
const isDragging = ref(false);
const validationError = ref('');

// Ref untuk hidden file input - OUTSIDE upload zone
const fileInput = ref(null);

// ============================================================================
// COMPUTED - Pure derived state from props
// ============================================================================

const hasFile = computed(() => {
  return props.modelValue !== null && props.modelValue !== undefined;
});

const fileName = computed(() => {
  if (!hasFile.value) return null;
  return props.modelValue?.name || 'File tersimpan';
});

const fileSizeMB = computed(() => {
  if (!hasFile.value) return 0;
  return (props.modelValue?.size / 1024 / 1024).toFixed(2);
});

const displayError = computed(() => {
  return props.error || validationError.value;
});

const uploadZoneClass = computed(() => {
  const baseClass = 'relative border-2 border-dashed rounded-lg p-6 transition-all duration-200';

  if (props.disabled) {
    return `${baseClass} border-gray-200 bg-gray-100 cursor-not-allowed opacity-50`;
  }

  if (displayError.value) {
    return `${baseClass} border-red-400 bg-red-50 cursor-pointer hover:bg-red-100`;
  }

  if (isDragging.value) {
    return `${baseClass} border-green-500 bg-green-50 shadow-lg cursor-copy`;
  }

  return `${baseClass} border-gray-300 bg-gray-50 cursor-pointer hover:bg-gray-100 hover:border-gray-400`;
});

// ============================================================================
// VALIDATION - UX-only (type & size check only)
// No backend logic here - just quick UX feedback
// ============================================================================

const validateFile = (file) => {
  if (!file) {
    return { valid: false, error: 'File diperlukan' };
  }

  // Type check - UX validation only
  if (file.type !== props.accept) {
    return {
      valid: false,
      error: `Hanya PDF yang diperbolehkan. Anda memilih: ${file.type || 'unknown'}`
    };
  }

  // Size check - UX validation only
  if (file.size > props.maxSize) {
    const maxMB = (props.maxSize / 1024 / 1024).toFixed(0);
    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
    return {
      valid: false,
      error: `File terlalu besar: ${sizeMB}MB (max: ${maxMB}MB)`
    };
  }

  return { valid: true };
};

// ============================================================================
// FILE PROCESSING - Single entry point for all file sources
// Synchronous, no setTimeout, no loading state
// ============================================================================

const processFile = (file) => {
  if (!file) return;

  // Clear previous error immediately
  validationError.value = '';

  // Validate synchronously
  const { valid, error } = validateFile(file);

  if (!valid) {
    validationError.value = error;
    resetFileInput();
    return;
  }

  // Emit raw File object to parent - component stays dumb
  // Parent (Form.vue) will handle building FormData
  validationError.value = '';
  emit('update:modelValue', file);
};

// ============================================================================
// EVENT HANDLERS
// ============================================================================

/**
 * Handle file input change event
 * This is the main entry point for file selection via hidden input
 */
const handleFileSelect = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    processFile(file);
  }
};

/**
 * Handle drag over
 */
const handleDragOver = (event) => {
  event.preventDefault();
  event.stopPropagation();
  if (!props.disabled) {
    isDragging.value = true;
  }
};

/**
 * Handle drag leave
 */
const handleDragLeave = (event) => {
  event.preventDefault();
  event.stopPropagation();
  isDragging.value = false;
};

/**
 * Handle drop event
 */
const handleDrop = (event) => {
  event.preventDefault();
  event.stopPropagation();
  isDragging.value = false;

  const file = event.dataTransfer?.files?.[0];
  if (file) {
    processFile(file);
  }
};

/**
 * Trigger file input click via requestAnimationFrame
 * Prevents event timing conflicts and ensures reliable trigger
 */
const triggerFileInput = () => {
  if (props.disabled) return;

  // Gunakan setTimeout kecil untuk memastikan event cycle selesai
  // sebelum trigger click
  if (fileInput.value) {
    fileInput.value.click();
  }
};

/**
 * Remove selected file
 */
const removeFile = () => {
  validationError.value = '';
  emit('update:modelValue', null);
  resetFileInput();
};

/**
 * Reset file input state
 */
const resetFileInput = () => {
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};
</script>

<template>
  <div class="space-y-3">
    <!-- Label with Required Indicator -->
    <div v-if="label" class="flex items-center gap-2">
      <label class="text-sm font-semibold text-gray-900">{{ label }}</label>
      <span v-if="required" class="text-red-500 font-bold">*</span>
    </div>

    <!-- Description -->
    <p v-if="description" class="text-xs text-gray-600 leading-relaxed">
      {{ description }}
    </p>
    <p v-if="desc" class="text-sm italic text-gray-600 leading-relaxed">
      {{ desc }}
    </p>

    <!-- CRITICAL: Hidden file input is OUTSIDE upload zone and COMPLETELY HIDDEN -->
    <!-- Key fixes:
         1. class="hidden" - Make sure input is NOT visible
         2. @change="handleFileSelect" - Reliable change detection
         3. No @click.stop - Allow normal event bubbling
         4. accept="application/pdf" - Only accept PDF files
    -->
    <input
      ref="fileInput"
      type="file"
      accept="application/pdf"
      @change="handleFileSelect"
      class="hidden"
      :disabled="disabled"
      aria-hidden="true"
    />

    <!-- Upload Zone (Drag/Drop + Click) -->
    <div
      @click="triggerFileInput"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      :class="uploadZoneClass"
      role="button"
      :tabindex="disabled ? -1 : 0"
      @keydown.enter="triggerFileInput"
      @keydown.space.prevent="triggerFileInput"
    >
      <!-- Empty State - No file selected -->
      <template v-if="!hasFile">
        <div class="text-center py-4">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 mb-3">
            <Upload class="w-6 h-6 text-gray-600" />
          </div>
          <p class="text-sm font-semibold text-gray-900 mb-1">
            Klik atau drag PDF di sini
          </p>
          <p class="text-xs text-gray-600">
            Max: {{ (maxSize / 1024 / 1024).toFixed(0) }}MB
          </p>
        </div>
      </template>

      <!-- File Selected State -->
      <template v-else>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
              <File class="w-6 h-6 text-red-600" />
            </div>
            <div class="min-w-0">
              <p class="text-sm font-semibold text-gray-900 truncate">
                {{ fileName }}
              </p>
              <p class="text-xs text-gray-600">
                {{ fileSizeMB }} MB
              </p>
            </div>
          </div>
          <button
            @click.stop="removeFile"
            type="button"
            class="flex-shrink-0 p-2 hover:bg-gray-200 rounded-lg transition-colors duration-200"
            :disabled="disabled"
            title="Hapus file"
          >
            <X class="w-5 h-5 text-gray-600 hover:text-red-600 transition-colors" />
          </button>
        </div>
      </template>

      <!-- Drag Over Visual Feedback -->
      <transition
        enter-active-class="transition-opacity duration-150"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isDragging && !hasFile"
          class="absolute inset-0 bg-green-500 bg-opacity-10 flex items-center justify-center rounded-lg pointer-events-none"
        >
          <div class="text-center">
            <Upload class="w-8 h-8 text-green-600 mx-auto mb-2 animate-bounce" />
            <p class="text-sm font-semibold text-green-700">Drop file di sini</p>
          </div>
        </div>
      </transition>
    </div>

    <!-- Success Message -->
    <transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0 translate-x-2"
      enter-to-class="opacity-100 translate-x-0"
      leave-active-class="transition-all duration-300"
      leave-from-class="opacity-100 translate-x-0"
      leave-to-class="opacity-0 translate-x-2"
    >
      <div
        v-if="hasFile && !displayError"
        class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg"
      >
        <CheckCircle class="w-5 h-5 text-green-600 flex-shrink-0" />
        <p class="text-sm text-green-700 font-medium">✓ File siap dikirim</p>
      </div>
    </transition>

    <!-- Error Message -->
    <transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-300"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="displayError"
        class="flex items-start gap-3 p-3 bg-red-50 border border-red-300 rounded-lg"
      >
        <AlertCircle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
        <p class="text-sm text-red-700 font-medium">{{ displayError }}</p>
      </div>
    </transition>
  </div>
</template>

<style scoped>
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}

.animate-bounce {
  animation: bounce 1s ease-in-out infinite;
}
</style>
