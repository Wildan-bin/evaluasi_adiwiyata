<script setup>
import { Link, router } from '@inertiajs/vue3';
import { refreshCsrfToken } from '@/Composables/useCsrf';
import { 
    // 🔹 Navigasi & Umum
    Home,           // Halaman utama
    Menu,           // Tombol sidebar toggle / menu navigasi
    X,              // Tutup / hapus / close modal
    ChevronDown,    // Panah dropdown
    Leaf as LoginIcon,
    ChevronLeft,    // Navigasi kiri
    ChevronRight,   // Navigasi kanan
    Search,         // Pencarian

    // 🔹 User & Akun
    User,           // Profil pengguna
    Users,          // Daftar pengguna
    LogIn,          // Tombol masuk
    LogOut,         // Tombol keluar
    Settings,       // Pengaturan
    Shield,         // Keamanan akun / proteksi
    Key,            // Password / akses
    UserPlus,       // Tambah pengguna

    // 🔹 Notifikasi & Status
    Bell,           // Notifikasi
    CheckCircle,    // Status sukses
    AlertCircle,    // Peringatan
    Info,           // Informasi
    XCircle,        // Error / gagal
    Loader,         // Loading / proses

    // 🔹 File & Dokumen
    FileText,       // Dokumen teks
    FilePlus,       // Tambah dokumen
    Folder,         // Folder
    ClipboardList,  // Daftar / catatan
    Download,       // Unduh file
    Upload,         // Unggah file
    Printer,        // Cetak dokumen

    // 🔹 Data & Visualisasi
    BarChart3,      // Statistik / grafik batang
    PieChart,       // Grafik lingkaran
    LineChart,      // Grafik garis
    Database,       // Basis data
    Table,          // Tabel data

    // 🔹 Keuangan
    Wallet,         // Dompet / saldo
    Receipt,        // Transaksi / struk
    CreditCard,     // Pembayaran / kartu kredit
    DollarSign,     // Uang / harga

    // 🔹 Aksi Umum
    Plus,           // Tambah data
    Minus,          // Kurang / hapus data
    Edit3,          // Edit data
    Trash2,         // Hapus data
    Eye,            // Lihat detail
    EyeOff,         // Sembunyikan
    Save,           // Simpan perubahan
    RefreshCcw,     // Reload / muat ulang

    // 🔹 Kalender & Jadwal
    Calendar,       // Jadwal
    Clock,          // Waktu
    AlarmClock,     // Pengingat
    Timer,          // Stopwatch / durasi

    // 🔹 Lain-lain
    Mail,           // Email / pesan
    Globe,          // Web / lokasi global
    MapPin,         // Lokasi / alamat
    Camera,         // Kamera / gambar
    Image,          // Gambar / foto
    Star,           // Favorit / rating
} from 'lucide-vue-next'

import { ref, onMounted, onUnmounted } from 'vue';

const isSidebarOpen = ref(false); // Default closed on mobile
const isUserMenuOpen = ref(false);
const isMobile = ref(false);
const hoveredItem = ref(null);
const showTooltip = ref(false);

// Check screen size
const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 1024;
    // Auto-close sidebar on mobile when screen resizes
    if (isMobile.value) {
        isSidebarOpen.value = false;
    } else {
        isSidebarOpen.value = true;
    }
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    // Reset hover states when toggling
    hoveredItem.value = null;
    showTooltip.value = false;
    // Close user menu when toggling sidebar
    isUserMenuOpen.value = false;
};

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const handleLogout = async () => {
    // Refresh CSRF token sebelum logout
    await refreshCsrfToken();
    router.post(route('logout'));
};

// Handle tooltip showing in collapsed mode
const handleMouseEnter = (item) => {
    if (!isSidebarOpen.value && !isMobile.value) {
        hoveredItem.value = item;
        showTooltip.value = true;
    }
};

const handleMouseLeave = () => {
    hoveredItem.value = null;
    showTooltip.value = false;
};

// Close sidebar when clicking outside on mobile - FIXED
const handleOutsideClick = (event) => {
    if (isMobile.value && isSidebarOpen.value) {
        const sidebar = document.querySelector('aside');
        const mobileHeader = document.querySelector('.mobile-header');
        const toggleButton = document.querySelector('[aria-label="toggle-sidebar"]');
        
        // Jangan tutup sidebar jika klik pada tombol toggle atau sidebar itu sendiri
        if (sidebar && !sidebar.contains(event.target) && 
            mobileHeader && !mobileHeader.contains(event.target) &&
            toggleButton && !toggleButton.contains(event.target)) {
            isSidebarOpen.value = false;
        }
    }
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
    document.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
    document.removeEventListener('click', handleOutsideClick);
});

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});

// Navigation items with icons and metadata
const navigationItems = [
    {
        name: 'Beranda',
        route: 'dashboard',
        icon: Home,
        description: 'Dashboard utama',
        color: 'text-green-400'
    },
    {
        name: 'Administrasi',
        route: 'administration',
        icon: Upload,
        description: 'Unggah Bukti Administrasi',
        color: 'text-green-400'
    },
    {
        name: 'Self Assessment',
        route: 'form',
        icon: FileText,
        description: 'Form Evaluasi PPEPP',
        color: 'text-green-400'
    },
    {
        name: 'Evaluasi PBLHS',
        route: 'evaluation',
        icon: FileText,
        description: 'Hasil Evaluasi PPEPP',
        color: 'text-green-400'
    },
    {
        name: 'Admin Test',
        route: 'admin.test',
        icon: FileText,
        description: 'File Preview PDF',
        color: 'text-green-400'
    },
    {
        name: 'Contoh Komponen Card',
        route: 'component-card',
        icon: Info,
        description: 'Contoh Pemakaian Komponen',
        color: 'text-green-400'
    },
    {
        name: 'Contoh Komponen Header',
        route: 'component-header',
        icon: Info,
        description: 'Contoh Pemakaian Komponen',
        color: 'text-green-400'
    },
    // {
    //     name: 'Evaluasi',
    //     route: 'evaluasi.index',
    //     icon: Receipt,
    //     description: 'Kelola evaluasi',
    //     color: 'text-emerald-400'
    // },
    // {
    //     name: 'Laporan',
    //     route: 'reports.index',
    //     icon: BarChart3,
    //     description: 'Laporan Adiwiyata',
    //     color: 'text-teal-400'
    // },
    // {
    //     name: 'Pengaturan',
    //     route: 'settings.index',
    //     icon: Settings,
    //     description: 'Konfigurasi sistem',
    //     color: 'text-lime-400',
    //     routePattern: 'settings.*'
    // }
];

// Check if route is active
const isRouteActive = (routeName, routePattern = null) => {
    return routePattern ? route().current(routePattern) : route().current(routeName);
};
</script>

<template>
    <div class="relative min-h-screen bg-[FAF9F6]">
        <!-- Background gradient dengan warna hijau tema Adiwiyata: emerald (90), green (50), teal (50) -->
        
        <!-- Mobile Header Bar - FIXED Z-INDEX -->
        <div 
            v-if="isMobile" 
            class="mobile-header fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-b border-gray-200/50 shadow-sm"
        >
            <div class="flex items-center justify-between px-4 py-3">
                <!-- Mobile Logo & Menu Button -->
                <div class="flex items-center gap-3">
                    <button
                        aria-label="toggle-sidebar"
                        @click.stop="toggleSidebar" 
                        class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-[#059669] to-[#10b981] rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 relative z-10"
                    >
                        <!-- Gradient hijau untuk tombol: from-emerald-600 to-green-500 -->
                        <Menu v-if="!isSidebarOpen" class="w-5 h-5 text-white" />
                        <X v-else class="w-5 h-5 text-white" />
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-[#059669] to-[#10b981] rounded-lg">
                            <!-- Ikon brand dengan latar hijau -->
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-sm font-bold text-gray-900">
                                <a :href="route('dashboard')">Evaluasi Adiwiyata</a></h1>
                        </div>
                    </div>
                </div>

                <!-- Mobile User Info -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-[#059669] to-[#10b981] rounded-lg">
                        <!-- Avatar user dengan warna hijau -->
                        <User class="w-4 h-4 text-white" />
                    </div>
                    <div class="hidden xs:block">
                        <p class="text-sm font-medium text-gray-900">{{ user.name.split(' ')[0] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backdrop for mobile - FIXED Z-INDEX -->
        <div 
            v-if="isSidebarOpen && isMobile" 
            @click="toggleSidebar"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity duration-300"
        ></div>

        <!-- Sidebar - FIXED Z-INDEX AND POSITIONING -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white/98 backdrop-blur-xl shadow-2xl transition-all duration-500 border-r border-white/20"
            :class=" [
                // Width classes
                isMobile 
                    ? (isSidebarOpen ? 'w-96' : 'w-0 overflow-hidden') 
                    : (isSidebarOpen ? 'w-80' : 'w-32'),
                // Border radius classes  
                isMobile
                    ? 'rounded-r-none'
                    : (isSidebarOpen ? 'rounded-r-3xl' : 'rounded-r-2xl'),
                // Transform classes for mobile - FIXED
                isMobile ? (isSidebarOpen ? 'translate-x-0' : '-translate-x-full') : 'translate-x-0'
            ]"
        >
            <!-- Header Section - FIXED TO SHOW ON MOBILE -->
            <div class="relative overflow-hidden">
                <!-- Background Gradient - Warna hijau untuk header sidebar -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#059669] via-[#10b981] to-[#34d399]"></div>
                <!-- Gradient hijau dari emerald-600 via green-500 ke emerald-300 -->
                
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-4 right-4 w-16 sm:w-20 h-16 sm:h-20 rounded-full border-2 border-white/30"></div>
                    <div class="absolute bottom-2 right-6 sm:right-8 w-10 sm:w-12 h-10 sm:h-12 rounded-full border-2 border-white/20"></div>
                </div>

                <!-- Universal Header for Both Mobile & Desktop -->
                <div class="relative z-10 flex items-center justify-between p-4 sm:p-6 border-b border-white/10">
                    <!-- Logo and Brand -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl">
                            <!-- Ikon logo dengan background transparan -->
                            <LoginIcon class="text-white w-5 h-5 group-hover:translate-x-1 transition"/>
                        </div>
                        <!-- Show brand name when sidebar is open (both mobile and desktop) -->
                        <div v-if="isSidebarOpen" class="transition-all duration-300">
                            <h1 class="text-lg sm:text-xl font-bold text-white leading-tight">Greenedu</h1>
                            <p class="text-xs text-white/70 hidden sm:block">Program Lingkungan</p>
                        </div>
                    </div>

                    <!-- Toggle Button - Enhanced for all modes -->
                    <button
                        aria-label="toggle-sidebar"
                        @click.stop="toggleSidebar" 
                        class="relative flex items-center justify-center transition-all duration-200 group z-10"
                        :class=" [
                            isSidebarOpen 
                                ? 'w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30' 
                                : 'w-12 h-12 bg-white/30 backdrop-blur-md rounded-xl hover:bg-white/40 shadow-lg hover:shadow-xl'
                        ]"
                    >
                        <!-- Enhanced Menu Icon for collapsed mode -->
                        <div v-if="!isSidebarOpen" class="relative">
                            <Menu class="w-6 h-6 text-white group-hover:scale-110 transition-transform" />
                            <!-- Active indicator dots for collapsed mode -->
                            <div class="absolute -top-1 -right-1 flex gap-0.5">
                                <div 
                                    v-for="(item, index) in navigationItems" 
                                    :key="index"
                                    class="w-1.5 h-1.5 rounded-full transition-all duration-200"
                                    :class=" [
                                        isRouteActive(item.route, item.routePattern) 
                                            ? 'bg-lime-300 shadow-sm' 
                                            : 'bg-white/40'
                                    ]"
                                ></div> 
                            </div>
                        </div>
                        <X v-else class="w-4 h-4 text-white group-hover:scale-110 transition-transform" />
                    </button>
                </div>

                <!-- Welcome Message for Mobile & Tablet -->
                
            </div>

            <!-- Navigation Section -->
            <nav class="flex-1 p-3 sm:p-4 space-y-2 overflow-y-auto">
                <div v-if="isSidebarOpen" class="px-3 py-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Utama</p>
                </div>

                <!-- Navigation Links -->
                <template v-for="item in navigationItems" :key="item.route">
                    <div class="relative">
                        <Link
                            add :href="route(item.route)"
                            @mouseenter="handleMouseEnter(item)"
                            @mouseleave="handleMouseLeave"
                            @click="isMobile && (isSidebarOpen = false)"
                            class="group relative flex items-center transition-all duration-200 hover:shadow-sm active:scale-95"
                            :class=" [
                                isSidebarOpen ? 'rounded-xl sm:rounded-2xl p-3' : 'rounded-xl p-3 mx-auto w-14 h-14 justify-center',
                                isSidebarOpen 
                                    ? 'hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50' 
                                    : 'hover:bg-gradient-to-br hover:from-emerald-50 hover:to-white hover:scale-105',
                                isRouteActive(item.route, item.routePattern) 
                                    ? isSidebarOpen
                                        ? 'bg-gradient-to-r from-[#059669]/10 to-[#10b981]/10 text-[#059669] shadow-sm border-l-4 border-[#059669]'
                                        : 'bg-gradient-to-br from-[#059669]/20 to-[#10b981]/20 text-[#059669] shadow-lg scale-105 ring-2 ring-[#059669]/30'
                                    : 'text-gray-700 hover:text-[#059669]'
                            ]"
                        >
                            <!-- Active Indicator for expanded mode -->
                            <div 
                                v-if="isRouteActive(item.route, item.routePattern) && isSidebarOpen" 
                                class="absolute right-3 w-2 h-2 bg-[#059669] rounded-full animate-pulse"
                            ></div>

                            <!-- Active Indicator for collapsed mode -->
                            <div 
                                v-if="isRouteActive(item.route, item.routePattern) && !isSidebarOpen" 
                                class="absolute -top-1 -right-1 w-3 h-3 bg-[#059669] rounded-full animate-pulse shadow-sm"
                            ></div>

                            <!-- Icon Container -->
                            <div 
                                class="relative flex items-center justify-center transition-all duration-200"
                                :class=" [
                                    isSidebarOpen ? 'w-10 h-10 rounded-xl' : 'w-8 h-8 rounded-lg',
                                    isRouteActive(item.route, item.routePattern) 
                                        ? ' text-[#059669]' 
                                        : ' text-dark'
                                ]"
                            >
                                <component 
                                    :is="item.icon" 
                                    class="transition-transform group-hover:scale-110" 
                                    :class="isSidebarOpen ? 'w-5 h-5' : 'w-6 h-6'"
                                />
                            </div>

                            <!-- Text Content for expanded mode -->
                            <div v-if="isSidebarOpen" class="ml-4 flex-1 transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-sm leading-tight">{{ item.name }}</p>
                                        <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors">
                                            {{ item.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hover Effect -->
                            <div 
                                class="absolute inset-0 bg-gradient-to-r from-[#059669]/5 to-[#10b981]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                :class="isSidebarOpen ? 'rounded-xl sm:rounded-2xl' : 'rounded-xl'"
                            ></div>
                        </Link>

                        <!-- Tooltip for collapsed mode (desktop only) -->
                        <div 
                            v-if="!isSidebarOpen && !isMobile && hoveredItem === item && showTooltip"
                            class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-xl z-[70] whitespace-nowrap animate-in slide-in-from-left-2 duration-200"
                        >
                            <div class="font-medium">{{ item.name }}</div>
                            <div class="text-xs text-gray-300">{{ item.description }}</div>
                            <!-- Arrow -->
                            <div class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
                        </div>
                    </div>
                </template>

                

                <!-- Collapsed mode info -->
                <div v-if="!isSidebarOpen && !isMobile" class="px-2 py-3 text-center">
                    <div class="w-8 h-8 mx-auto bg-gradient-to-br from-[#059669]/10 to-[#10b981]/10 rounded-lg flex items-center justify-center mb-2">
                        <div class="w-2 h-2 bg-[#059669] rounded-full animate-pulse"></div>
                    </div>
                </div>
            </nav>

            <!-- User Menu Section - FIXED POSITIONING -->
            <div class="border-t border-gray-100 bg-white/80 backdrop-blur-sm">
                <div class="p-3 sm:p-4">
                    <div class="relative">
                        <button 
                            @click.stop="toggleUserMenu" 
                            @mouseenter="handleMouseEnter({ name: user.name, description: 'Pengaturan akun' })"
                            @mouseleave="handleMouseLeave"
                            class="group w-full flex items-center text-gray-700 transition-all duration-200 hover:shadow-sm active:scale-95 relative z-10"
                            :class=" [
                                isSidebarOpen 
                                    ? 'rounded-xl sm:rounded-2xl p-3 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50' 
                                    : 'rounded-xl p-2 justify-center hover:bg-gradient-to-br hover:from-emerald-50 hover:to-green-50 hover:scale-105'
                            ]"
                        >
                            <!-- Avatar - Warna hijau untuk avatar user -->
                            <div class="relative flex items-center justify-center bg-gradient-to-br from-[#059669] to-[#10b981] rounded-xl shadow-sm"
                                 :class="isSidebarOpen ? 'w-10 h-10' : 'w-12 h-12'">
                                <User :class="isSidebarOpen ? 'w-5 h-5' : 'w-6 h-6'" class="text-white" />
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>

                            <!-- User Info -->
                            <!-- <div v-if="isSidebarOpen" class="ml-3 flex-1 text-left">
                                <p class="font-semibold text-sm text-gray-900 leading-tight">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            </div> -->

                            <!-- Chevron -->
                            <ChevronDown 
                                v-if="isSidebarOpen" 
                                class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': isUserMenuOpen }"
                            />
                        </button>

                        <!-- Tooltip for user menu in collapsed mode -->
                        <!-- <div 
                            v-if="!isSidebarOpen && !isMobile && hoveredItem?.name === user.name && showTooltip"
                            class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-xl z-[70] whitespace-nowrap animate-in slide-in-from-left-2 duration-200"
                        >
                            <div class="font-medium">{{ user.name }}</div>
                            <div class="text-xs text-gray-300">{{ user.email }}</div>
                            <div class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
                        </div> -->

                        <!-- Dropdown Menu - FIXED Z-INDEX -->
                        <div 
                            v-if="isUserMenuOpen && isSidebarOpen" 
                            class="absolute bottom-full left-0 mb-2 w-full bg-white rounded-xl sm:rounded-2xl shadow-xl border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-2 duration-200 z-[60]"
                        >
                            <div class="p-2 space-y-1">
                                <Link 
                                    :href="route('profile.edit')" 
                                    @click="isMobile && (isSidebarOpen = false)"
                                    class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition-colors duration-150 group active:scale-95"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg group-hover:bg-emerald-100 transition-colors mr-3">
                                        <!-- Icon hover dengan warna hijau -->
                                        <Settings class="w-4 h-4 text-gray-600 group-hover:text-emerald-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Pengaturan</p>
                                        <p class="text-xs text-gray-500">Kelola profil Anda</p>
                                    </div>
                                </Link>
                                
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                <button 
                                    @click="handleLogout" 
                                    class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors duration-150 group active:scale-95"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors mr-3">
                                        <LogOut class="w-4 h-4 text-red-600" />
                                    </div>
                                    <div class="text-left">
                                        <p class="font-medium">Keluar</p>
                                        <p class="text-xs text-red-500">Akhiri sesi Anda</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div 
            class="transition-all duration-300" 
            :class="{
                'ml-80': isSidebarOpen && !isMobile, 
                'ml-32': !isSidebarOpen && !isMobile,
                'ml-0': isMobile,
                'pt-16': isMobile
            }"
        >
            <main class="min-h-screen">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes slide-in-from-bottom {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in-from-left {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation-fill-mode: both;
}

.slide-in-from-bottom-2 {
    animation: slide-in-from-bottom 0.2s ease-out;
}

.slide-in-from-left-2 {
    animation: slide-in-from-left 0.2s ease-out;
}

/* Custom scrollbar - Warna scrollbar hijau */
nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: transparent;
}

nav::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #059669, #10b981);
    border-radius: 10px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #047857, #059669);
}

/* Enhanced hover effects */
.group:hover {
    transform: translateX(2px);
}

/* Active link glow effect */
.border-l-4 {
    box-shadow: 0 0 0 1px rgba(5, 150, 105, 0.1);
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(16px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(16px);
    }
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    .backdrop-blur-lg {
        backdrop-filter: blur(12px);
    }
}

/* Mobile specific styles */
@media (max-width: 1024px) {
    aside {
        position: fixed;
        height: 100vh;
        height: 100dvh; /* For mobile browsers */
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .group:hover {
        transform: none;
    }
    
    .active\:scale-95:active {
        transform: scale(0.95);
    }
}

/* Tooltip z-index fix */
.z-60 {
    z-index: 60;
}

.z-\[60\] {
    z-index: 60;
}

.z-\[70\] {
    z-index: 70;
}

/* Extra small screens */
@media (max-width: 480px) {
    .xs\:block {
        display: block;
    }
}

/* Prevent layout shift on mobile */
@media (max-width: 1024px) {
    .transition-all {
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    }
}

/* Safe area for modern mobile devices */
@supports (padding-top: env(safe-area-inset-top)) {
    @media (max-width: 1024px) {
        .fixed.top-0 {
            padding-top: env(safe-area-inset-top);
        }
    }
}
</style>