<template>
  <div :class="['flex flex-col items-center justify-center max-w-xl mx-auto w-full text-center my-auto', isEmbedded ? 'py-2 px-1' : 'py-3 sm:py-6']">
    <!-- AI Bot Avatar -->
    <div :class="['rounded-2xl bg-gradient-to-br from-[#0093DD] to-[#007bbd] flex items-center justify-center shadow-xs border border-[#0093DD]/20 text-white shrink-0 overflow-hidden p-2', isEmbedded ? 'w-11 h-11 mb-2' : 'w-12 h-12 sm:w-16 sm:h-16 mb-2 sm:mb-4']">
      <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
        <path d="M9.5 23.5H22C25.5899 23.5 28.5 20.5899 28.5 17C28.5 13.5683 25.8366 10.7573 22.4668 10.521C21.7248 6.78652 18.4414 4 14.5 4C10.0526 4 6.38676 7.23432 5.67978 11.5173C3.0189 12.3789 1 14.9455 1 18C1 21.3137 3.68629 24 7 24L5 28L10.5 24.5L9.5 23.5Z" 
              fill="white" fill-opacity="0.25" stroke="white" stroke-width="1.75" stroke-linejoin="round" />
        <rect x="9.5" y="14.5" width="2.2" height="5.5" rx="1.1" fill="#EB891B" />
        <rect x="13.5" y="11.5" width="2.2" height="8.5" rx="1.1" fill="#FFFFFF" />
        <rect x="17.5" y="13" width="2.2" height="7" rx="1.1" fill="#68B92E" />
        <path d="M24 5L24.8 7.2L27 8L24.8 8.8L24 11L23.2 8.8L21 8L23.2 7.2L24 5Z" fill="#FDE047" />
      </svg>
    </div>

    <!-- Hero Title & Subtitle -->
    <h2 :class="['font-extrabold text-[#1F2937] tracking-tight leading-snug mb-1', isEmbedded ? 'text-base sm:text-lg' : 'text-base sm:text-3xl sm:mb-2']">
      Halo, ada yang bisa saya bantu?
    </h2>
    <p :class="['text-[#64748B] max-w-xs leading-relaxed', isEmbedded ? 'text-xs sm:text-[13px] mb-3' : 'text-[11.5px] sm:text-[15px] sm:max-w-md mb-3.5 sm:mb-6']">
      Tanyakan data statistik, publikasi resmi, metodologi, atau layanan BPS se-Indonesia.
    </p>

    <!-- Suggested Questions Grid (4 Cards) -->
    <div :class="['grid gap-2 w-full text-left', isEmbedded ? 'grid-cols-1 gap-2' : 'grid-cols-1 sm:grid-cols-2 sm:gap-3']">
      <button 
        v-for="item in (isEmbedded ? suggestedQuestions.slice(0, 3) : suggestedQuestions)" 
        :key="item.title"
        @click="$emit('select', item.query)"
        :class="['bg-[#FFFFFF] border border-[#E2E8F0] rounded-xl hover:border-[#0093DD] hover:bg-[#EBF7FD]/40 hover:shadow-xs active:scale-[0.99] transition-all duration-150 group text-left flex items-center justify-between cursor-pointer', isEmbedded ? 'p-2.5 sm:p-3' : 'p-2.5 sm:p-3.5']"
      >
        <div class="w-full min-w-0 pr-1">
          <p :class="['font-semibold text-[#1F2937] group-hover:text-[#0093DD] transition-colors flex items-center justify-between', isEmbedded ? 'text-[13px] sm:text-[14px]' : 'text-xs sm:text-[14.5px]']">
            <span class="truncate">{{ item.title }}</span>
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-300 group-hover:text-[#0093DD] transition-colors shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </p>
          <p :class="['text-[#64748B] mt-0.5 line-clamp-1', isEmbedded ? 'text-[11px] sm:text-[12.5px]' : 'text-[10.5px] sm:text-[12.5px] sm:mt-1']">{{ item.desc }}</p>
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
