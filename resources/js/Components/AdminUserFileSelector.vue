<template>
    <div class="space-y-6">
        <!-- User Selector -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pilih Pengguna</h2>

            <div v-if="loading" class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
            </div>
            <select
                v-else
                @change="handleUserChange"
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-green-500 focus:border-green-500"
            >
                <option value="" disabled selected>-- Pilih Pengguna --</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} - {{ user.email }}
                </option>
            </select>
        </div>

        <!-- User Information -->
        <div v-if="selectedUser" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pengguna</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedUser.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedUser.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">NPSN</label>
                    <p class="mt-1 text-sm text-gray-900">{{ getUserAdministrasiData(selectedUser).npsn }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                    <p class="mt-1 text-sm text-gray-900">{{ getUserAdministrasiData(selectedUser).nama_sekolah }}</p>
                </div>
            </div>
        </div>

        <!-- File List -->
        <div v-if="selectedUser" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                File yang Diupload
            </h3>
            <FileReviewList :userId="selectedUser.id" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import FileReviewList from './FileReviewList.vue';

const users = ref([]);
const selectedUser = ref(null);
const loading = ref(true);

onMounted(() => {
    loadUsers();
});

const loadUsers = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/users');
        users.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error loading users:', error);
    } finally {
        loading.value = false;
    }
};

const handleUserChange = (e) => {
    const userId = parseInt(e.target.value);
    const user = users.value.find(u => u.id === userId);
    selectedUser.value = user || null;
};

const getUserAdministrasiData = (user) => {
    return {
        npsn: user.administrasi_sekolah?.npsn || '-',
        nama_sekolah: user.administrasi_sekolah?.nama_sekolah || '-',
        alamat: user.administrasi_sekolah?.alamat || '-',
    };
};
</script>
