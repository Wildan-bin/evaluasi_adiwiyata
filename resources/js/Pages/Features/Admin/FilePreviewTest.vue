<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Eye, FileText, Download, ExternalLink, X, Loader } from 'lucide-vue-next';

// ============================================================================
// STATE
// ============================================================================
const files = ref([
  {
    id: 1,
    name: 'test.pdf',
    size: 2500000,
    uploadedAt: '2024-12-03',
    fieldName: 'evaluasi_diri_sekolah'
  }
]);

const selectedFile = ref(null);
const showPreview = ref(false);
const previewUrl = ref('');
const isLoading = ref(false);

// ============================================================================
// METHODS
// ============================================================================

/**
 * Format file size to human readable format
 */
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

/**
 * Format date to human readable format
 */
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

/**
 * Open preview modal
 */
const openPreview = async (file) => {
  isLoading.value = true;
  selectedFile.value = file;
  previewUrl.value = route('test.preview', file.name);
  showPreview.value = true;
  
  // Simulate loading delay
  await new Promise(resolve => setTimeout(resolve, 500));
  isLoading.value = false;
};

/**
 * Close preview modal
 */
const closePreview = () => {
  showPreview.value = false;
  isLoading.value = false;
  selectedFile.value = null;
  previewUrl.value = '';
};

/**
 * Open PDF in new tab
 */
const openInNewTab = () => {
  window.open(previewUrl.value, '_blank');
};

/**
 * Download file
 */
const downloadFile = () => {
  const link = document.createElement('a');
  link.href = previewUrl.value;
  link.download = selectedFile.value.name;
  link.click();
};

/**
 * Handle keyboard event (close on Escape)
 */
const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    closePreview();
  }
};
</script>

<template>
  <MainLayout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-3">📄 File Preview Test</h1>
        <p class="text-lg text-gray-600">
          Test preview file PDF dari storage/app/submissions
        </p>
      </div>

      <!-- Files Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template v-for="file in files" :key="file.id">
          <!-- File Card -->
          <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 hover:border-green-400">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-red-100 to-red-50 px-6 py-8 text-center">
              <FileText class="w-16 h-16 text-red-600 mx-auto mb-3" />
              <p class="text-sm text-gray-600 font-semibold">PDF Document</p>
            </div>

            <!-- Card Content -->
            <div class="px-6 py-4">
              <!-- File Name -->
              <h3 class="font-bold text-gray-900 text-lg mb-3 truncate" :title="file.name">
                {{ file.name }}
              </h3>

              <!-- File Details -->
              <div class="space-y-2 mb-6 text-sm text-gray-600">
                <div class="flex items-center justify-between">
                  <span>Ukuran:</span>
                  <span class="font-semibold text-gray-900">{{ formatFileSize(file.size) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Tipe:</span>
                  <span class="font-semibold text-gray-900">{{ file.fieldName }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Tanggal:</span>
                  <span class="font-semibold text-gray-900">{{ formatDate(file.uploadedAt) }}</span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-2">
                <button
                  @click="openPreview(file)"
                  class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600  font-semibold text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg"
                >
                  <Eye class="w-5 h-5" />
                  Preview PDF
                </button>

                <button
                  @click="downloadFile"
                  class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-300"
                >
                  <Download class="w-5 h-5" />
                  Download
                </button>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- Info Box -->
      <div class="mt-12 p-6 bg-blue-50 border-2 border-blue-200 rounded-xl">
        <p class="text-blue-900">
          <strong>ℹ️ Info:</strong> File PDF akan ditampilkan di modal preview. Anda dapat membuka di tab baru atau mendownload file.
        </p>
      </div>

      <!-- ================================================================== -->
      <!-- PDF PREVIEW MODAL - OPTIMIZED UX -->
      <!-- ================================================================== -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition-all duration-300"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <!-- Overlay -->
        <div
          v-if="showPreview"
          @click="closePreview"
          @keydown="handleKeydown"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 md:p-6"
          role="dialog"
          aria-modal="true"
          aria-label="PDF Preview"
        >
          <!-- Modal Container -->
          <div
            @click.stop
            class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl h-[960px] flex flex-col overflow-hidden"
          >
            <!-- ============================================================== -->
            <!-- HEADER - Sticky untuk UX lebih baik -->
            <!-- ============================================================== -->
            <div class="flex-shrink-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 md:px-8 py-4 flex items-center justify-between border-b border-blue-800">
              <!-- Left Side: File Info -->
              <div class="flex-1 min-w-0">
                <h2 class="text-lg md:text-xl font-bold text-white truncate">
                  {{ selectedFile?.name }}
                </h2>
                <p class="text-blue-100 text-xs md:text-sm mt-1">
                  {{ formatFileSize(selectedFile?.size) }} • Uploaded {{ formatDate(selectedFile?.uploadedAt) }}
                </p>
              </div>

              <!-- Right Side: Close Button -->
              <button
                @click="closePreview"
                class="flex-shrink-0 ml-4 p-2 hover:bg-blue-500 rounded-lg transition-colors duration-200"
                aria-label="Close preview"
                title="Close (Esc)"
              >
                <X class="w-6 h-6 text-white" />
              </button>
            </div>

            <!-- ============================================================== -->
            <!-- CONTENT - PDF Viewer dengan Loading State -->
            <!-- ============================================================== -->
            <div class="flex-1 overflow-auto bg-gray-900 relative flex items-center justify-center">
              <!-- Loading Indicator -->
              <div
                v-if="isLoading"
                class="absolute inset-0 flex items-center justify-center bg-gray-900/80 z-10"
              >
                <div class="text-center">
                  <Loader class="w-12 h-12 text-blue-500 animate-spin mx-auto mb-3" />
                  <p class="text-white font-semibold">Memuat preview...</p>
                </div>
              </div>

              <!-- PDF Iframe -->
              <iframe
                v-if="previewUrl"
                :src="previewUrl"
                class="w-full h-full border-0"
                title="PDF Preview"
                @load="isLoading = false"
              ></iframe>
            </div>

            <!-- ============================================================== -->
            <!-- FOOTER - Action Buttons -->
            <!-- ============================================================== -->
            <div class="flex-shrink-0 bg-gray-50 px-6 md:px-8 py-4 border-t border-gray-200 flex items-center justify-between gap-3">
              <!-- Left: Info -->
              <p class="text-sm text-gray-600 hidden sm:block">
                Press <kbd class="px-2 py-1 bg-gray-200 rounded text-gray-800 font-mono text-xs">Esc</kbd> to close
              </p>

              <!-- Right: Action Buttons -->
              <div class="flex items-center gap-3">
                <!-- Open in New Tab Button -->
                <button
                  @click="openInNewTab"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-all duration-200"
                  title="Open in new tab"
                >
                  <ExternalLink class="w-4 h-4" />
                  <span class="hidden sm:inline">Open in Tab</span>
                  <span class="sm:hidden">Tab</span>
                </button>

                <!-- Download Button -->
                <button
                  @click="downloadFile"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg"
                  title="Download file"
                >
                  <Download class="w-4 h-4" />
                  <span class="hidden sm:inline">Download</span>
                  <span class="sm:hidden">DL</span>
                </button>

                <!-- Close Button -->
                <button
                  @click="closePreview"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 font-semibold hover:bg-red-200 transition-all duration-200"
                  title="Close preview"
                >
                  <X class="w-4 h-4" />
                  <span class="hidden sm:inline">Close</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </MainLayout>
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

/* Keyboard shortcut styling */
kbd {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  border-bottom: 2px solid currentColor;
  box-shadow: 0 2px 0 currentColor;
}
</style>