<script setup>
import { computed } from 'vue';

/**
 * Card Component - Komponen kartu yang fleksibel dan reusable
 * 
 * Props yang tersedia:
 * - title (String): Judul kartu
 * - description (String): Deskripsi singkat
 * - icon (Component): Ikon lucide-vue-next (opsional)
 * - color (String): Warna tema - 'green', 'blue', 'purple', 'orange', 'red' (default: 'green')
 * - variant (String): Varian tampilan - 'elevated', 'outlined', 'filled' (default: 'elevated')
 * - clickable (Boolean): Membuat kartu clickable (default: false)
 * - hoverable (Boolean): Menambahkan efek hover (default: true)
 * - loading (Boolean): Tampilkan loading state (default: false)
 * - link (String): URL untuk navigasi (opsional)
 * - external (Boolean): Buka link di tab baru (default: false)
 * - action (Function): Callback ketika kartu diklik
 * 
 * Slot yang tersedia:
 * - default: Konten utama kartu
 * - header: Area header custom
 * - footer: Area footer custom
 * - icon: Custom icon area
 * 
 * Contoh penggunaan:
 * <Card 
 *   title="Dashboard"
 *   description="Lihat ringkasan data"
 *   color="green"
 *   :clickable="true"
 *   @action="handleClick"
 * >
 *   <p>Konten custom di sini</p>
 * </Card>
 */

const props = defineProps({
  // Konten
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  
  // Styling
  color: {
    type: String,
    default: 'green',
    validator: (value) => ['green', 'blue', 'purple', 'orange', 'red', 'emerald', 'teal'].includes(value)
  },
  variant: {
    type: String,
    default: 'elevated',
    validator: (value) => ['elevated', 'outlined', 'filled'].includes(value)
  },
  
  // Behavior
  clickable: {
    type: Boolean,
    default: false
  },
  hoverable: {
    type: Boolean,
    default: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  
  // Navigation
  link: {
    type: String,
    default: null
  },
  external: {
    type: Boolean,
    default: false
  },
  
  // Icon
  icon: {
    type: [Object, null],
    default: null
  }
});

const emit = defineEmits(['action']);

// Hitung kelas CSS berdasarkan color
const colorClasses = computed(() => {
  const colorMap = {
    green: {
      bg: 'bg-green-50',
      border: 'border-green-200',
      text: 'text-green-600',
      icon: 'text-green-500',
      hover: 'hover:bg-green-100 hover:border-green-300',
      badge: 'bg-green-100 text-green-700'
    },
    emerald: {
      bg: 'bg-emerald-50',
      border: 'border-emerald-200',
      text: 'text-emerald-600',
      icon: 'text-emerald-500',
      hover: 'hover:bg-emerald-100 hover:border-emerald-300',
      badge: 'bg-emerald-100 text-emerald-700'
    },
    teal: {
      bg: 'bg-teal-50',
      border: 'border-teal-200',
      text: 'text-teal-600',
      icon: 'text-teal-500',
      hover: 'hover:bg-teal-100 hover:border-teal-300',
      badge: 'bg-teal-100 text-teal-700'
    },
    blue: {
      bg: 'bg-blue-50',
      border: 'border-blue-200',
      text: 'text-blue-600',
      icon: 'text-blue-500',
      hover: 'hover:bg-blue-100 hover:border-blue-300',
      badge: 'bg-blue-100 text-blue-700'
    },
    purple: {
      bg: 'bg-purple-50',
      border: 'border-purple-200',
      text: 'text-purple-600',
      icon: 'text-purple-500',
      hover: 'hover:bg-purple-100 hover:border-purple-300',
      badge: 'bg-purple-100 text-purple-700'
    },
    orange: {
      bg: 'bg-orange-50',
      border: 'border-orange-200',
      text: 'text-orange-600',
      icon: 'text-orange-500',
      hover: 'hover:bg-orange-100 hover:border-orange-300',
      badge: 'bg-orange-100 text-orange-700'
    },
    red: {
      bg: 'bg-red-50',
      border: 'border-red-200',
      text: 'text-red-600',
      icon: 'text-red-500',
      hover: 'hover:bg-red-100 hover:border-red-300',
      badge: 'bg-red-100 text-red-700'
    }
  };
  
  return colorMap[props.color] || colorMap.green;
});

// Hitung kelas CSS berdasarkan variant
const variantClasses = computed(() => {
  const variantMap = {
    elevated: `${colorClasses.value.bg} shadow-md hover:shadow-lg border border-gray-100`,
    outlined: `bg-white border-2 ${colorClasses.value.border}`,
    filled: `${colorClasses.value.bg} border border-gray-200`
  };
  
  return variantMap[props.variant];
});

// Handle click action
const handleCardClick = () => {
  if (props.clickable || props.link) {
    emit('action');
    
    if (props.link) {
      if (props.external) {
        window.open(props.link, '_blank');
      } else {
        window.location.href = props.link;
      }
    }
  }
};
</script>

<template>
  <div
    class="relative rounded-xl p-6 transition-all duration-300"
    :class="[
      variantClasses,
      {
        'cursor-pointer': clickable || link,
        [colorClasses.hover]: (clickable || link) && hoverable,
        'active:scale-95': clickable || link,
        'opacity-50 pointer-events-none': loading
      }
    ]"
    @click="handleCardClick"
    role="article"
  >
    <!-- Loading Overlay -->
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm rounded-xl z-10">
      <div class="flex flex-col items-center gap-2">
        <div class="w-6 h-6 border-3 border-gray-300 border-t-green-600 rounded-full animate-spin"></div>
        <p class="text-sm text-gray-600 font-medium">Loading...</p>
      </div>
    </div>

    <!-- Custom Header Slot -->
    <slot name="header">
      <!-- Default Header dengan Icon dan Title -->
      <div v-if="title" class="flex items-start gap-4 mb-4">
        <!-- Icon Area -->
        <slot name="icon">
          <div v-if="icon" :class="[
            'flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center',
            colorClasses.badge
          ]">
            <component :is="icon" class="w-6 h-6" :class="colorClasses.icon" />
          </div>
        </slot>

        <!-- Title & Description -->
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ title }}</h3>
          <p v-if="description" class="text-sm text-gray-600 line-clamp-2">{{ description }}</p>
        </div>

        <!-- Badge Indikator untuk clickable cards -->
        <div v-if="clickable || link" class="flex-shrink-0">
          <div class="w-2 h-2 rounded-full" :class="colorClasses.badge"></div>
        </div>
      </div>
    </slot>

    <!-- Main Content Slot -->
    <div class="mb-4">
      <slot />
    </div>

    <!-- Footer Slot -->
    <slot name="footer" />
  </div>
</template>

<style scoped>
/* Animasi custom untuk loading */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* /* Smooth transitions -->
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.duration-300 {
  transition-duration: 300ms;
}

/* Line clamp untuk text panjang -->
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
} */
</style>