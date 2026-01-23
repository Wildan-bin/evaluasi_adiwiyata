<template>
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Upload Terbaru</h2>

        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            <p class="mt-2 text-sm text-gray-600">Memuat data...</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- User Column -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                    Per User
                </h3>
                <div class="space-y-3">
                    <p v-if="grouped.users.length === 0" class="text-sm text-gray-500">Tidak ada data</p>
                    <div
                        v-for="file in grouped.users"
                        :key="file.id"
                        class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ file.user_name }}</h4>
                            <span
                                :class="[
                                    'text-xs px-2 py-0.5 rounded-full font-medium',
                                    statusColors[file.status]
                                ]"
                            >
                                {{ file.status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-2">{{ file.indikator }}</p>
                        <p class="text-xs text-gray-500">{{ file.uploaded_at }}</p>
                        <a
                            :href="`/admin/users/${file.user_id}/files`"
                            class="mt-2 inline-block text-xs text-green-600 hover:text-green-800 font-medium"
                        >
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Administrasi Column -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                    Administrasi
                </h3>
                <div class="space-y-3">
                    <p v-if="grouped.administrasi.length === 0" class="text-sm text-gray-500">Tidak ada data</p>
                    <div
                        v-for="file in grouped.administrasi"
                        :key="file.id"
                        class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ file.user_name }}</h4>
                            <span
                                :class="[
                                    'text-xs px-2 py-0.5 rounded-full font-medium',
                                    statusColors[file.status]
                                ]"
                            >
                                {{ file.status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-2">{{ file.indikator }}</p>
                        <p class="text-xs text-gray-500">{{ file.uploaded_at }}</p>
                        <a
                            :href="`/admin/users/${file.user_id}/files`"
                            class="mt-2 inline-block text-xs text-green-600 hover:text-green-800 font-medium"
                        >
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Self-Assessment Column -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                    Self-Assessment
                </h3>
                <div class="space-y-3">
                    <p v-if="grouped.self_assessment.length === 0" class="text-sm text-gray-500">Tidak ada data</p>
                    <div
                        v-for="file in grouped.self_assessment"
                        :key="file.id"
                        class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ file.user_name }}</h4>
                            <span
                                :class="[
                                    'text-xs px-2 py-0.5 rounded-full font-medium',
                                    statusColors[file.status]
                                ]"
                            >
                                {{ file.status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mb-2">{{ file.indikator }}</p>
                        <p class="text-xs text-gray-500">{{ file.uploaded_at }}</p>
                        <a
                            :href="`/admin/users/${file.user_id}/files`"
                            class="mt-2 inline-block text-xs text-green-600 hover:text-green-800 font-medium"
                        >
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const latestUploads = ref([]);
const loading = ref(true);

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    revision_requested: 'bg-orange-100 text-orange-800',
};

onMounted(() => {
    loadLatestUploads();
});

const loadLatestUploads = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/file-upload/latest');
        latestUploads.value = response.data.data;
    } catch (error) {
        console.error('Error loading latest uploads:', error);
    } finally {
        loading.value = false;
    }
};

const grouped = computed(() => {
    return {
        users: latestUploads.value
            .reduce((acc, file) => {
                if (!acc.find(f => f.user_id === file.user_id)) {
                    acc.push(file);
                }
                return acc;
            }, [])
            .slice(0, 5),
        administrasi: latestUploads.value
            .filter(f => f.category === 'administrasi')
            .slice(0, 5),
        self_assessment: latestUploads.value
            .filter(f => f.category === 'self_assessment')
            .slice(0, 5),
    };
});
</script>
