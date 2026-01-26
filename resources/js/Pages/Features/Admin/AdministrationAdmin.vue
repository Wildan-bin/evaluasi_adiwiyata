<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Search, 
    Eye, 
    CheckCircle, 
    XCircle, 
    Building2, 
    Users as UsersIcon, 
    FileCheck,
    ChevronRight,
    Filter,
    Download,
    FileText
} from 'lucide-vue-next';

// Props dari controller
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

// ============================================================================
// STATE
// ============================================================================
const searchQuery = ref('');
const statusFilter = ref('all'); // 'all', 'complete', 'incomplete'

// ============================================================================
// COMPUTED
// ============================================================================
const filteredUsers = computed(() => {
    let result = props.users;

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(user => 
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            user.nama_sekolah.toLowerCase().includes(query) ||
            user.npsn.toLowerCase().includes(query)
        );
    }

    // Filter by status
    if (statusFilter.value === 'complete') {
        result = result.filter(user => 
            user.status.sekolah && user.status.skTim && user.status.administrasiDasar
        );
    } else if (statusFilter.value === 'incomplete') {
        result = result.filter(user => 
            !user.status.sekolah || !user.status.skTim || !user.status.administrasiDasar
        );
    }

    return result;
});

const stats = computed(() => {
    const total = props.users.length;
    const complete = props.users.filter(u => 
        u.status.sekolah && u.status.skTim && u.status.administrasiDasar
    ).length;
    const incomplete = total - complete;

    return { total, complete, incomplete };
});

// ============================================================================
// METHODS
// ============================================================================
const viewDetail = (userId) => {
    router.visit(route('admin.administrasi.show', { user: userId }));
};

const getCompletionPercentage = (status) => {
    const completed = [status.sekolah, status.skTim, status.administrasiDasar].filter(Boolean).length;
    return Math.round((completed / 3) * 100);
};

const getStatusColor = (percentage) => {
    if (percentage === 100) return 'text-green-600 bg-green-100';
    if (percentage >= 50) return 'text-yellow-600 bg-yellow-100';
    return 'text-red-600 bg-red-100';
};
</script>

<template>
    <MainLayout>
        <Head title="Admin - Data Administrasi Sekolah" />

        <Header
            title="Data Administrasi Sekolah"
            description="Kelola dan pantau data administrasi dari semua sekolah"
            color="emerald"
        />

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <Building2 class="w-6 h-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Sekolah</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <CheckCircle class="w-6 h-6 text-green-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Lengkap</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.complete }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <XCircle class="w-6 h-6 text-red-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Belum Lengkap</p>
                                <p class="text-2xl font-bold text-red-600">{{ stats.incomplete }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1 relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama sekolah, user, email, atau NPSN..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            />
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="flex items-center gap-2">
                            <Filter class="w-5 h-5 text-gray-400" />
                            <select
                                v-model="statusFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            >
                                <option value="all">Semua Status</option>
                                <option value="complete">Lengkap</option>
                                <option value="incomplete">Belum Lengkap</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Sekolah / User
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center justify-center gap-1">
                                            <Building2 class="w-4 h-4" />
                                            Data Sekolah
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center justify-center gap-1">
                                            <UsersIcon class="w-4 h-4" />
                                            SK Tim
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center justify-center gap-1">
                                            <FileCheck class="w-4 h-4" />
                                            Adm. Dasar
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Progress
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="user in filteredUsers"
                                    :key="user.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <!-- Sekolah / User Info -->
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ user.nama_sekolah }}</p>
                                            <p class="text-xs text-gray-500">{{ user.name }} • {{ user.email }}</p>
                                            <p class="text-xs text-gray-400 mt-1">NPSN: {{ user.npsn }}</p>
                                        </div>
                                    </td>
                                    
                                    <!-- Data Sekolah Status -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center">
                                            <CheckCircle 
                                                v-if="user.status.sekolah" 
                                                class="w-6 h-6 text-green-500" 
                                            />
                                            <XCircle 
                                                v-else 
                                                class="w-6 h-6 text-red-400" 
                                            />
                                        </div>
                                    </td>
                                    
                                    <!-- SK Tim Status -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center">
                                            <CheckCircle 
                                                v-if="user.status.skTim" 
                                                class="w-6 h-6 text-green-500" 
                                            />
                                            <XCircle 
                                                v-else 
                                                class="w-6 h-6 text-red-400" 
                                            />
                                        </div>
                                    </td>
                                    
                                    <!-- Administrasi Dasar Status -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <CheckCircle 
                                                v-if="user.status.administrasiDasar" 
                                                class="w-6 h-6 text-green-500" 
                                            />
                                            <XCircle 
                                                v-else 
                                                class="w-6 h-6 text-red-400" 
                                            />
                                            <span class="text-xs text-gray-500 mt-1">
                                                {{ user.dokumen_count }} dokumen
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Progress -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <span 
                                                class="px-2 py-1 rounded-full text-xs font-semibold"
                                                :class="getStatusColor(getCompletionPercentage(user.status))"
                                            >
                                                {{ getCompletionPercentage(user.status) }}%
                                            </span>
                                            <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div 
                                                    class="h-full rounded-full transition-all"
                                                    :class="{
                                                        'bg-green-500': getCompletionPercentage(user.status) === 100,
                                                        'bg-yellow-500': getCompletionPercentage(user.status) >= 50 && getCompletionPercentage(user.status) < 100,
                                                        'bg-red-500': getCompletionPercentage(user.status) < 50
                                                    }"
                                                    :style="{ width: `${getCompletionPercentage(user.status)}%` }"
                                                ></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            @click="viewDetail(user.id)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors text-sm font-medium"
                                        >
                                            <Eye class="w-4 h-4" />
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Empty State -->
                                <tr v-if="filteredUsers.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <Building2 class="w-12 h-12 mb-2" />
                                            <p class="text-sm">Tidak ada data yang ditemukan</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="mt-4 text-sm text-gray-500 text-center">
                    Menampilkan {{ filteredUsers.length }} dari {{ users.length }} sekolah
                </div>

            </div>
        </div>
    </MainLayout>
</template>