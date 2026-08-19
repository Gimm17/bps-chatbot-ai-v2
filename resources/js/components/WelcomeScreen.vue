<template>
  <div class="flex flex-col items-center justify-center py-6 md:py-10 max-w-2xl mx-auto w-full px-4 text-center">
    <!-- AI Avatar -->
    <div class="w-16 h-16 md:w-20 md:h-20 bg-blue-50/80 rounded-2xl flex items-center justify-center mb-5 shadow-sm border border-blue-100 text-[#0077A6]">
      <svg class="w-8 h-8 md:w-10 md:h-10 text-[#0077A6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
        <rect x="3" y="11" width="18" height="10" rx="2" stroke-linecap="round"/>
        <circle cx="12" cy="5" r="2"/>
        <path d="M12 7v4" stroke-linecap="round"/>
        <line x1="8" y1="16" x2="8.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
        <line x1="16" y1="16" x2="16.01" y2="16" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
    </div>

    <!-- Hero Title & Subtitle -->
    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-2 tracking-tight">
      Halo, ada yang bisa saya bantu?
    </h2>
    <p class="text-sm md:text-base text-slate-600 mb-8 max-w-lg">
      Tanyakan data, istilah statistik, publikasi, metodologi, atau informasi layanan BPS.
    </p>

    <!-- Suggested Questions 2x2 Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full text-left">
      <button 
        v-for="item in suggestedQuestions" 
        :key="item.title"
        @click="$emit('select', item.query)"
        class="bg-white border border-slate-200 rounded-xl p-4 hover:border-[#00ADEF] hover:bg-blue-50/40 hover:shadow-xs transition-all duration-200 group text-left flex flex-col justify-between"
      >
        <div>
          <p class="text-sm font-semibold text-slate-900 group-hover:text-[#0077A6] transition-colors flex items-center justify-between">
            <span>{{ item.title }}</span>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-[#00ADEF] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </p>
          <p class="text-xs text-slate-500 mt-1 leading-normal">{{ item.desc }}</p>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
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
    title: 'Bagaimana mencari publikasi BPS?',
    desc: 'Panduan akses direktori buku publikasi resmi.',
    query: 'Bagaimana cara mencari dan mengunduh publikasi statistik di website BPS?'
  },
  {
    title: 'Di mana menemukan data penduduk?',
    desc: 'Arahkan ke hasil Sensus Penduduk dan Susenas terbaru.',
    query: 'Di mana saya bisa menemukan data sensus penduduk terbaru?'
  }
];
</script>
