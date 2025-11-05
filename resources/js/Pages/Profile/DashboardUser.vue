<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';

// Functions moved from inline script: manage dynamic activity items
function addNewActivity() {
  const container = document.getElementById('activities-container');
  const newActivity = document.createElement('div');
  newActivity.className = 'activity-item flex items-start gap-2';
  newActivity.innerHTML = `
    <div class="flex-grow">
      <input type="text" placeholder="Nama Kegiatan" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500 mb-2">
      <textarea placeholder="Deskripsi Kegiatan" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" rows="2"></textarea>
    </div>
    <button type="button" class="text-red-500 hover:text-red-700 mt-2 remove-activity-btn">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
    </button>
  `;
  container.appendChild(newActivity);
  // attach event listener to the remove button
  const btn = newActivity.querySelector('.remove-activity-btn');
  if (btn) btn.addEventListener('click', () => removeActivity(btn));
}

function removeActivity(button) {
  const activityItem = button.closest('.activity-item');
  if (activityItem) activityItem.remove();
}
</script>

<template>
    
    <MainLayout>
        <Head title="Dashboard" />
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <Header
            title="Dashboard Adiwiyata"
            description="Kelola dan monitor program lingkungan sekolah Anda"
            color="green"
        />

        <main class="container mx-auto px-4 pt-24 pb-8">

  <!-- Pengajuan Penilaian Section -->
  <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Pengajuan Penilaian Adiwiyata</h2>
    
    <!-- Form Pengajuan -->
    <form class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Sekolah -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sekolah</label>
            <input type="text" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NPSN</label>
            <input type="text" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" rows="3"></textarea>
          </div>
          <div>
              <div class="flex justify-between items-center mb-2">
              <label class="block text-sm font-medium text-gray-700">Daftar Kegiatan</label>
              <button type="button" @click="addNewActivity" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                Tambah Kegiatan
              </button>
            </div>
            <div id="activities-container" class="space-y-3">
              <div class="activity-item flex items-start gap-2">
                <div class="flex-grow">
                  <input type="text" placeholder="Nama Kegiatan" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500 mb-2">
                  <textarea placeholder="Deskripsi Kegiatan" class="w-full rounded-md border-gray-300 shadow-sm px-4 py-2 border focus:border-blue-500 focus:ring-1 focus:ring-blue-500" rows="2"></textarea>
                </div>
                <button type="button" @click="removeActivity($event.currentTarget)" class="text-red-500 hover:text-red-700 mt-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          
        </div>

        <!-- File Upload Section -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen Pendukung</label>
            <div class="space-y-4">
              <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                <div class="text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-4h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <p class="mt-1 text-sm text-gray-600">Upload dokumen evaluasi diri</p>
                  <input type="file" class="hidden" id="file-upload">
                  <button type="button" onclick="document.getElementById('file-upload').click()" class="mt-2 px-4 py-2 text-sm text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">
                    Pilih File
                  </button>
                </div>
              </div>
              <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                <div class="text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-4h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <p class="mt-1 text-sm text-gray-600">Upload dokumentasi kegiatan</p>
                  <input type="file" class="hidden" id="doc-upload">
                  <button type="button" onclick="document.getElementById('doc-upload').click()" class="mt-2 px-4 py-2 text-sm text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">
                    Pilih File
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end">
        <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-md hover:bg-green-600 font-medium">
          Ajukan Penilaian
        </button>
      </div>
    </form>
  </div>

  <!-- Two columns: History & Evaluasi -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- History Pengajuan -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-800 mb-4">History Pengajuan</h3>
      <div class="space-y-4">
        <div class="border rounded-lg p-4">
          <div class="flex justify-between items-start mb-2">
            <div>
              <h4 class="font-medium text-gray-800">Pengajuan #1</h4>
              <p class="text-sm text-gray-500">Diajukan pada: 15 Oktober 2025</p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
              Dalam Review
            </span>
          </div>
          <div class="text-sm text-gray-600">
            <p>Status: Menunggu verifikasi dokumen</p>
            <p class="mt-1">Estimasi selesai: 22 Oktober 2025</p>
          </div>
        </div>

        <div class="border rounded-lg p-4">
          <div class="flex justify-between items-start mb-2">
            <div>
              <h4 class="font-medium text-gray-800">Pengajuan #2</h4>
              <p class="text-sm text-gray-500">Diajukan pada: 1 Oktober 2025</p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
              Selesai
            </span>
          </div>
          <div class="text-sm text-gray-600">
            <p>Status: Penilaian selesai</p>
            <p class="mt-1">Selesai pada: 8 Oktober 2025</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Evaluasi & Poin -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <h3 class="text-xl font-bold text-gray-800 mb-4">Evaluasi & Poin</h3>
      
      <!-- Total Score -->
      <div class="bg-blue-50 rounded-lg p-4 mb-6">
        <div class="text-center">
          <p class="text-sm text-gray-600 mb-1">Total Poin</p>
          <p class="text-3xl font-bold text-blue-600">85/100</p>
        </div>
      </div>

      <!-- Detailed Scores -->
      <div class="space-y-4">
        <div class="border-b pb-4">
          <div class="flex justify-between items-center mb-2">
            <h4 class="font-medium text-gray-800">Kebijakan Berwawasan Lingkungan</h4>
            <span class="font-medium text-blue-600">22/25</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 88%"></div>
          </div>
        </div>

        <div class="border-b pb-4">
          <div class="flex justify-between items-center mb-2">
            <h4 class="font-medium text-gray-800">Pelaksanaan Kurikulum</h4>
            <span class="font-medium text-blue-600">20/25</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
          </div>
        </div>

        <div class="border-b pb-4">
          <div class="flex justify-between items-center mb-2">
            <h4 class="font-medium text-gray-800">Kegiatan Lingkungan</h4>
            <span class="font-medium text-blue-600">23/25</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 92%"></div>
          </div>
        </div>

        <div class="pb-4">
          <div class="flex justify-between items-center mb-2">
            <h4 class="font-medium text-gray-800">Pengelolaan Sarana</h4>
            <span class="font-medium text-blue-600">20/25</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
    </MainLayout>
</template>
