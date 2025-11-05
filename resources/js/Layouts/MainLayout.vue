<script setup>
import { Link, router } from '@inertiajs/vue3';
import { 
    Home,
    Menu,
    X,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Search,
    User,
    Users,
    LogIn,
    LogOut,
    Settings,
    Shield,
    Key,
    UserPlus,
    Bell,
    CheckCircle,
    AlertCircle,
    Info,
    XCircle,
    Loader,
    FileText,
    FilePlus,
    Folder,
    ClipboardList,
    Download,
    Upload,
    Printer,
    BarChart3,
    PieChart,
    LineChart,
    Database,
    Table,
    Wallet,
    Receipt,
    CreditCard,
    DollarSign,
    Plus,
    Minus,
    Edit3,
    Trash2,
    Eye,
    EyeOff,
    Save,
    RefreshCcw,
    Calendar,
    Clock,
    AlarmClock,
    Timer,
    Mail,
    Globe,
    MapPin,
    Camera,
    Image,
    Star,
} from 'lucide-vue-next'

import { ref, onMounted, onUnmounted } from 'vue';

const isNavOpen = ref(false);
const isUserMenuOpen = ref(false);
const isMobile = ref(false);
const activeDropdown = ref(null);

// Check screen size
const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 1024;
};

const toggleNav = () => {
    isNavOpen.value = !isNavOpen.value;
    activeDropdown.value = null;
};

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const toggleDropdown = (routeName) => {
    activeDropdown.value = activeDropdown.value === routeName ? null : routeName;
};

const handleLogout = () => {
    router.post(route('logout'));
};

// Close nav when clicking outside on mobile
const handleOutsideClick = (event) => {
    if (isMobile.value && isNavOpen.value) {
        const nav = document.querySelector('nav.mobile-menu');
        const toggleBtn = document.querySelector('[aria-label="toggle-nav"]');
        
        if (nav && !nav.contains(event.target) && 
            toggleBtn && !toggleBtn.contains(event.target)) {
            isNavOpen.value = false;
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

defineProps({
    user: {
        type: Object,
        required: true,
    },
});

// Navigation items
const navigationItems = [
    {
        name: 'Beranda',
        route: 'dashboard',
        icon: Home,
        description: 'Dashboard utama',
    },
    {
        name: 'Form PPEPP',
        route: 'form',
        icon: FileText,
        description: 'Form Evaluasi PPEPP',
    },
    {
        name: 'Evaluasi PPEPP',
        route: 'evaluation',
        icon: BarChart3,
        description: 'Hasil Evaluasi PPEPP',
    },
    {
        name: 'Komponen',
        route: null,
        icon: Info,
        description: 'Contoh Pemakaian Komponen',
        submenu: [
            {
                name: 'Card Component',
                route: 'component-card',
                icon: FileText,
                description: 'Contoh Komponen Card',
            },
            {
                name: 'Header Component',
                route: 'component-header',
                icon: FileText,
                description: 'Contoh Komponen Header',
            }
        ]
    }
];

// Check if route is active
const isRouteActive = (routeName) => {
    return routeName ? route().current(routeName) : false;
};

// Check if any submenu route is active
const isSubmenuActive = (submenu) => {
    return submenu?.some(item => isRouteActive(item.route)) ?? false;
};
</script>

<template>
    <div class="relative min-h-screen bg-white">
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-gray-200/50 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-20">
                    <!-- Left: Logo & Brand -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-[#059669] to-[#10b981] rounded-lg shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <Link :href="route('dashboard')" class="hidden sm:block">
                            <h1 class="text-lg font-bold text-gray-900">Evaluasi Adiwiyata</h1>
                            <p class="text-xs text-gray-500">Program Lingkungan</p>
                        </Link>
                    </div>

                    <!-- Center: Desktop Navigation -->
                    <div class="hidden lg:flex items-center gap-1">
                        <template v-for="item in navigationItems" :key="item.route">
                            <!-- Dropdown Menu -->
                            <div v-if="item.submenu" class="relative group">
                                <button 
                                    class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-green-600 transition-colors duration-200 rounded-lg group-hover:bg-green-50"
                                >
                                    <component :is="item.icon" class="w-5 h-5" />
                                    <span class="font-medium">{{ item.name }}</span>
                                    <ChevronDown class="w-4 h-4 transition-transform group-hover:rotate-180" />
                                </button>

                                <!-- Dropdown Content -->
                                <div class="absolute top-full left-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-2 group-hover:translate-y-0 z-40">
                                    <div class="p-2 space-y-1">
                                        <Link
                                            v-for="subitem in item.submenu"
                                            :key="subitem.route"
                                            :href="route(subitem.route)"
                                            class="flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-green-50 rounded-lg transition-colors group/item"
                                        >
                                            <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg group-hover/item:bg-green-100 transition-colors">
                                                <component :is="subitem.icon" class="w-4 h-4 text-gray-600 group-hover/item:text-green-600" />
                                            </div>
                                            <div>
                                                <p class="font-medium">{{ subitem.name }}</p>
                                                <p class="text-xs text-gray-500">{{ subitem.description }}</p>
                                            </div>
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Regular Menu Item -->
                            <Link
                                v-else
                                :href="route(item.route)"
                                class="flex items-center gap-2 px-4 py-2 text-gray-700 rounded-lg transition-all duration-200"
                                :class="isRouteActive(item.route)
                                    ? 'bg-green-100 text-green-700 font-semibold'
                                    : 'hover:bg-gray-100 hover:text-green-600'"
                            >
                                <component :is="item.icon" class="w-5 h-5" />
                                <span class="font-medium">{{ item.name }}</span>
                            </Link>
                        </template>
                    </div>

                    <!-- Right: User Menu & Notifications -->
                    <div class="flex items-center gap-3 sm:gap-4">
                        <!-- Desktop User Menu -->
                        <div class="hidden sm:block relative">
                            <button 
                                @click="toggleUserMenu"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors group"
                            >
                                <div class="flex items-center justify-center w-9 h-9 bg-gradient-to-br from-[#059669] to-[#10b981] rounded-lg shadow-sm">
                                    <User class="w-5 h-5 text-white" />
                                </div>
                                <div class="text-left hidden md:block">
                                    <!-- <p class="text-sm font-semibold text-gray-900">{{ user.name.split(' ')[0] }}</p> -->
                                    <p class="text-xs text-gray-500">Admin</p>
                                </div>
                                <ChevronDown class="w-4 h-4 text-gray-600 transition-transform" :class="{ 'rotate-180': isUserMenuOpen }" />
                            </button>

                            <!-- User Dropdown Menu -->
                            <div 
                                v-if="isUserMenuOpen"
                                class="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-40 animate-in fade-in slide-in-from-top-2 duration-200"
                            >
                                <div class="p-2 space-y-1">
                                    <Link 
                                        :href="route('profile.edit')"
                                        @click="isUserMenuOpen = false"
                                        class="flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors group/item"
                                    >
                                        <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg group-hover/item:bg-green-100 transition-colors">
                                            <Settings class="w-4 h-4 text-gray-600 group-hover/item:text-green-600" />
                                        </div>
                                        <div>
                                            <p class="font-medium">Pengaturan</p>
                                            <p class="text-xs text-gray-500">Kelola profil</p>
                                        </div>
                                    </Link>
                                    
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <button 
                                        @click="handleLogout"
                                        class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors group/item"
                                    >
                                        <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg group-hover/item:bg-red-200 transition-colors">
                                            <LogOut class="w-4 h-4 text-red-600" />
                                        </div>
                                        <div class="text-left">
                                            <p class="font-medium">Keluar</p>
                                            <p class="text-xs text-red-500">Akhiri sesi</p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Menu Toggle -->
                        <button
                            aria-label="toggle-nav"
                            @click="toggleNav"
                            class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <Menu v-if="!isNavOpen" class="w-6 h-6 text-gray-700" />
                            <X v-else class="w-6 h-6 text-gray-700" />
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Menu -->
                <transition
                    enter-active-class="transition-all duration-300"
                    enter-from-class="opacity-0 -translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-300"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-4"
                >
                    <div v-if="isNavOpen && isMobile" class="mobile-menu border-t border-gray-200 bg-white/50 backdrop-blur-sm">
                        <div class="px-4 py-4 space-y-2">
                            <template v-for="item in navigationItems" :key="item.route">
                                <!-- Dropdown Menu Mobile -->
                                <div v-if="item.submenu" class="space-y-2">
                                    <button 
                                        @click="toggleDropdown(item.name)"
                                        class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-green-50 rounded-lg transition-colors"
                                        :class="isSubmenuActive(item.submenu) ? 'bg-green-50 text-green-700 font-semibold' : ''"
                                    >
                                        <div class="flex items-center gap-3">
                                            <component :is="item.icon" class="w-5 h-5" />
                                            <span>{{ item.name }}</span>
                                        </div>
                                        <ChevronRight 
                                            class="w-4 h-4 transition-transform"
                                            :class="{ 'rotate-90': activeDropdown === item.name }"
                                        />
                                    </button>

                                    <!-- Submenu Items -->
                                    <transition
                                        enter-active-class="transition-all duration-300"
                                        enter-from-class="opacity-0 -translate-x-4"
                                        enter-to-class="opacity-100 translate-x-0"
                                        leave-active-class="transition-all duration-300"
                                        leave-from-class="opacity-100 translate-x-0"
                                        leave-to-class="opacity-0 -translate-x-4"
                                    >
                                        <div v-if="activeDropdown === item.name" class="space-y-1 pl-8">
                                            <Link
                                                v-for="subitem in item.submenu"
                                                :key="subitem.route"
                                                :href="route(subitem.route)"
                                                @click="isNavOpen = false"
                                                class="flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-green-50 rounded-lg transition-colors"
                                                :class="isRouteActive(subitem.route) ? 'bg-green-100 text-green-700 font-semibold' : ''"
                                            >
                                                <component :is="subitem.icon" class="w-4 h-4" />
                                                <span>{{ subitem.name }}</span>
                                            </Link>
                                        </div>
                                    </transition>
                                </div>

                                <!-- Regular Menu Item Mobile -->
                                <Link
                                    v-else
                                    :href="route(item.route)"
                                    @click="isNavOpen = false"
                                    class="flex items-center gap-3 w-full px-4 py-3 text-gray-700 hover:bg-green-50 rounded-lg transition-colors"
                                    :class="isRouteActive(item.route) ? 'bg-green-100 text-green-700 font-semibold' : ''"
                                >
                                    <component :is="item.icon" class="w-5 h-5" />
                                    <span>{{ item.name }}</span>
                                </Link>
                            </template>

                            <!-- Mobile User Menu -->
                            <div class="border-t border-gray-200 mt-4 pt-4 space-y-2">
                                <Link 
                                    :href="route('profile.edit')"
                                    @click="isNavOpen = false"
                                    class="flex items-center gap-3 w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg">
                                        <Settings class="w-4 h-4 text-gray-600" />
                                    </div>
                                    <span>Pengaturan</span>
                                </Link>
                                
                                <button 
                                    @click="handleLogout"
                                    class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg">
                                        <LogOut class="w-4 h-4 text-red-600" />
                                    </div>
                                    <span>Keluar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="min-h-screen">
            <slot />
        </main>
    </div>
</template>

<style scoped>
/* Smooth transitions */
nav {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Dropdown animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: fadeIn 0.2s ease-out;
}

/* Fade in animation */
.fade-in {
    animation: fadeIn 0.2s ease-out;
}

.slide-in-from-top-2 {
    animation: fadeIn 0.2s ease-out;
}

/* Responsive navbar */
@media (max-width: 1024px) {
    nav {
        height: 4rem;
    }
}

@media (max-width: 640px) {
    nav {
        height: 3.5rem;
    }
}
</style>