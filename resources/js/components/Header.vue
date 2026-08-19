<template>
  <header :class="['w-full bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs', isEmbedded ? 'h-13 px-3' : 'h-16 px-4']">
    <div :class="['max-w-4xl mx-auto flex items-center justify-between h-full']">
      <!-- Logo & Brand -->
      <div class="flex items-center gap-2.5 cursor-pointer select-none" @click="$emit('reset')">
        <div :class="['rounded-xl bg-gradient-to-br from-[#00ADEF] to-[#0077A6] flex items-center justify-center shadow-2xs text-white shrink-0 overflow-hidden', isEmbedded ? 'w-8 h-8' : 'w-10 h-10']">
          <svg width="20" height="20" :class="isEmbedded ? 'w-4 h-4' : 'w-5 h-5'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="flex flex-col">
          <div class="flex items-center gap-1.5">
            <h1 :class="['font-bold text-slate-900 leading-tight', isEmbedded ? 'text-sm' : 'text-lg']">BPS AI Assistant</h1>
            <span class="text-[10px] font-semibold uppercase tracking-wider bg-blue-50 text-[#0077A6] px-1.5 py-0.2 rounded-full border border-blue-200">Demo</span>
          </div>
          <span :class="['text-slate-500', isEmbedded ? 'text-[10px]' : 'text-xs']">Asisten Statistik Publik Resmi</span>
        </div>
      </div>

      <!-- Action Nav -->
      <div class="flex items-center gap-1.5 sm:gap-2">
        <button 
          v-if="hasMessages"
          @click="$emit('reset')"
          class="text-xs font-medium text-slate-600 hover:text-[#0077A6] flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-slate-100 transition-colors"
          title="Mulai percakapan baru"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span class="text-[11px]">Chat Baru</span>
        </button>

        <button 
          @click="showAboutModal = true"
          class="text-[11px] font-medium text-slate-600 hover:text-[#0077A6] px-2 py-1 rounded-lg hover:bg-slate-100 transition-colors"
        >
          Tentang
        </button>

        <button 
          v-if="!isEmbedded"
          @click="showHelpModal = true"
          class="text-xs font-medium text-slate-600 hover:text-[#0077A6] px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors"
        >
          Bantuan
        </button>
      </div>
    </div>

    <!-- About Modal -->
    <div v-if="showAboutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full p-5 shadow-xl border border-slate-200 animate-in fade-in zoom-in duration-150">
        <div class="flex justify-between items-start mb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#0077A6] flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">BPS AI Assistant</h3>
          </div>
          <button @click="showAboutModal = false" class="text-slate-400 hover:text-slate-600 p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-xs text-slate-600 leading-relaxed mb-3">
          Asisten resmi berbasis AI untuk menelusuri data statistik, definisi indikator, publikasi, metodologi, dan layanan Badan Pusat Statistik di seluruh Indonesia.
        </p>
        <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-2.5 text-[11px] text-slate-700 space-y-1 mb-4">
          <div class="font-semibold text-[#0077A6]">Keunggulan:</div>
          <div>• Live data dari <strong>BPS WebAPI</strong> (549 Wilayah).</div>
          <div>• Rujukan terverifikasi ke portal resmi & SIRuSa.</div>
        </div>
        <div class="flex justify-end">
          <button @click="showAboutModal = false" class="bg-[#0077A6] hover:bg-[#005F85] text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Help Modal -->
    <div v-if="showHelpModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-lg w-full p-5 shadow-xl border border-slate-200 animate-in fade-in zoom-in duration-150">
        <div class="flex justify-between items-start mb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#F7941D] flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Panduan Pertanyaan</h3>
          </div>
          <button @click="showHelpModal = false" class="text-slate-400 hover:text-slate-600 p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-xs text-slate-600 mb-3">Contoh pertanyaan statistik:</p>
        <div class="space-y-1.5 mb-4 text-[11px]">
          <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
            <div class="font-semibold text-slate-900">1. Konsep Statistik:</div>
            "Apa itu inflasi?", "Jelaskan konsep PDRB", "Apa itu TPT?"
          </div>
          <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
            <div class="font-semibold text-slate-900">2. Data Daerah & Publikasi:</div>
            "Data penduduk Sulawesi Tengah 2026", "Tampilkan publikasi terbaru"
          </div>
        </div>
        <div class="flex justify-end">
          <button @click="showHelpModal = false" class="bg-[#0077A6] hover:bg-[#005F85] text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
            Mengerti
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  hasMessages: {
    type: Boolean,
    default: false
  },
  isEmbedded: {
    type: Boolean,
    default: false
  }
});

defineEmits(['reset']);

const showAboutModal = ref(false);
const showHelpModal = ref(false);
</script>
