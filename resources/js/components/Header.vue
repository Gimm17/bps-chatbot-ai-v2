<template>
  <header class="w-full bg-white/95 backdrop-blur-md border-b border-[#E2E8F0] sticky top-0 z-30 shadow-2xs h-13 sm:h-15 px-3 sm:px-4">
    <div class="max-w-3xl mx-auto flex items-center justify-between h-full w-full gap-2">
      <!-- Logo & Brand Title -->
      <div class="flex items-center gap-2 sm:gap-2.5 cursor-pointer select-none shrink-0" @click="$emit('reset')">
        <div class="w-7.5 h-7.5 sm:w-8.5 sm:h-8.5 rounded-xl bg-gradient-to-br from-[#0093DD] to-[#007bbd] flex items-center justify-center shadow-2xs text-white shrink-0 overflow-hidden p-1">
          <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <path d="M9.5 23.5H22C25.5899 23.5 28.5 20.5899 28.5 17C28.5 13.5683 25.8366 10.7573 22.4668 10.521C21.7248 6.78652 18.4414 4 14.5 4C10.0526 4 6.38676 7.23432 5.67978 11.5173C3.0189 12.3789 1 14.9455 1 18C1 21.3137 3.68629 24 7 24L5 28L10.5 24.5L9.5 23.5Z" 
                  fill="white" fill-opacity="0.25" stroke="white" stroke-width="1.75" stroke-linejoin="round" />
            <rect x="9.5" y="14.5" width="2.2" height="5.5" rx="1.1" fill="#EB891B" />
            <rect x="13.5" y="11.5" width="2.2" height="8.5" rx="1.1" fill="#FFFFFF" />
            <rect x="17.5" y="13" width="2.2" height="7" rx="1.1" fill="#68B92E" />
            <path d="M24 5L24.8 7.2L27 8L24.8 8.8L24 11L23.2 8.8L21 8L23.2 7.2L24 5Z" fill="#FDE047" />
          </svg>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
          <h1 class="text-[13.5px] sm:text-base font-bold text-[#1F2937] tracking-tight whitespace-nowrap leading-none">BPS AI Assistant</h1>
          <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-[#FEF6EB] text-[#EB891B] px-1.5 py-0.5 rounded border border-[#EB891B]/30 shrink-0 hidden sm:inline-block">Demo</span>
        </div>
      </div>

      <!-- Action Nav Buttons: Desktop Mode (sm and up) -->
      <div class="hidden sm:flex items-center gap-1.5 shrink-0">
        <!-- Install App Button (PWA) -->
        <button 
          v-if="isInstallable && !isEmbedded"
          @click="installApp"
          class="text-xs font-semibold bg-[#EBF7FD] hover:bg-[#d8f0fb] text-[#0093DD] border border-[#0093DD]/30 flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0"
          title="Install Aplikasi BPS AI ke Perangkat"
        >
          <svg class="w-3.5 h-3.5 text-[#0093DD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          <span class="text-xs font-semibold">Install App</span>
        </button>

        <!-- New Chat Button -->
        <button 
          v-if="hasMessages"
          @click="$emit('reset')"
          class="text-xs font-semibold text-[#1F2937] hover:text-[#0093DD] flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer shrink-0"
          title="Mulai percakapan baru"
        >
          <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span class="text-xs">Chat Baru</span>
        </button>

        <!-- About Button -->
        <button 
          @click="showAboutModal = true"
          class="text-xs font-medium text-[#64748B] hover:text-[#0093DD] px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer shrink-0"
        >
          Tentang
        </button>

        <!-- Help Button -->
        <button 
          @click="showHelpModal = true"
          class="text-xs font-medium text-[#64748B] hover:text-[#0093DD] px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer shrink-0"
        >
          Bantuan
        </button>
      </div>

      <!-- Action Nav Buttons: Mobile Mode (under sm) -->
      <div class="flex sm:hidden items-center gap-1 shrink-0 relative">
        <!-- Mobile Install App Icon -->
        <button 
          v-if="isInstallable && !isEmbedded"
          @click="installApp"
          class="w-8 h-8 rounded-lg bg-[#EBF7FD] text-[#0093DD] border border-[#0093DD]/30 flex items-center justify-center hover:bg-[#d8f0fb] transition-colors cursor-pointer shrink-0"
          title="Install Aplikasi BPS AI"
        >
          <svg class="w-4 h-4 text-[#0093DD]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
        </button>

        <!-- Mobile New Chat Icon -->
        <button 
          v-if="hasMessages"
          @click="$emit('reset')"
          class="w-8 h-8 rounded-lg bg-slate-100 text-[#1F2937] hover:text-[#0093DD] flex items-center justify-center transition-colors cursor-pointer shrink-0"
          title="Mulai percakapan baru"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
        </button>

        <!-- Mobile 3-Dots Menu Trigger -->
        <button 
          @click="showMobileMenu = !showMobileMenu"
          class="w-8 h-8 rounded-lg text-[#64748B] hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0"
          title="Menu"
        >
          <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
          </svg>
        </button>

        <!-- Mobile Dropdown Menu -->
        <div 
          v-if="showMobileMenu" 
          class="absolute right-0 top-10 w-44 bg-white rounded-xl shadow-xl border border-[#E2E8F0] py-1.5 z-50 animate-in fade-in zoom-in duration-100"
          @click="showMobileMenu = false"
        >
          <button 
            v-if="isInstallable && !isEmbedded"
            @click="installApp"
            class="w-full text-left px-3.5 py-2 text-xs font-semibold text-[#0093DD] hover:bg-[#EBF7FD] flex items-center gap-2"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Install Aplikasi
          </button>
          <button 
            @click="showAboutModal = true"
            class="w-full text-left px-3.5 py-2 text-xs text-[#1F2937] hover:bg-slate-50 flex items-center gap-2"
          >
            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Tentang BPS AI
          </button>
          <button 
            @click="showHelpModal = true"
            class="w-full text-left px-3.5 py-2 text-xs text-[#1F2937] hover:bg-slate-50 flex items-center gap-2"
          >
            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Panduan & Bantuan
          </button>
        </div>
      </div>
    </div>

    <!-- About Modal -->
    <div v-if="showAboutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full p-5 shadow-2xl border border-[#E2E8F0] animate-in fade-in zoom-in duration-150">
        <div class="flex justify-between items-start mb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#EBF7FD] text-[#0093DD] flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-[#1F2937]">Tentang BPS AI Assistant</h3>
          </div>
          <button @click="showAboutModal = false" class="text-[#64748B] hover:text-[#1F2937] p-1 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-xs text-[#4B5563] leading-relaxed mb-3">
          Asisten resmi berbasis AI untuk menelusuri data statistik resmi, definisi indikator, publikasi, metodologi, dan layanan Badan Pusat Statistik di seluruh Indonesia.
        </p>
        <div class="bg-[#EBF7FD]/70 border border-[#0093DD]/20 rounded-xl p-2.5 text-[11px] text-[#1F2937] space-y-1 mb-4">
          <div class="font-semibold text-[#0093DD]">Fitur & Keunggulan:</div>
          <div>• Data langsung dari <strong>BPS WebAPI</strong> (549 Wilayah).</div>
          <div>• Terintegrasi dengan <strong>Portal PPID BPS</strong> & SIRuSa.</div>
          <div>• Mendukung <strong>PWA (Progressive Web App)</strong> untuk instalasi langsung.</div>
        </div>
        <div class="flex justify-end gap-2">
          <button 
            v-if="isInstallable && !isEmbedded"
            @click="installApp(); showAboutModal = false;" 
            class="bg-[#EBF7FD] hover:bg-[#d8f0fb] text-[#0093DD] border border-[#0093DD]/30 text-xs font-semibold px-3 py-2 rounded-lg transition-colors cursor-pointer"
          >
            Install Aplikasi
          </button>
          <button @click="showAboutModal = false" class="bg-[#0093DD] hover:bg-[#007bbd] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Help Modal -->
    <div v-if="showHelpModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full p-5 shadow-2xl border border-[#E2E8F0] animate-in fade-in zoom-in duration-150">
        <div class="flex justify-between items-start mb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#FEF6EB] text-[#EB891B] flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-[#1F2937]">Panduan Pertanyaan</h3>
          </div>
          <button @click="showHelpModal = false" class="text-[#64748B] hover:text-[#1F2937] p-1 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-xs text-[#4B5563] mb-2.5">Contoh pertanyaan statistik:</p>
        <div class="space-y-1.5 mb-4 text-[11px]">
          <div class="p-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#1F2937]">
            <div class="font-semibold text-[#1F2937]">1. Konsep Statistik:</div>
            "Apa itu inflasi?", "Jelaskan konsep PDRB", "Apa itu TPT?"
          </div>
          <div class="p-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#1F2937]">
            <div class="font-semibold text-[#1F2937]">2. Data Daerah & Publikasi:</div>
            "Data penduduk Sulawesi Tengah 2026", "Tampilkan publikasi kependudukan"
          </div>
          <div class="p-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[#1F2937]">
            <div class="font-semibold text-[#1F2937]">3. Kelembagaan & Pejabat:</div>
            "Siapa Kepala BPS Pusat?", "Alamat kantor BPS Kota Palu"
          </div>
        </div>
        <div class="flex justify-end">
          <button @click="showHelpModal = false" class="bg-[#0093DD] hover:bg-[#007bbd] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
            Mengerti
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { usePwa } from '../pwa';

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

const { isInstallable, installApp } = usePwa();

const showMobileMenu = ref(false);
const showAboutModal = ref(false);
const showHelpModal = ref(false);
</script>
