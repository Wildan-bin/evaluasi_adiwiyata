<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';
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

  <!-- User Management Section -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Admin Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Admin</h2>
        <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
          + Tambah Admin
        </button>
      </div>
      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div>
            <p class="font-medium">Admin 1</p>
            <p class="text-sm text-gray-500">admin@example.com</p>
          </div>
          <button class="text-red-600 hover:text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- User Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">User</h2>
        <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
          + Tambah User
        </button>
      </div>
      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div>
            <p class="font-medium">User 1</p>
            <p class="text-sm text-gray-500">user@example.com</p>
          </div>
          <button class="text-red-600 hover:text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Pembimbing Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Mentor</h2>
        <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
          + Tambah Mentor
        </button>
      </div>
      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div>
            <p class="font-medium">Pembimbing 1</p>
            <p class="text-sm text-gray-500">pembimbing@example.com</p>
          </div>
          <button class="text-red-600 hover:text-red-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Pengajuan List Section -->
  <div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold mb-6">Daftar Sekolah</h2>
    
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-500 text-white">
          <tr>
            <th class="px-6 py-4 text-left">Nama Sekolah</th>
            <th class="px-6 py-4 text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SDN Example 1</td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum Dievaluasi
              </span>
            </td>
          </tr>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                Sudah Dievaluasi
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>
    </MainLayout>
</template>
