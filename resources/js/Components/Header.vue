<script setup>
import { computed } from 'vue';

/**
 * Header Component - Komponen header untuk menampilkan judul dan deskripsi halaman
 * 
 * Props yang tersedia:
 * - title (String): Judul halaman - REQUIRED
 * - description (String): Deskripsi halaman (opsional)
 * - subtitle (String): Subjudul tambahan (opsional)
 * - icon (Component): Ikon dari lucide-vue-next (opsional)
 * - color (String): Warna tema - 'green', 'emerald', 'teal', 'blue', 'purple', 'orange', 'red' (default: 'green')
 * - size (String): Ukuran header - 'sm', 'md', 'lg' (default: 'md')
 * - showBreadcrumb (Boolean): Tampilkan breadcrumb (default: false)
 * - breadcrumbs (Array): Data breadcrumb - contoh: [{name: 'Home', href: '/'}, {name: 'Dashboard', href: null}]
 * - variant (String): Gaya header - 'default', 'gradient', 'minimal' (default: 'default')
 * - badge (String): Badge teks di sebelah judul (opsional)
 * - badgeColor (String): Warna badge (default: 'gray')
 * 
 * Slot yang tersedia:
 * - default: Konten custom di area kanan header
 * - icon: Custom icon area
 * - badge: Custom badge area
 * 
 * Contoh penggunaan:
 * <Header 
 *   title="Dashboard Adiwiyata"
 *   description="Kelola dan monitor program lingkungan sekolah"
 *   :icon="BarChart3"
 *   color="green"
 *   size="lg"
 * />
 */

const props = defineProps({
  // Konten utama
  title: {
    type: String,
    required: true,
    validator: (value) => value.length > 0
  },
  description: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },

  // Styling
  color: {
    type: String,
    default: 'green',
    validator: (value) => ['green', 'emerald', 'teal', 'blue', 'purple', 'orange', 'red'].includes(value)
  },
  size: {
    type: String,
    default: 'lg',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'gradient', 'minimal'].includes(value)
  },

  // Icon
  icon: {
    type: [Object, null],
    default: null
  },

  // Badge
  badge: {
    type: String,
    default: ''
  },
  badgeColor: {
    type: String,
    default: 'gray',
    validator: (value) => ['gray', 'green', 'emerald', 'blue', 'red', 'orange', 'purple'].includes(value)
  },

  // Breadcrumb
  showBreadcrumb: {
    type: Boolean,
    default: false
  },
  breadcrumbs: {
    type: Array,
    default: () => []
  }
});

// Hitung kelas CSS berdasarkan color
const colorClasses = computed(() => {
  const colorMap = {
    green: {
      bg: 'bg-green-50',
      border: 'border-green-200',
      text: 'text-green-600',
      icon: 'text-green-500',
      gradient: 'from-green-400 to-emerald-600',
      badge: 'bg-green-100 text-green-700',
      line: 'bg-green-500'
    },
    emerald: {
      bg: 'bg-emerald-50',
      border: 'border-emerald-200',
      text: 'text-emerald-600',
      icon: 'text-emerald-500',
      gradient: 'from-emerald-400 to-teal-600',
      badge: 'bg-emerald-100 text-emerald-700',
      line: 'bg-emerald-500'
    },
    teal: {
      bg: 'bg-teal-50',
      border: 'border-teal-200',
      text: 'text-teal-600',
      icon: 'text-teal-500',
      gradient: 'from-teal-400 to-cyan-600',
      badge: 'bg-teal-100 text-teal-700',
      line: 'bg-teal-500'
    },
    blue: {
      bg: 'bg-blue-50',
      border: 'border-blue-200',
      text: 'text-blue-600',
      icon: 'text-blue-500',
      gradient: 'from-blue-400 to-indigo-600',
      badge: 'bg-blue-100 text-blue-700',
      line: 'bg-blue-500'
    },
    purple: {
      bg: 'bg-purple-50',
      border: 'border-purple-200',
      text: 'text-purple-600',
      icon: 'text-purple-500',
      gradient: 'from-purple-400 to-pink-600',
      badge: 'bg-purple-100 text-purple-700',
      line: 'bg-purple-500'
    },
    orange: {
      bg: 'bg-orange-50',
      border: 'border-orange-200',
      text: 'text-orange-600',
      icon: 'text-orange-500',
      gradient: 'from-orange-400 to-red-600',
      badge: 'bg-orange-100 text-orange-700',
      line: 'bg-orange-500'
    },
    red: {
      bg: 'bg-red-50',
      border: 'border-red-200',
      text: 'text-red-600',
      icon: 'text-red-500',
      gradient: 'from-red-400 to-pink-600',
      badge: 'bg-red-100 text-red-700',
      line: 'bg-red-500'
    }
  };

  return colorMap[props.color] || colorMap.green;
});

// Hitung kelas CSS berdasarkan size
const sizeClasses = computed(() => {
  const sizeMap = {
    sm: {
      container: 'px-4 py-4',
      title: 'text-xl',
      description: 'text-sm',
      icon: 'w-8 h-8',
      iconBg: 'w-10 h-10'
    },
    md: {
      container: 'px-6 py-6',
      title: 'text-2xl',
      description: 'text-base',
      icon: 'w-6 h-6',
      iconBg: 'w-12 h-12'
    },
    lg: {
      container: 'px-8 py-8',
      title: 'text-4xl',
      description: 'text-lg',
      icon: 'w-7 h-7',
      iconBg: 'w-14 h-14'
    }
  };

  return sizeMap[props.size] || sizeMap.md;
});

// Hitung kelas CSS untuk badge color
const badgeColorClasses = computed(() => {
  const badgeMap = {
    gray: 'bg-gray-100 text-gray-700',
    green: 'bg-green-100 text-green-700',
    emerald: 'bg-emerald-100 text-emerald-700',
    blue: 'bg-blue-100 text-blue-700',
    red: 'bg-red-100 text-red-700',
    orange: 'bg-orange-100 text-orange-700',
    purple: 'bg-purple-100 text-purple-700'
  };

  return badgeMap[props.badgeColor] || badgeMap.gray;
});

// Hitung kelas CSS untuk variant
const variantClasses = computed(() => {
  const variantMap = {
    default: `${colorClasses.value.bg} `,
    gradient: `bg-gradient-to-r ${colorClasses.value.gradient} text-white`,
    minimal: 'bg-transparent border-b-2'
  };

  return variantMap[props.variant];
});

// Kelas untuk teks berdasarkan variant
const textColorClass = computed(() => {
  if (props.variant === 'gradient') {
    return 'text-white';
  }
  return 'text-gray-900';
});

const descriptionColorClass = computed(() => {
  if (props.variant === 'gradient') {
    return 'text-white/90';
  }
  return 'text-gray-600';
});
</script>

<template>
  <div class="w-full">
    <!-- Breadcrumb Navigation (opsional) -->
    <div v-if="showBreadcrumb && breadcrumbs.length > 0" class="px-6 py-3 bg-white border-b border-gray-200">
      <nav class="flex items-center gap-2 text-sm" aria-label="Breadcrumb">
        <template v-for="(crumb, index) in breadcrumbs" :key="index">
          <!-- Separator -->
          <span v-if="index > 0" class="text-gray-400">/</span>
          
          <!-- Breadcrumb Item -->
          <a 
            v-if="crumb.href"
            :href="crumb.href"
            class="text-blue-600 hover:text-blue-700 hover:underline transition-colors"
          >
            {{ crumb.name }}
          </a>
          <span v-else class="text-gray-600">
            {{ crumb.name }}
          </span>
        </template>
      </nav>
    </div>

    <!-- Header Main Content -->
    <div 
      :class="[
        'relative w-full transition-all duration-300',
        sizeClasses.container,
        variantClasses
      ]"
    >
      <!-- Decorative Background Pattern (untuk variant gradient) -->
      <div v-if="props.variant === 'gradient'" class="absolute inset-0 opacity-10 rounded-xl sm:rounded-2xl">
        <div class="absolute top-4 right-6 w-20 h-20 rounded-full border-2 border-white/30"></div>
        <div class="absolute bottom-2 right-12 w-12 h-12 rounded-full border-2 border-white/20"></div>
      </div>

      <!-- Container dengan flexbox untuk layout responsif -->
      <div class="relative z-10 flex items-start justify-between gap-4 sm:gap-6">
        <!-- Left Section: Icon + Content -->
        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
          <!-- Icon Area -->
          <slot name="icon">
            <div 
              v-if="icon"
              :class="[
                'flex-shrink-0 rounded-lg sm:rounded-xl flex items-center justify-center transition-transform duration-300 hover:scale-110',
                sizeClasses.iconBg,
                props.variant === 'gradient' ? 'bg-white/20 backdrop-blur-sm' : colorClasses.badge
              ]"
            >
              <component 
                :is="icon" 
                :class="[
                  sizeClasses.icon,
                  props.variant === 'gradient' ? 'text-white' : colorClasses.icon
                ]"
              />
            </div>
          </slot>

          <!-- Content Area -->
          <div class="flex-1 min-w-0">
            <!-- Title + Badge -->
            <div class="flex items-start gap-2 mb-1 flex-wrap">
              <h1 
                :class="[
                  'font-bold leading-tight',
                  sizeClasses.title,
                  textColorClass
                ]"
              >
                {{ title }}
              </h1>
              
              <!-- Badge (opsional) -->
              <slot name="badge">
                <span 
                  v-if="badge"
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold flex-shrink-0 mt-0.5',
                    badgeColorClasses
                  ]"
                >
                  {{ badge }}
                </span>
              </slot>
            </div>

            <!-- Subtitle (opsional) -->
            <p v-if="subtitle" :class="['text-sm font-medium mb-2', descriptionColorClass]">
              {{ subtitle }}
            </p>

            <!-- Description -->
            <p 
              v-if="description"
              :class="[
                'text-sm sm:text-base line-clamp-2',
                descriptionColorClass
              ]"
            >
              {{ description }}
            </p>

            <!-- Accent Line untuk variant minimal -->
            <div 
              v-if="props.variant === 'minimal'" 
              class="h-1 w-16 rounded-full mt-3"
              :style="{ backgroundColor: colorClasses.line }"
            ></div>
          </div>
        </div>

        <!-- Right Section: Custom Content Slot -->
        <div class="flex-shrink-0 hidden sm:block">
          <slot />
        </div>
      </div>

      <!-- Mobile Right Section: Custom Content Slot -->
      <div class="sm:hidden mt-4 pt-4 border-t" :class="props.variant === 'gradient' ? 'border-white/20' : 'border-gray-200'">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.duration-300 {
  transition-duration: 300ms;
}

/* Line clamp untuk text panjang */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hover effect untuk icon */
.hover\:scale-110:hover {
  transform: scale(1.1);
}
</style>