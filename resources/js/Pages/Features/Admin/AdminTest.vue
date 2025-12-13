<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Download, Eye, AlertCircle, Loader, FileText } from 'lucide-vue-next';

const page = usePage();
const submissions = ref([]);
const loading = ref(true);
const selectedFile = ref(null);
const previewUrl = ref('');
const showPreview = ref(false);

onMounted(async () => {
  try {
    // Fetch semua submissions dengan file evidences
    const response = await fetch('/api/submissions-with-files', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    if (response.ok) {
      const data = await response.json();
      submissions.value = data.submissions;
    }
  } catch (error) {
    console.error('Error fetching submissions:', error);
  } finally {
    loading.value = false;
  }
});

const openPreview = (fileEvidence) => {
  selectedFile.value = fileEvidence;
  previewUrl.value = `/file-evidence/${fileEvidence.id}/preview`;
  showPreview.value = true;
};

const closePreview = () => {
  showPreview.value = false;
  selectedFile.value = null;
  previewUrl.value = '';
};

const downloadFile = (fileEvidenceId) => {
  window.location.href = `/file-evidence/${fileEvidenceId}/download`;
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📋 Admin Test - File Preview</h1>
        <p class="text-gray-600 mt-2">Lihat dan download semua file yang telah disubmit</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <Loader class="w-8 h-8 text-green-600 animate-spin mr-3" />
        <p class="text-gray-600">Memuat data...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="submissions.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
        <AlertCircle class="w-12 h-12 text-gray-400 mx-auto mb-3" />
        <p class="text-gray-600">Tidak ada submission ditemukan</p>
      </div>

      <!-- Submissions List -->
      <div v-else class="space-y-6">
        <template v-for="submission in submissions" :key="submission.id">
          <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
            <!-- Submission Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-xl font-bold">Submission #{{ submission.id }}</h2>
                  <p class="text-green-100 text-sm">{{ submission.user?.name }} - {{ submission.user?.school?.name }}</p>
                </div>
                <div class="text-right">
                  <span class="inline-block px-3 py-1 bg-green-700 rounded-full text-sm font-semibold">
                    {{ submission.status }}
                  </span>
                  <p class="text-green-100 text-xs mt-2">{{ formatDate(submission.submitted_at) }}</p>
                </div>
              </div>
            </div>

            <!-- Submission Info -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                  <p class="text-gray-600 font-semibold">Ketua Tim</p>
                  <p class="text-gray-900 font-medium">{{ submission.ketua_tim }}</p>
                </div>
                <div>
                  <p class="text-gray-600 font-semibold">Jumlah Kader</p>
                  <p class="text-gray-900 font-medium">{{ submission.jumlah_kader_adiwiyata }} orang</p>
                </div>
                <div>
                  <p class="text-gray-600 font-semibold">File Diunggah</p>
                  <p class="text-gray-900 font-medium">{{ submission.file_evidences?.length || 0 }} file</p>
                </div>
                <div>
                  <p class="text-gray-600 font-semibold">Total Ukuran</p>
                  <p class="text-gray-900 font-medium">
                    {{
                      formatFileSize(
                        submission.file_evidences?.reduce((acc, f) => acc + (f.file_size || 0), 0) || 0
                      )
                    }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Files List -->
            <div class="px-6 py-4">
              <h3 class="font-bold text-gray-900 mb-4">📁 File Evidence</h3>

              <div v-if="!submission.file_evidences || submission.file_evidences.length === 0" class="text-center py-6 bg-gray-50 rounded-lg">
                <AlertCircle class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                <p class="text-gray-600">Tidak ada file untuk submission ini</p>
              </div>

              <div v-else class="space-y-3">
                <template v-for="file in submission.file_evidences" :key="file.id">
                  <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                      <!-- File Icon -->
                      <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <FileText class="w-6 h-6 text-red-600" />
                      </div>

                      <!-- File Info -->
                      <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900 truncate" :title="file.original_name">
                          {{ file.original_name }}
                        </p>
                        <div class="flex items-center gap-3 text-xs text-gray-600 mt-1">
                          <span>{{ formatFileSize(file.file_size) }}</span>
                          <span>•</span>
                          <span>{{ file.field_name }}</span>
                          <span>•</span>
                          <span>{{ formatDate(file.uploaded_at) }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                      <button
                        @click="openPreview(file)"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors font-semibold text-sm"
                        title="Preview file"
                      >
                        <Eye class="w-4 h-4" />
                        <span class="hidden sm:inline">Preview</span>
                      </button>

                      <button
                        @click="downloadFile(file.id)"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition-colors font-semibold text-sm"
                        title="Download file"
                      >
                        <Download class="w-4 h-4" />
                        <span class="hidden sm:inline">Download</span>
                      </button>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- PDF Preview Modal -->
      <transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-all duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showPreview" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold text-gray-900 truncate">
                  {{ selectedFile?.original_name }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                  {{ selectedFile?.field_name }} • {{ formatFileSize(selectedFile?.file_size) }}
                </p>
              </div>
              <button
                @click="closePreview"
                class="flex-shrink-0 p-2 hover:bg-gray-200 rounded-lg transition-colors ml-4"
              >
                ✕
              </button>
            </div>

            <!-- PDF Viewer -->
            <div class="flex-1 overflow-auto bg-gray-100 flex items-center justify-center">
              <iframe
                :src="previewUrl"
                class="w-full h-full border-0"
                title="PDF Preview"
              ></iframe>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50">
              <p class="text-sm text-gray-600">
                Uploaded: {{ formatDate(selectedFile?.uploaded_at) }}
              </p>
              <button
                @click="downloadFile(selectedFile?.id)"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors font-semibold"
              >
                <Download class="w-4 h-4" />
                Download
              </button>
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
</style>