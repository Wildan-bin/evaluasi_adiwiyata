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
    q6: null,
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

// Definisi pertanyaan untuk setiap tab sesuai PPEPP Greenedu
const questions = {
  penetapan: [
    {
      id: 'q1',
      text: 'Apakah sekolah telah menetapkan kebijakan lingkungan yang jelas dan tertulis?',
    },
    {
      id: 'q2',
      text: 'Apakah ada tim khusus yang bertanggung jawab untuk program Greenedu?',
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
      text: 'Apakah ada alokasi anggaran untuk mendukung program Greenedu?',
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
      text: 'Apakah sekolah melakukan evaluasi berkala terhadap pencapaian program Greenedu?',
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
    },
    {
      id: 'q6',
      text: 'Apakah ada mekanisme umpan balik dari stakeholder tentang hasil evaluasi di 1 tahun ini?',
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
      text: 'Apakah sekolah melakukan upaya berkelanjutan untuk meningkatkan program Greenedu?',
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
  { value: 1, label: 'Sangat Kurang', color: 'bg-red-500', textColor: 'text-red-600', bgColor: 'bg-red-50', circleBg: 'bg-red-300' },
  { value: 2, label: 'Kurang', color: 'bg-orange-500', textColor: 'text-orange-600', bgColor: 'bg-orange-50', circleBg: 'bg-orange-300' },
  { value: 3, label: 'Cukup', color: 'bg-gray-400', textColor: 'text-gray-600', bgColor: 'bg-gray-50', circleBg: 'bg-gray-300' },
  { value: 4, label: 'Baik', color: 'bg-yellow-500', textColor: 'text-yellow-600', bgColor: 'bg-yellow-50', circleBg: 'bg-yellow-300' },
  { value: 5, label: 'Sangat Baik', color: 'bg-green-500', textColor: 'text-green-600', bgColor: 'bg-green-50', circleBg: 'bg-green-300' }
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
    <div>
      <Header
        title="Form Evaluasi Greenedu"
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
              'flex-1 px-6 py-4 text-sm font-semibold transition-all duration-300 whitespace-nowrap',
              activeTab === tab.id
                ? 'bg-green-600 text-white shadow-lg'
                : 'bg-white text-gray-600 hover:bg-gray-50'
            ]"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Progress Bar di bawah tabs -->
        <div class="bg-white px-6 py-3 border-t border-gray-200">
          <div class="flex items-center justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-gray-600">Progress</span>
                <span class="text-xs font-bold text-green-600">{{ calculateTabProgress(activeTab) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-green-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: calculateTabProgress(activeTab) + '%' }"
                ></div>
              </div>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ tabs.find(t => t.id === activeTab).label }}</h2>

            <!-- Rating Scale Legend -->
            <div class="flex justify-end gap-2 text-xs">
              <div v-for="scale in ratingScale" :key="scale.value" class="flex items-center gap-1">
                <div :class="['w-3 h-3 rounded-full', scale.color]"></div>
                <span class="text-gray-600">{{ scale.label }}</span>
              </div>
            </div>
          </div>

          <!-- Questions List -->
          <div class="space-y-6">
            <div
              v-for="(question, index) in questions[activeTab]"
              :key="question.id"
              class="border-b border-gray-100 pb-6 last:border-b-0"
            >
              <p class="text-gray-700 font-medium mb-4">{{ index + 1 }}. {{ question.text }}</p>

              <!-- Rating Buttons -->
              <div class="flex gap-3 justify-end">
                <button
                  v-for="scale in ratingScale"
                  :key="scale.value"
                  @click="formResponses[activeTab][question.id] = scale.value"
                  :class="[
                    'w-10 h-10 rounded-full border-2 transition-all duration-200',
                    formResponses[activeTab][question.id] === scale.value
                      ? `${scale.color} border-transparent shadow-lg scale-110`
                      : `bg-white border-gray-300 hover:border-gray-400 hover:scale-105`
                  ]"
                  :title="scale.label"
                >
                  <span class="sr-only">{{ scale.label }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Summary Section -->
          <div class="mt-8 pt-8 border-t-2 border-green-200 bg-green-50 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-bold text-gray-800">Skor {{ tabs.find(t => t.id === activeTab).label }}</h3>
                <p class="text-sm text-gray-600">
                  {{ Object.values(formResponses[activeTab]).filter(v => v !== null).length }} dari
                  {{ Object.keys(formResponses[activeTab]).length }} pertanyaan dijawab
                </p>
              </div>
              <div class="text-right">
                <div class="text-4xl font-bold text-green-600">
                  {{ calculateTabScore(activeTab) }}
                </div>
                <div class="text-sm text-gray-600">/ 5.00</div>
              </div>
            </div>

            <!-- Progress bar untuk tab saat ini -->
            <div class="mt-4">
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-green-600 h-3 rounded-full transition-all duration-300"
                  :style="{ width: calculateTabProgress(activeTab) + '%' }"
                ></div>
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
          <div v-if="submitMessage" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
            <div class="flex items-center gap-3">
              <CheckCircle class="w-5 h-5 text-green-600" />
              <p class="text-green-700 font-medium">{{ submitMessage }}</p>
            </div>
          </div>
        </Transition>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
          <button
            @click="handleReset"
            :disabled="isSubmitting"
            class="w-full sm:w-auto px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Reset Semua Jawaban
          </button>

          <button
            @click="handleSave"
            :disabled="isSubmitting"
            class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-lg hover:shadow-xl"
          >
            <Save class="w-5 h-5" v-if="!isSubmitting" />
            <Loader class="w-5 h-5 animate-spin" v-else />
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kuisioner' }}
          </button>
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
