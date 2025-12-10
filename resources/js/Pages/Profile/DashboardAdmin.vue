<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, X, UserPlus, Eye, EyeOff } from 'lucide-vue-next';

// Props dari backend
const props = defineProps({
  admins: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  },
  mentors: {
    type: Array,
    default: () => []
  },
  administrasiSekolah: {
    type: Array,
    default: () => []
  }
});

// Reactive data untuk users (dari props)
const admins = ref(props.admins);
const users = ref(props.users);
const mentors = ref(props.mentors);

// Data sekolah
const schools = ref([
  { id: 1, name: "SDN Example 1", mentor: null, status: "pending" },
  { id: 2, name: "SMP Example 1", mentor: null, status: "pending" },
  { id: 3, name: "SMA Example 1", mentor: null, status: "pending" }
]);

const evaluatedSchools = ref([
  { id: 1, name: "SDN Contoh 1", mentor: "Mentor A", status: "completed", hasFile: false },
  { id: 2, name: "SMP Contoh 2", mentor: "Mentor B", status: "completed", hasFile: false },
  { id: 3, name: "SMA Contoh 3", mentor: "Mentor C", status: "completed", hasFile: true }
]);

// Data administrasi sekolah dari backend props
const administrasiSekolah = ref(props.administrasiSekolah);

// Modal state
const showModal = ref(false);
const modalType = ref('');
const showPassword = ref(false);
const formData = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});
const formErrors = ref({});

// Open modal untuk tambah user
const openAddUserModal = (type) => {
  modalType.value = type;
  showModal.value = true;
  formData.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  };
  formErrors.value = {};
  showPassword.value = false;
};

// Close modal
const closeModal = () => {
  showModal.value = false;
  formData.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  };
  formErrors.value = {};
  showPassword.value = false;
};

// Validate form
const validateForm = () => {
  formErrors.value = {};

  if (!formData.value.name) {
    formErrors.value.name = 'Nama wajib diisi';
  }

  if (!formData.value.email) {
    formErrors.value.email = 'Email wajib diisi';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    formErrors.value.email = 'Format email tidak valid';
  }

  if (!formData.value.password) {
    formErrors.value.password = 'Password wajib diisi';
  } else if (formData.value.password.length < 8) {
    formErrors.value.password = 'Password minimal 8 karakter';
  }

  if (formData.value.password !== formData.value.password_confirmation) {
    formErrors.value.password_confirmation = 'Konfirmasi password tidak cocok';
  }

  return Object.keys(formErrors.value).length === 0;
};

// Submit form
const submitForm = () => {
  if (!validateForm()) {
    return;
  }

  // Kirim ke backend dengan Inertia
  router.post('/users', {
    name: formData.value.name,
    email: formData.value.email,
    password: formData.value.password,
    password_confirmation: formData.value.password_confirmation,
    role: modalType.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      // Reload data setelah berhasil
      router.reload({ only: ['admins', 'users', 'mentors'] });
    },
    onError: (errors) => {
      formErrors.value = errors;
    }
  });
};

const deleteUser = (type, id) => {
  if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
    router.delete(`/users/${id}`, {
      preserveScroll: true,
      onSuccess: () => {
        // Update akan otomatis dari backend
        router.reload({ only: ['admins', 'users', 'mentors'] });
      }
    });
  }
};// Functions untuk assign mentor
const assignMentor = (schoolId, mentorName) => {
  const school = schools.value.find(s => s.id === schoolId);
  if (school) {
    school.mentor = mentorName;
    alert('Mentor berhasil disimpan!');
  }
};

const downloadBukti = (schoolId) => {
  const school = evaluatedSchools.value.find(s => s.id === schoolId);
  if (school && school.hasFile) {
    alert('Downloading file bukti untuk ' + school.name);
    // Implement download logic here
  } else {
    alert('Belum ada file bukti untuk sekolah ini');
  }
};

// Available mentors list
const availableMentors = computed(() => {
  return mentors.value.map(m => m.name);
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
            title="Dashboard Greenedu Admin"
            description="Kelola user, assign mentor, dan monitor program lingkungan sekolah"
            color="green"
        />

        <main class="container mx-auto px-4 pt-24 pb-8">

  <!-- User Management Section -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Admin Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Admin</h2>
        <button @click="openAddUserModal('admin')" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
          + Tambah Admin
        </button>
      </div>
      <div class="space-y-3" id="admin-list">
        <div v-for="admin in admins" :key="admin.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div>
            <p class="font-medium">{{ admin.name }}</p>
            <p class="text-sm text-gray-500">{{ admin.email }}</p>
          </div>
          <button @click="deleteUser('admin', admin.id)" class="text-red-600 hover:text-red-800 transition">
            <Trash2 :size="20" />
          </button>
        </div>
      </div>
    </div>

    <!-- User Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">User</h2>
        <button @click="openAddUserModal('user')" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
          + Tambah User
        </button>
      </div>
      <div class="space-y-3" id="user-list">
        <div v-for="user in users" :key="user.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div>
            <p class="font-medium">{{ user.name }}</p>
            <p class="text-sm text-gray-500">{{ user.email }}</p>
          </div>
          <button @click="deleteUser('user', user.id)" class="text-red-600 hover:text-red-800 transition">
            <Trash2 :size="20" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mentor Container -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Mentor</h2>
        <button @click="openAddUserModal('mentor')" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
          + Tambah Mentor
        </button>
      </div>
      <div class="space-y-3" id="mentor-list">
        <div v-for="mentor in mentors" :key="mentor.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div>
            <p class="font-medium">{{ mentor.name }}</p>
            <p class="text-sm text-gray-500">{{ mentor.email }}</p>
          </div>
          <button @click="deleteUser('mentor', mentor.id)" class="text-red-600 hover:text-red-800 transition">
            <Trash2 :size="20" />
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
          <tr v-for="school in schools" :key="school.id" class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4">{{ school.name }}</td>
            <td class="px-6 py-4 text-center">
              <select
                v-model="school.mentor"
                class="border rounded px-3 py-2 w-40 focus:outline-none focus:ring-2 focus:ring-green-500"
                :disabled="school.mentor !== null"
              >
                <option :value="null">Pilih Mentor</option>
                <option v-for="mentor in availableMentors" :key="mentor" :value="mentor">
                  {{ mentor }}
                </option>
              </select>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum Dievaluasi
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button
                @click="assignMentor(school.id, school.mentor)"
                :disabled="!school.mentor"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
              >
                Simpan Mentor
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
   <!-- Daftar Sekolah Sudah Evaluasi -->
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
          <tr v-for="school in evaluatedSchools" :key="school.id" class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4">{{ school.name }}</td>
            <td class="px-6 py-4 text-center">{{ school.mentor }}</td>
            <td class="px-6 py-4 text-center">
              <button
                @click="downloadBukti(school.id)"
                :class="school.hasFile ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 cursor-not-allowed'"
                class="text-white px-4 py-2 rounded transition"
                :disabled="!school.hasFile"
              >
                Download Bukti
              </button>
              <span class="block text-xs mt-1" :class="school.hasFile ? 'text-green-600' : 'text-gray-500'">
                {{ school.hasFile ? 'File tersedia' : 'Belum ada file bukti' }}
              </span>
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
          <tr v-for="adm in administrasiSekolah" :key="adm.id" class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4 font-medium">{{ adm.name }}</td>

            <!-- Rencana & Evaluasi PBLHS -->
            <td class="px-6 py-4 text-center">
              <Link
                v-if="adm.rencana_evaluasi"
                :href="`/administrasi-sekolah/${adm.user_id}/preview`"
                class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition cursor-pointer"
              >
                <Eye :size="14" />
                Lihat
              </Link>
              <span v-else class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum diisi
              </span>
            </td>

            <!-- Self Assessment -->
            <td class="px-6 py-4 text-center">
              <Link
                v-if="adm.self_assessment"
                :href="`/administrasi-sekolah/${adm.user_id}/preview#self-assessment`"
                class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition cursor-pointer"
              >
                <Eye :size="14" />
                Lihat
              </Link>
              <span v-else class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum diisi
              </span>
            </td>

            <!-- Kebutuhan Pendampingan -->
            <td class="px-6 py-4 text-center">
              <Link
                v-if="adm.kebutuhan_pendampingan"
                :href="`/administrasi-sekolah/${adm.user_id}/preview#kebutuhan`"
                class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition cursor-pointer"
              >
                <Eye :size="14" />
                Lihat
              </Link>
              <span v-else class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum diisi
              </span>
            </td>

            <!-- Pernyataan & Persetujuan -->
            <td class="px-6 py-4 text-center">
              <Link
                v-if="adm.pernyataan"
                :href="`/administrasi-sekolah/${adm.user_id}/preview#pernyataan`"
                class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition cursor-pointer"
              >
                <Eye :size="14" />
                Lihat
              </Link>
              <span v-else class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                Belum diisi
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

    <!-- Modal Add User -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
          <!-- Backdrop -->
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

          <!-- Modal Content -->
          <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all">
              <!-- Header -->
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <UserPlus class="text-green-600" :size="20" />
                  </div>
                  <h3 class="text-xl font-bold text-gray-900">
                    Tambah {{ modalType.charAt(0).toUpperCase() + modalType.slice(1) }}
                  </h3>
                </div>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                  <X :size="24" />
                </button>
              </div>

              <!-- Form -->
              <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Nama -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="formData.name"
                    type="text"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    :class="formErrors.name ? 'border-red-500' : 'border-gray-300'"
                    placeholder="Masukkan nama lengkap"
                  />
                  <p v-if="formErrors.name" class="text-red-500 text-xs mt-1">{{ formErrors.name }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="formData.email"
                    type="email"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    :class="formErrors.email ? 'border-red-500' : 'border-gray-300'"
                    placeholder="contoh@email.com"
                  />
                  <p v-if="formErrors.email" class="text-red-500 text-xs mt-1">{{ formErrors.email }}</p>
                </div>

                <!-- Password -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model="formData.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition pr-10"
                      :class="formErrors.password ? 'border-red-500' : 'border-gray-300'"
                      placeholder="Minimal 8 karakter"
                    />
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="!showPassword" :size="20" />
                      <EyeOff v-else :size="20" />
                    </button>
                  </div>
                  <p v-if="formErrors.password" class="text-red-500 text-xs mt-1">{{ formErrors.password }}</p>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi Password <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="formData.password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    :class="formErrors.password_confirmation ? 'border-red-500' : 'border-gray-300'"
                    placeholder="Ulangi password"
                  />
                  <p v-if="formErrors.password_confirmation" class="text-red-500 text-xs mt-1">{{ formErrors.password_confirmation }}</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                  <button
                    type="button"
                    @click="closeModal"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                  >
                    Batal
                  </button>
                  <button
                    type="submit"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                  >
                    Tambah {{ modalType.charAt(0).toUpperCase() + modalType.slice(1) }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
    </MainLayout>
</template>

<style scoped>
/* Modal Animation */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.95);
}
</style>
