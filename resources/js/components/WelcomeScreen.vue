<template>
  <div class="flex flex-col items-center justify-center max-w-xl mx-auto w-full text-center my-auto py-2 sm:py-4">
    <!-- AI Avatar -->
    <div :class="['rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100/80 flex items-center justify-center shadow-2xs border border-blue-200/60 text-[#0077A6] shrink-0 overflow-hidden', isEmbedded ? 'w-10 h-10 mb-2' : 'w-12 h-12 sm:w-16 sm:h-16 mb-3 sm:mb-4']">
      <svg :class="isEmbedded ? 'w-5 h-5 text-[#0077A6]' : 'w-6 h-6 sm:w-8 sm:h-8 text-[#0077A6]'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <rect x="3" y="11" width="18" height="10" rx="2" stroke-linecap="round"/>
        <circle cx="12" cy="5" r="2"/>
        <path d="M12 7v4" stroke-linecap="round"/>
        <line x1="8" y1="16" x2="8.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
        <line x1="16" y1="16" x2="16.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
    </div>

    <!-- Hero Title & Subtitle -->
    <h2 :class="['font-extrabold text-slate-900 tracking-tight leading-snug', isEmbedded ? 'text-sm sm:text-base mb-0.5' : 'text-lg sm:text-2xl mb-1']">
      Halo, ada yang bisa saya bantu?
    </h2>
    <p :class="['text-slate-500 max-w-sm', isEmbedded ? 'text-[11px] mb-3' : 'text-xs sm:text-sm mb-4 sm:mb-6']">
      Tanyakan data statistik, publikasi resmi, metodologi, atau layanan BPS se-Indonesia.
    </p>

    <!-- Suggested Questions Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-2.5 w-full text-left">
      <button 
        v-for="item in (isEmbedded ? suggestedQuestions.slice(0, 3) : suggestedQuestions)" 
        :key="item.title"
        @click="$emit('select', item.query)"
        class="bg-white border border-slate-200/90 rounded-xl p-2.5 sm:p-3 hover:border-[#00ADEF] hover:bg-blue-50/40 hover:shadow-2xs active:scale-[0.99] transition-all duration-150 group text-left flex items-center justify-between cursor-pointer"
      >
        <div class="w-full min-w-0 pr-1">
          <p class="text-xs sm:text-[13px] font-semibold text-slate-900 group-hover:text-[#0077A6] transition-colors flex items-center justify-between">
            <span class="truncate">{{ item.title }}</span>
            <svg class="w-3 h-3 text-slate-300 group-hover:text-[#00ADEF] transition-colors shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </p>
          <p class="text-slate-500 text-[10.5px] sm:text-[11.5px] mt-0.5 line-clamp-1">{{ item.desc }}</p>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isEmbedded: {
    type: Boolean,
    default: false
  }
});

defineEmits(['select']);

const suggestedQuestions = [
  {
    title: 'Apa itu inflasi?',
    desc: 'Konsep dan penghitungan inflasi IHK resmi BPS.',
    query: 'Apa itu inflasi dan bagaimana BPS menghitungnya?'
  },
  {
    title: 'Apa itu PDRB?',
    desc: 'Definisi, pendekatan berlaku vs konstan.',
    query: 'Apa itu PDRB dan apa perbedaan harga berlaku vs harga konstan?'
  },
  {
    title: 'Data Penduduk Sulteng 2025',
    desc: 'Statistik kependudukan resmi BPS daerah.',
    query: 'DATA PENDUDUK SULAWESI TENGAH TAHUN 2025'
  },
  {
    title: 'Cari Publikasi Statistik',
    desc: 'Panduan akses direktori buku publikasi resmi.',
    query: 'Bagaimana cara mencari dan mengunduh publikasi statistik di website BPS?'
  }
];
</script>
