<script setup>
import { ref } from 'vue';
import Card from "@/Components/Card.vue";
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { Home, BarChart3, Users, Settings, ChevronRight, Save, AlertCircle } from "lucide-vue-next";

// State untuk tabs
const activeTab = ref('penetapan');

// State untuk form responses - simpan jawaban setiap tab
const formResponses = ref({
  penetapan: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  pelaksanaan: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  evaluasi: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  pengendalian: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  },
  peningkatan: {
    q1: null,
    q2: null,
    q3: null,
    q4: null,
    q5: null,
  }
});

// State untuk tracking form completion
const isSubmitting = ref(false);
const submitMessage = ref('');

// Definisi tabs
const tabs = [
  { id: 'penetapan', label: 'Penetapan' },
  { id: 'pelaksanaan', label: 'Pelaksanaan' },
  { id: 'evaluasi', label: 'Evaluasi' },
  { id: 'pengendalian', label: 'Pengendalian' },
  { id: 'peningkatan', label: 'Peningkatan' }
];

// Definisi pertanyaan untuk setiap tab sesuai PPEPP Adiwiyata
const questions = {
  penetapan: [
    {
      id: 'q1',
      text: 'Apakah sekolah telah menetapkan kebijakan lingkungan yang jelas dan tertulis?',
    },
    {
      id: 'q2',
      text: 'Apakah ada tim khusus yang bertanggung jawab untuk program Adiwiyata?',
    },
    {
      id: 'q3',
      text: 'Apakah target dan indikator keberhasilan program lingkungan sudah ditetapkan?',
    },
    {
      id: 'q4',
      text: 'Apakah kurikulum sekolah mengintegrasikan pendidikan lingkungan hidup?',
    },
    {
      id: 'q5',
      text: 'Apakah ada alokasi anggaran untuk mendukung program Adiwiyata?',
    },
  ],
  pelaksanaan: [
    {
      id: 'q1',
      text: 'Apakah sekolah secara konsisten melaksanakan kegiatan pembelajaran tentang lingkungan?',
    },
    {
      id: 'q2',
      text: 'Apakah sekolah memiliki fasilitas pengelolaan sampah yang baik (TPS, komposting, daur ulang)?',
    },
    {
      id: 'q3',
      text: 'Apakah program hemat air dan listrik sudah diterapkan di sekolah?',
    },
    {
      id: 'q4',
      text: 'Apakah sekolah melakukan program penghijauan dan perawatan taman sekolah?',
    },
    {
      id: 'q5',
      text: 'Apakah partisipasi siswa dan guru dalam kegiatan lingkungan sudah berjalan aktif?',
    }
  ],
  evaluasi: [
    {
      id: 'q1',
      text: 'Apakah sekolah melakukan evaluasi berkala terhadap pencapaian program Adiwiyata?',
    },
    {
      id: 'q2',
      text: 'Apakah terdapat data pengukuran perubahan perilaku siswa terhadap lingkungan?',
    },
    {
      id: 'q3',
      text: 'Apakah sekolah mendokumentasikan hasil dan dampak program lingkungan?',
    },
    {
      id: 'q4',
      text: 'Apakah efektivitas pengelolaan sampah dan energi sudah diukur?',
    },
    {
      id: 'q5',
      text: 'Apakah ada mekanisme umpan balik dari stakeholder tentang program?',
    }
  ],
  pengendalian: [
    {
      id: 'q1',
      text: 'Apakah sekolah memiliki sistem monitoring rutin untuk pelaksanaan program?',
    },
    {
      id: 'q2',
      text: 'Apakah ada mekanisme kontrol kualitas untuk limbah dan sampah sekolah?',
    },
    {
      id: 'q3',
      text: 'Apakah terdapat SOP (Standar Operasional Prosedur) yang jelas dan dipatuhi?',
    },
    {
      id: 'q4',
      text: 'Apakah ada sistem pelaporan dan pencatatan kegiatan lingkungan secara teratur?',
    },
    {
      id: 'q5',
      text: 'Apakah sekolah melakukan koreksi atas temuan atau penyimpangan program?',
    }
  ],
  peningkatan: [
    {
      id: 'q1',
      text: 'Apakah sekolah melakukan upaya berkelanjutan untuk meningkatkan program Adiwiyata?',
    },
    {
      id: 'q2',
      text: 'Apakah ada pelatihan dan pengembangan kapasitas tim lingkungan sekolah?',
    },
    {
      id: 'q3',
      text: 'Apakah sekolah membangun kemitraan dengan komunitas atau lembaga lingkungan?',
    },
    {
      id: 'q4',
      text: 'Apakah ada program sosialisasi dan advokasi lingkungan ke masyarakat sekitar?',
    },
    {
      id: 'q5',
      text: 'Apakah sekolah terus mengembangkan infrastruktur hijau dan ramah lingkungan?',
    }
  ]
};

// Skala penilaian
const ratingScale = [
  { value: 1, label: 'Very Bad', color: 'bg-red-500', textColor: 'text-red-600', bgColor: 'bg-red-50', circleBg: 'bg-red-300' },
  { value: 2, label: 'Bad', color: 'bg-orange-500', textColor: 'text-orange-600', bgColor: 'bg-orange-50', circleBg: 'bg-orange-300' },
  { value: 3, label: 'Neutral', color: 'bg-gray-400', textColor: 'text-gray-600', bgColor: 'bg-gray-50', circleBg: 'bg-gray-300' },
  { value: 4, label: 'Good', color: 'bg-yellow-500', textColor: 'text-yellow-600', bgColor: 'bg-yellow-50', circleBg: 'bg-yellow-300' },
  { value: 5, label: 'Excellent', color: 'bg-green-500', textColor: 'text-green-600', bgColor: 'bg-green-50', circleBg: 'bg-green-300' }
];

// Function untuk hitung skor total tab
const calculateTabScore = (tabId) => {
  const responses = formResponses.value[tabId];
  const scores = Object.values(responses).filter(v => v !== null);
  if (scores.length === 0) return 0;
  return (scores.reduce((a, b) => a + b, 0) / scores.length).toFixed(2);
};

// Function untuk hitung progress tab
const calculateTabProgress = (tabId) => {
  const responses = formResponses.value[tabId];
  const totalQuestions = Object.keys(responses).length;
  const answered = Object.values(responses).filter(v => v !== null).length;
  return Math.round((answered / totalQuestions) * 100);
};

// Function untuk handle save
const handleSave = async () => {
  isSubmitting.value = true;
  submitMessage.value = '';

  try {
    // Simulasi API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Hitung skor total semua tab
    const totalScore = tabs.map(tab => parseFloat(calculateTabScore(tab.id)) || 0)
      .reduce((a, b) => a + b, 0) / tabs.length;
    
    submitMessage.value = `✅ Kuisioner berhasil disimpan! Skor rata-rata: ${totalScore.toFixed(2)}/5`;
    
    setTimeout(() => {
      submitMessage.value = '';
    }, 4000);
  } catch (error) {
    submitMessage.value = '❌ Gagal menyimpan kuisioner. Coba lagi!';
  } finally {
    isSubmitting.value = false;
  }
};

// Function untuk reset form
const handleReset = () => {
  if (confirm('Apakah Anda yakin ingin mereset semua jawaban?')) {
    Object.keys(formResponses.value).forEach(key => {
      Object.keys(formResponses.value[key]).forEach(q => {
        formResponses.value[key][q] = null;
      });
    });
    submitMessage.value = '';
  }
};
</script>

<template>
  <MainLayout>
    <!-- Header -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
      <Header
        title="Form Evaluasi Adiwiyata"
        description="Kelola dan monitor program lingkungan sekolah Anda melalui penilaian PPEPP"
        color="green"
        size="lg"
      />
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
      <!-- Tab Navigation - Dark Style -->
      <div class="bg-gray-100 rounded-lg overflow-hidden mb-8">
        <div class="flex flex-col sm:flex-row overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-6 py-4 font-semibold text-sm sm:text-base transition-all duration-300 whitespace-nowrap border-b-2 sm:border-b-0 sm:border-r',
              activeTab === tab.id
                ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg'
                : 'bg-gray-100 text-gray-600 hover:text-gray-900 hover:bg-gray-200'
            ]"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Progress Bar di bawah tabs -->
        <div class="bg-white px-6 py-3 border-t border-gray-200">
          <div class="flex items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
              <span class="text-green-600 font-semibold">{{ calculateTabProgress(activeTab) }}%</span> 
              selesai
            </div>
            <div class="flex-1 bg-gray-300 rounded-full h-2 overflow-hidden">
              <div 
                class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-300"
                :style="{ width: calculateTabProgress(activeTab) + '%' }"
              ></div>
            </div>
            <div class="text-sm text-gray-600">
              Skor: <span class="text-green-600 font-semibold">{{ calculateTabScore(activeTab) }}/5</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tab Content -->
      <div class="space-y-6">
        <!-- Questions Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
          <!-- Header dengan Rating Scale Labels -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
              {{ tabs.find(t => t.id === activeTab)?.label }}
            </h2>
            
            <!-- Rating Scale Header -->
            <div class="flex items-center gap-4 mb-8">
              <div class="flex-1"></div>
              <div class="flex gap-4 sm:gap-6">
                <div v-for="rating in ratingScale" :key="rating.value" class="text-center">
                  <p class="text-xs sm:text-sm font-semibold text-gray-700 whitespace-nowrap">{{ rating.label }}</p>
                </div>
              </div>
            </div>
            
            <div class="border-b-2 border-gray-300"></div>
          </div>

          <!-- Questions List -->
          <div class="space-y-6">
            <template v-for="(question, index) in questions[activeTab]" :key="question.id">
              <div class="flex items-start gap-4 pb-6 border-b border-gray-200 last:border-b-0 last:pb-0">
                <!-- Question Text -->
                <div class="flex-1 min-w-0">
                  <p class="text-sm sm:text-base text-gray-700 font-medium leading-relaxed">
                    {{ question.text }}
                  </p>
                </div>

                <!-- Rating Circles -->
                <div class="flex gap-3 sm:gap-4 flex-shrink-0">
                  <button
                    v-for="rating in ratingScale"
                    :key="rating.value"
                    @click="formResponses[activeTab][question.id] = rating.value"
                    :class="[
                      'w-8 h-8 sm:w-10 sm:h-10 rounded-full transition-all duration-200 transform hover:scale-110 active:scale-95 border-2',
                      formResponses[activeTab][question.id] === rating.value
                        ? `${rating.color} border-white shadow-lg`
                        : `${rating.circleBg} border-gray-300 hover:border-gray-400 cursor-pointer`
                    ]"
                    :title="rating.label"
                  ></button>
                </div>
              </div>
            </template>
          </div>

          <!-- Summary Section -->
          <div class="mt-8 pt-8 border-t-2 border-green-200 bg-green-50 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-4">
              <BarChart3 class="w-6 h-6 text-green-600" />
              <h3 class="font-bold text-gray-900">Ringkasan Tab {{ tabs.find(t => t.id === activeTab)?.label }}</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Total Pertanyaan</p>
                <p class="text-2xl font-bold text-green-600">{{ questions[activeTab].length }}</p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Sudah Dijawab</p>
                <p class="text-2xl font-bold text-green-600">
                  {{ Object.values(formResponses[activeTab]).filter(v => v !== null).length }}
                </p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Rata-rata Skor</p>
                <p class="text-2xl font-bold text-green-600">{{ calculateTabScore(activeTab) }}</p>
              </div>
              <div class="bg-white rounded-lg p-4 border border-green-200">
                <p class="text-sm text-gray-600 mb-1">Progress</p>
                <p class="text-2xl font-bold text-green-600">{{ calculateTabProgress(activeTab) }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <Transition
          enter-active-class="transition-all duration-300"
          enter-from-class="opacity-0 transform translate-y-4"
          enter-to-class="opacity-100 transform translate-y-0"
          leave-active-class="transition-all duration-300"
          leave-from-class="opacity-100 transform translate-y-0"
          leave-to-class="opacity-0 transform translate-y-4"
        >
          <div 
            v-if="submitMessage"
            :class="[
              'flex items-start gap-3 p-4 rounded-lg border',
              submitMessage.includes('✅') 
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800'
            ]"
          >
            <AlertCircle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <p class="font-medium">{{ submitMessage }}</p>
          </div>
        </Transition>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
          <button
            @click="handleReset"
            class="w-full sm:w-auto px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200 active:scale-95"
          >
            ↻ Reset Semua
          </button>

          <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <!-- Score Display -->
            <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border-2 border-green-200 rounded-lg">
              <BarChart3 class="w-5 h-5 text-green-600" />
              <span class="font-bold text-green-700">
                Total Skor: 
                <span class="text-lg">
                  {{ (tabs.map(t => parseFloat(calculateTabScore(t.id)) || 0).reduce((a, b) => a + b, 0) / tabs.length).toFixed(2) }}/5
                </span>
              </span>
            </div>

            <!-- Save Button -->
            <button
              @click="handleSave"
              :disabled="isSubmitting"
              :class="[
                'w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 font-semibold rounded-lg transition-all duration-200 active:scale-95',
                isSubmitting
                  ? 'bg-gray-400 text-white cursor-not-allowed'
                  : 'bg-green-600 text-white hover:bg-green-700 shadow-md hover:shadow-lg'
              ]"
            >
              <Save class="w-5 h-5" />
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kuisioner' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<style scoped>
/* Smooth transitions untuk semua elemen */
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar untuk tab navigation */
div::-webkit-scrollbar {
  height: 4px;
}

div::-webkit-scrollbar-track {
  background: transparent;
}

div::-webkit-scrollbar-thumb {
  background: linear-gradient(to right, #059669, #10b981);
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to right, #047857, #059669);
}
</style>