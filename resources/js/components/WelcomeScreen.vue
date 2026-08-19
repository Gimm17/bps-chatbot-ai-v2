<template>
  <div :class="['flex flex-col items-center justify-center max-w-2xl mx-auto w-full text-center', isEmbedded ? 'py-2 px-1' : 'py-6 md:py-10 px-4']">
    <!-- AI Avatar -->
    <div :class="['bg-blue-50/80 rounded-2xl flex items-center justify-center shadow-2xs border border-blue-100 text-[#0077A6] shrink-0 overflow-hidden', isEmbedded ? 'w-10 h-10 mb-2' : 'w-16 h-16 md:w-20 md:h-20 mb-4']">
      <svg :class="isEmbedded ? 'w-5 h-5 text-[#0077A6]' : 'w-8 h-8 md:w-10 md:h-10 text-[#0077A6]'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <rect x="3" y="11" width="18" height="10" rx="2" stroke-linecap="round"/>
        <circle cx="12" cy="5" r="2"/>
        <path d="M12 7v4" stroke-linecap="round"/>
        <line x1="8" y1="16" x2="8.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
        <line x1="16" y1="16" x2="16.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
    </div>

    <!-- Hero Title & Subtitle -->
    <h2 :class="['font-extrabold text-slate-900 tracking-tight leading-snug', isEmbedded ? 'text-base mb-1' : 'text-2xl md:text-3xl mb-2']">
      Halo, ada yang bisa saya bantu?
    </h2>
    <p :class="['text-slate-500 max-w-md', isEmbedded ? 'text-xs mb-3' : 'text-sm md:text-base mb-6']">
      Tanyakan data, istilah statistik, publikasi, atau layanan BPS.
    </p>

    <!-- Suggested Questions Grid -->
    <div :class="['grid w-full text-left', isEmbedded ? 'grid-cols-1 gap-2' : 'grid-cols-1 sm:grid-cols-2 gap-3']">
      <button 
        v-for="item in (isEmbedded ? suggestedQuestions.slice(0, 3) : suggestedQuestions)" 
        :key="item.title"
        @click="$emit('select', item.query)"
        :class="['bg-white border border-slate-200 rounded-xl hover:border-[#00ADEF] hover:bg-blue-50/40 hover:shadow-2xs transition-all duration-200 group text-left flex items-center justify-between cursor-pointer', isEmbedded ? 'p-2.5' : 'p-3.5 flex-col justify-between']"
      >
        <div class="w-full">
          <p :class="['font-semibold text-slate-900 group-hover:text-[#0077A6] transition-colors flex items-center justify-between', isEmbedded ? 'text-xs' : 'text-sm']">
            <span>{{ item.title }}</span>
            <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#00ADEF] transition-colors shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </p>
          <p :class="['text-slate-500 line-clamp-1', isEmbedded ? 'text-[11px] mt-0.5' : 'text-xs mt-1']">{{ item.desc }}</p>
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
    desc: 'Penjelasan ringkas konsep dan penghitungan inflasi BPS.',
    query: 'Apa itu inflasi dan bagaimana BPS menghitungnya?'
  },
  {
    title: 'Apa itu PDRB?',
    desc: 'Definisi, pendekatan, dan komponen PDRB daerah.',
    query: 'Apa itu PDRB dan apa perbedaan harga berlaku vs harga konstan?'
  },
  {
    title: 'Data Penduduk Sulteng 2025',
    desc: 'Statistik kependudukan dan proyeksi resmi BPS daerah.',
    query: 'DATA PENDUDUK SULAWESI TENGAH TAHUN 2025'
  },
  {
    title: 'Bagaimana mencari publikasi BPS?',
    desc: 'Panduan akses direktori buku publikasi resmi.',
    query: 'Bagaimana cara mencari dan mengunduh publikasi statistik di website BPS?'
  }
];
</script>
