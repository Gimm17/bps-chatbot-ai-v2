<template>
  <div class="w-full">
    <!-- 1. Clarification Required State (Sliced from REDESIGN_UI_UX/klarifikasi_pertanyaan) -->
    <div v-if="status === 'clarification_required'" class="flex flex-col gap-2.5 w-full">
      <div class="bg-[#D5E3FF] text-[#001B3C] border border-[#A8C8FF] rounded-t-2xl rounded-r-2xl rounded-bl-xs px-4 sm:px-5 py-3 sm:py-3.5 shadow-2xs relative">
        <p class="text-xs sm:text-[14px] leading-relaxed font-medium">
          {{ clarificationQuestion || 'Saya memerlukan informasi tambahan terkait wilayah atau periode tahun untuk menyajikan data statistik yang akurat.' }}
        </p>
      </div>

      <!-- Suggestion Chips -->
      <div class="flex flex-wrap gap-1.5 mt-0.5">
        <button 
          v-for="(chip, index) in dynamicChips" 
          :key="index"
          @click="$emit('select-chip', chip.query)"
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-[#EAEDFF] text-[#0058A9] border border-[#DAE2FD] hover:border-[#0058A9]/50 rounded-full text-xs font-semibold shadow-2xs transition-all cursor-pointer active:scale-95"
        >
          <span class="material-symbols-outlined text-[15px] text-[#0058A9]">{{ chip.icon }}</span>
          <span>{{ chip.label }}</span>
        </button>
      </div>
    </div>

    <!-- 2. No Evidence / Out of Scope / Error State (Sliced from REDESIGN_UI_UX/layanan_tidak_tersedia) -->
    <div v-else class="bg-white border border-[#DAE2FD] rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col items-start gap-3 text-left">
      <div class="flex items-start gap-3 w-full">
        <!-- Icon Avatar -->
        <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs', 
          status === 'provider_error' ? 'bg-[#FFDAD6] text-[#BA1A1A]' : 'bg-[#FFDBC8] text-[#984700]']"
        >
          <span class="material-symbols-outlined text-[22px]">
            {{ status === 'provider_error' ? 'cloud_off' : (status === 'out_of_scope' ? 'help_outline' : 'search_off') }}
          </span>
        </div>

        <div class="flex-1 min-w-0">
          <h4 class="text-xs sm:text-sm font-bold text-[#131B2E]">
            {{ stateTitle }}
          </h4>
          <p class="text-xs sm:text-[13px] text-[#424751] leading-relaxed mt-1">
            {{ answer || defaultStateDesc }}
          </p>
        </div>
      </div>

      <!-- Action & Portal Link -->
      <div class="flex flex-wrap items-center justify-between gap-2 w-full pt-2 border-t border-[#E2E7FF]/80 mt-1">
        <button 
          v-if="status === 'provider_error'"
          @click="$emit('retry')"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#FE852B] hover:bg-[#984700] text-white rounded-lg text-xs font-semibold transition-colors shadow-2xs cursor-pointer"
        >
          <span class="material-symbols-outlined text-[16px]">refresh</span>
          <span>Coba lagi</span>
        </button>

        <a 
          href="https://www.bps.go.id" 
          target="_blank" 
          rel="noopener noreferrer" 
          class="inline-flex items-center gap-1 text-xs font-semibold text-[#0058A9] hover:underline ml-auto"
        >
          <span>Kunjungi Portal BPS.go.id</span>
          <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true
  },
  answer: {
    type: String,
    default: ''
  },
  clarificationQuestion: {
    type: String,
    default: ''
  }
});

defineEmits(['select-chip', 'retry']);

const dynamicChips = computed(() => {
  const q = (props.clarificationQuestion || '').toLowerCase();
  
  if (q.includes('wilayah') || q.includes('daerah')) {
    return [
      { icon: 'language', label: 'Seluruh Indonesia', query: 'Seluruh Indonesia' },
      { icon: 'map', label: 'Provinsi Sulawesi Tengah', query: 'Provinsi Sulawesi Tengah' },
      { icon: 'location_city', label: 'Kota Palu', query: 'Kota Palu' },
      { icon: 'calendar_month', label: 'Tahun 2024', query: 'Tahun 2024' },
      { icon: 'calendar_month', label: 'Tahun 2025', query: 'Tahun 2025' }
    ];
  }

  return [
    { icon: 'calendar_month', label: 'Tahun 2025', query: 'Tahun 2025' },
    { icon: 'calendar_month', label: 'Tahun 2024', query: 'Tahun 2024' },
    { icon: 'map', label: 'Provinsi Sulawesi Tengah', query: 'Provinsi Sulawesi Tengah' },
    { icon: 'language', label: 'Nasional', query: 'Nasional' }
  ];
});

const stateTitle = computed(() => {
  switch (props.status) {
    case 'provider_error':
      return 'Layanan AI Sedang Mengalami Kendala';
    case 'out_of_scope':
      return 'Topik di Luar Cakupan BPS';
    case 'no_evidence':
      return 'Informasi Belum Ditemukan di Portal BPS';
    default:
      return 'Status Respons';
  }
});

const defaultStateDesc = computed(() => {
  switch (props.status) {
    case 'provider_error':
      return 'Silakan coba beberapa saat lagi atau akses portal resmi BPS.';
    case 'out_of_scope':
      return 'BPS AI Assistant difokuskan untuk data statistik, publikasi, metodologi, dan kelembagaan BPS.';
    case 'no_evidence':
      return 'Sumber data atau publikasi resmi BPS untuk topik ini belum ditemukan.';
    default:
      return '';
  }
});
</script>
