<template>
  <div class="flex flex-col items-center justify-center w-full min-h-[70vh] py-6 sm:py-10 px-2 sm:px-4 relative animate-fade-in-up">
    <!-- Ambient Background Gradient Glow -->
    <div class="absolute inset-0 pointer-events-none opacity-40 z-0 flex justify-center items-center">
      <div class="w-[500px] h-[500px] sm:w-[700px] sm:h-[700px] bg-gradient-to-tr from-[#D5E3FF]/40 via-[#FAF8FF] to-[#FFDBC8]/40 blur-3xl rounded-full mix-blend-multiply"></div>
    </div>

    <div class="w-full max-w-2xl mx-auto flex flex-col items-center justify-center space-y-6 sm:space-y-8 z-10">
      <!-- Hero AI Robot Avatar -->
      <div class="flex flex-col items-center text-center space-y-3">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#EAEDFF] rounded-3xl shadow-xs flex items-center justify-center border border-[#DAE2FD] text-[#0058A9] transition-transform hover:scale-105">
          <span class="material-symbols-outlined text-3xl sm:text-4xl" style="font-variation-settings: 'FILL' 1;">smart_toy</span>
        </div>
        <h2 class="text-2xl sm:text-3.5xl font-bold text-[#131B2E] tracking-tight leading-tight">
          Halo, ada yang bisa saya bantu?
        </h2>
        <p class="text-xs sm:text-sm text-[#424751] max-w-lg leading-relaxed">
          Tanyakan data, istilah statistik, publikasi resmi, metodologi, atau informasi kelembagaan Badan Pusat Statistik.
        </p>
      </div>

      <!-- 2x2 Suggestion Cards Grid -->
      <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
        <button 
          v-for="(card, index) in suggestionCards"
          :key="index"
          @click="$emit('select', card.prompt)"
          class="flex flex-col items-start p-3.5 sm:p-4 bg-white/95 backdrop-blur-xs rounded-xl border border-[#DAE2FD] shadow-[0_2px_8px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-[#0058A9]/40 hover:bg-[#FAF8FF] transition-all group text-left cursor-pointer active:scale-[0.99]"
        >
          <div class="flex items-center gap-2 mb-1 text-[#0058A9]">
            <span class="material-symbols-outlined text-[20px] text-[#0058A9] group-hover:scale-110 transition-transform">{{ card.icon }}</span>
            <h3 class="text-xs sm:text-[13.5px] font-bold text-[#131B2E] group-hover:text-[#0058A9] transition-colors leading-tight">
              {{ card.title }}
            </h3>
          </div>
          <p class="text-[11px] sm:text-xs text-[#424751] leading-relaxed">
            {{ card.desc }}
          </p>
        </button>
      </div>
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

const suggestionCards = [
  {
    icon: 'trending_up',
    title: 'Apa itu inflasi?',
    desc: 'Penjelasan ringkas konsep dan penghitungan inflasi.',
    prompt: 'Apa itu inflasi dan bagaimana cara menghitungnya?'
  },
  {
    icon: 'account_balance',
    title: 'Data PDRB Sulawesi Tengah?',
    desc: 'Pertumbuhan ekonomi dan PDRB Sulteng terbaru.',
    prompt: 'Berapa pertumbuhan ekonomi dan PDRB Sulawesi Tengah 2024?'
  },
  {
    icon: 'menu_book',
    title: 'Mencari publikasi BPS?',
    desc: 'Akses buku dan dokumen publikasi resmi daerah.',
    prompt: 'Bagaimana cara mencari publikasi statistik daerah di website BPS?'
  },
  {
    icon: 'groups',
    title: 'Data kependudukan & kemiskinan?',
    desc: 'Angka kemiskinan dan kependudukan resmi.',
    prompt: 'Berapa persentase kemiskinan di Sulawesi Tengah terbaru?'
  }
];
</script>
