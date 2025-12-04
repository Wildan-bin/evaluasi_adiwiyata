<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue'
import { Head } from '@inertiajs/vue3';

    // Data user
    let admins = [
      { name: "Admin 1", email: "admin@example.com" }
    ];
    let users = [
      { name: "User 1", email: "user@example.com" }
    ];
    let mentors = [
      { name: "Pembimbing 1", email: "pembimbing@example.com" }
    ];

    function renderUserList(containerId, data, type) {
      const container = document.getElementById(containerId);
      container.innerHTML = "";
      data.forEach((user, idx) => {
        const div = document.createElement("div");
        div.className = "flex items-center justify-between p-3 bg-gray-50 rounded-md mb-2";
        div.innerHTML = `
          <div>
            <p class="font-medium">${user.name}</p>
            <p class="text-sm text-gray-500">${user.email}</p>
          </div>
          <button class="text-red-600 hover:text-red-800" onclick="deleteUser('${type}', ${idx})">
            <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' /></svg>
          </button>
        `;
        container.appendChild(div);
      });
    }

    function deleteUser(type, idx) {
      if (type === "admin") admins.splice(idx, 1);
      if (type === "user") users.splice(idx, 1);
      if (type === "mentor") mentors.splice(idx, 1);
      renderAll();
    }

    function addUser(type) {
      const name = prompt("Masukkan nama " + type);
      const email = prompt("Masukkan email " + type);
      if (name && email) {
        if (type === "admin") admins.push({ name, email });
        if (type === "user") users.push({ name, email });
        if (type === "mentor") mentors.push({ name, email });
        renderAll();
      }
    }

    function renderAll() {
      renderUserList("admin-list", admins, "admin");
      renderUserList("user-list", users, "user");
      renderUserList("mentor-list", mentors, "mentor");
    }

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("add-admin-btn").onclick = function() { addUser("admin"); };
      document.getElementById("add-user-btn").onclick = function() { addUser("user"); };
      document.getElementById("add-mentor-btn").onclick = function() { addUser("mentor"); };
      renderAll();
    });

function getMentorListFromContainer() {
  const mentorNodes = document.querySelectorAll('.bg-white.rounded-lg.shadow-lg .space-y-3 .font-medium');
  let mentors = [];
  // Ambil hanya mentor dari container Pembimbing
  document.querySelectorAll('.bg-white.rounded-lg.shadow-lg').forEach(container => {
    const title = container.querySelector('h2');
    if (title && title.textContent.trim().toLowerCase().includes('mentor')) {
      container.querySelectorAll('.space-y-3 .font-medium').forEach(node => {
        mentors.push(node.textContent.trim());
      });
    }
  });
  return mentors;
}
let assignedMentors = {};

function renderMentorDropdown(rowId, mentorList) {
  const select = document.createElement('select');
  select.className = "border rounded px-2 py-1 w-32";
  select.innerHTML = '<option value="">Pilih Mentor</option>' + mentorList.map(m => `<option value="${m}">${m}</option>`).join('');
  select.onchange = function() {
    assignedMentors[rowId] = select.value;
  };
  return select;
}

function saveMentor(rowId, selectElem) {
  const mentor = selectElem.value;
  if (mentor) {
    selectElem.disabled = true;
    document.getElementById('save-btn-' + rowId).disabled = true;
    alert('Mentor berhasil disimpan!');
    // Refresh dropdown di baris lain
    document.querySelectorAll('.mentor-dropdown').forEach((drop, idx) => {
      if (idx !== rowId) {
        const mentorList = getMentorListFromContainer().filter(m => !Object.values(assignedMentors).includes(m));
        const currentValue = drop.value;
        drop.innerHTML = '<option value="">Pilih Mentor</option>' + mentorList.map(m => `<option value="${m}">${m}</option>`).join('');
        drop.value = currentValue;
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  // Ambil mentor dari container
  let mentorList = getMentorListFromContainer();
  document.querySelectorAll('.mentor-cell').forEach((cell, idx) => {
    const select = renderMentorDropdown(idx, mentorList.filter(m => !Object.values(assignedMentors).includes(m)));
    select.classList.add('mentor-dropdown');
    cell.innerHTML = '';
    cell.appendChild(select);
    // Simpan mentor
    const saveBtn = document.getElementById('save-btn-' + idx);
    if (saveBtn) {
      saveBtn.onclick = function() { saveMentor(idx, select); };
    }
  });
});
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
  <div class="bg-white rounded-lg shadow-lg p-6 mb-12">
    <h2 class="text-2xl font-bold mb-6">Daftar Sekolah Pengajuan PPEPP</h2>
    
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-500 text-white">
          <tr>
            <th class="px-6 py-4 text-left">Nama Sekolah</th>
            <th class="px-6 py-4 text-center">Mentor</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SDN Example 1</td>
            <td class="px-6 py-4 text-center mentor-cell"></td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum Dievaluasi
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button id="save-btn-0" class="bg-blue-500 text-white px-3 py-1 rounded">Simpan Mentor</button>
            </td>
          </tr>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center mentor-cell"></td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum Dievaluasi
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button id="save-btn-1" class="bg-blue-500 text-white px-3 py-1 rounded">Simpan Mentor</button>
            </td>
          </tr>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center mentor-cell"></td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum Dievaluasi
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button id="save-btn-2" class="bg-blue-500 text-white px-3 py-1 rounded">Simpan Mentor</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
   <!-- Pengajuan List Section -->
  <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-2xl font-bold mb-6">Daftar Sekolah Sudah Evaluasi PPEPP</h2>
    
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-500 text-white">
          <tr>
            <th class="px-6 py-4 text-left">Nama Sekolah</th>
            <th class="px-6 py-4 text-center">Mentor</th>
            <th class="px-6 py-4 text-center">File Bukti</th>
            <th class="px-6 py-4 text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SDN Example 1</td>
            <td class="px-6 py-4 text-center">Mentor A</td>
            <td class="px-6 py-4 text-center">
              <a href="#" class="bg-green-500 text-white px-3 py-1 rounded" id="download-bukti-0">Download Bukti</a>
              <span class="block text-xs text-gray-500 mt-1" id="bukti-status-0">Belum ada file bukti</span>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                Sudah Dievaluasi
              </span>
            </td>
          </tr>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center">Mentor B</td>
            <td class="px-6 py-4 text-center">
              <a href="#" class="bg-green-500 text-white px-3 py-1 rounded" id="download-bukti-1">Download Bukti</a>
              <span class="block text-xs text-gray-500 mt-1" id="bukti-status-1">Belum ada file bukti</span>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                Sudah Dievaluasi
              </span>
            </td>
          </tr>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center">Mentor C</td>
            <td class="px-6 py-4 text-center">
              <a href="#" class="bg-green-500 text-white px-3 py-1 rounded" id="download-bukti-2">Download Bukti</a>
              <span class="block text-xs text-gray-500 mt-1" id="bukti-status-2">Belum ada file bukti</span>
            </td>
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
   <!-- Pemantauan Administrasi Sekolah Section -->
  <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
    <h2 class="text-2xl font-bold mb-6">Pemantauan Administrasi Sekolah</h2>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-500 text-white">
          <tr>
            <th class="px-6 py-4 text-left">Nama Sekolah</th>
            <th class="px-6 py-4 text-center">Rencana & Evaluasi PBLHS</th>
            <th class="px-6 py-4 text-center">Self Assessment</th>
            <th class="px-6 py-4 text-center">Kebutuhan Pendampingan</th>
            <th class="px-6 py-4 text-center">Pernyataan & Persetujuan</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Diisi</button>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Diisi</button>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Diisi</button>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Belum diisi</button>
            </td>
          </tr>
        </tbody>
        <tbody>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">SMP Example 1</td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Diisi</button>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Belum diisi</button>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Diisi</button>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Belum diisi</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>
    </MainLayout>
</template>
