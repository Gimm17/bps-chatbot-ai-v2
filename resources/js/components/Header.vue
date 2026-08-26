<template>
  <header class="w-full bg-[#FAF8FF]/90 backdrop-blur-md border-b border-[#E2E7FF] sticky top-0 z-30 shadow-[0_1px_8px_rgba(0,0,0,0.04)] h-14 sm:h-16 px-3 sm:px-6">
    <div class="max-w-4xl mx-auto flex items-center justify-between h-full w-full gap-2">
      <!-- Logo & Brand Title -->
      <div class="flex items-center gap-2.5 sm:gap-3 cursor-pointer select-none shrink-0" @click="$emit('reset')">
        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-[#0058A9] to-[#00417F] flex items-center justify-center shadow-xs text-white shrink-0 overflow-hidden p-1">
          <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <path d="M9.5 23.5H22C25.5899 23.5 28.5 20.5899 28.5 17C28.5 13.5683 25.8366 10.7573 22.4668 10.521C21.7248 6.78652 18.4414 4 14.5 4C10.0526 4 6.38676 7.23432 5.67978 11.5173C3.0189 12.3789 1 14.9455 1 18C1 21.3137 3.68629 24 7 24L5 28L10.5 24.5L9.5 23.5Z" 
                  fill="white" fill-opacity="0.25" stroke="white" stroke-width="1.75" stroke-linejoin="round" />
            <rect x="9.5" y="14.5" width="2.2" height="5.5" rx="1.1" fill="#FE852B" />
            <rect x="13.5" y="11.5" width="2.2" height="8.5" rx="1.1" fill="#FFFFFF" />
            <rect x="17.5" y="13" width="2.2" height="7" rx="1.1" fill="#137333" />
            <path d="M24 5L24.8 7.2L27 8L24.8 8.8L24 11L23.2 8.8L21 8L23.2 7.2L24 5Z" fill="#FDE047" />
          </svg>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <h1 class="text-[14px] sm:text-[17px] font-bold text-[#00417F] tracking-tight whitespace-nowrap leading-none">BPS AI Assistant</h1>
          <span class="text-[9.5px] font-bold uppercase tracking-wider bg-[#FFDBC8] text-[#984700] px-2 py-0.5 rounded-full border border-[#FE852B]/30 shrink-0 hidden sm:inline-block">Demo</span>
        </div>
      </div>

      <!-- Action Nav Buttons: Desktop Mode (sm and up) -->
      <div class="hidden sm:flex items-center gap-2 shrink-0">
        <!-- Install App Button (PWA) -->
        <button 
          v-if="isInstallable && !isEmbedded"
          @click="installApp"
          class="text-xs font-semibold bg-[#EAEDFF] hover:bg-[#DAE2FD] text-[#0058A9] border border-[#DAE2FD] flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0"
          title="Install Aplikasi BPS AI ke Perangkat"
        >
          <span class="material-symbols-outlined text-[16px]">install_mobile</span>
          <span>Install App</span>
        </button>

        <!-- New Chat Button -->
        <button 
          v-if="hasMessages"
          @click="$emit('reset')"
          class="text-xs font-semibold text-[#131B2E] hover:text-[#0058A9] hover:bg-[#F2F3FF] flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0"
          title="Mulai percakapan baru"
        >
          <span class="material-symbols-outlined text-[16px]">add</span>
          <span>Chat Baru</span>
        </button>

        <!-- About Button -->
        <button 
          @click="showAboutModal = true"
          class="text-xs font-semibold text-[#424751] hover:text-[#0058A9] hover:bg-[#F2F3FF] px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0"
        >
          Tentang
        </button>

        <!-- Help Button -->
        <button 
          @click="showHelpModal = true"
          class="text-xs font-semibold text-[#424751] hover:text-[#0058A9] hover:bg-[#F2F3FF] px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0"
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
          class="w-8 h-8 rounded-lg bg-[#EAEDFF] text-[#0058A9] border border-[#DAE2FD] flex items-center justify-center hover:bg-[#DAE2FD] transition-colors cursor-pointer shrink-0"
          title="Install Aplikasi BPS AI"
        >
          <span class="material-symbols-outlined text-[18px]">install_mobile</span>
        </button>

        <!-- Mobile New Chat Icon -->
        <button 
          v-if="hasMessages"
          @click="$emit('reset')"
          class="w-8 h-8 rounded-lg bg-[#F2F3FF] text-[#131B2E] hover:text-[#0058A9] flex items-center justify-center transition-colors cursor-pointer shrink-0"
          title="Mulai percakapan baru"
        >
          <span class="material-symbols-outlined text-[18px]">add</span>
        </button>

        <!-- Mobile 3-Dots Menu Trigger -->
        <button 
          @click="showMobileMenu = !showMobileMenu"
          class="w-8 h-8 rounded-lg text-[#424751] hover:bg-[#F2F3FF] flex items-center justify-center transition-colors cursor-pointer shrink-0"
          title="Menu"
        >
          <span class="material-symbols-outlined text-[20px]">more_vert</span>
        </button>

        <!-- Mobile Dropdown Menu -->
        <div 
          v-if="showMobileMenu" 
          class="absolute right-0 top-10 w-48 bg-white rounded-xl shadow-xl border border-[#DAE2FD] py-1.5 z-50 animate-in fade-in zoom-in-95 duration-100"
          @click="showMobileMenu = false"
        >
          <button 
            v-if="isInstallable && !isEmbedded"
            @click="installApp"
            class="w-full text-left px-3.5 py-2 text-xs font-semibold text-[#0058A9] hover:bg-[#F2F3FF] flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-[16px]">install_mobile</span>
            Install Aplikasi
          </button>
          <button 
            @click="showAboutModal = true"
            class="w-full text-left px-3.5 py-2 text-xs text-[#131B2E] hover:bg-[#F2F3FF] flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-[16px] text-[#424751]">info</span>
            Tentang BPS AI
          </button>
          <button 
            @click="showHelpModal = true"
            class="w-full text-left px-3.5 py-2 text-xs text-[#131B2E] hover:bg-[#F2F3FF] flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-[16px] text-[#424751]">help_outline</span>
            Panduan & Bantuan
          </button>
        </div>
      </div>
    </div>

    <!-- Modals Teleported to Body -->
    <Teleport to="body">
      <!-- 1. Full-Featured About Modal (Sliced from REDESIGN_UI_UX/tentang_bps_ai_assistant) -->
      <div 
        v-if="showAboutModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#131B2E]/60 backdrop-blur-xs overflow-y-auto animate-in fade-in duration-150"
        @click.self="showAboutModal = false"
      >
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-5 sm:p-7 shadow-2xl border border-[#DAE2FD] animate-in fade-in zoom-in-95 duration-150 relative">
          <!-- Modal Header -->
          <div class="flex justify-between items-start mb-4 border-b border-[#E2E7FF] pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#EAEDFF] text-[#0058A9] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">smart_toy</span>
              </div>
              <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#EAEDFF] text-[#0058A9] text-[10.5px] font-bold mb-1">
                  <span class="material-symbols-outlined text-[13px]">verified</span>
                  Layanan Resmi Statistik Nasional
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-[#131B2E]">Tentang BPS AI Assistant</h3>
              </div>
            </div>
            <button @click="showAboutModal = false" class="text-[#424751] hover:text-[#131B2E] p-1.5 rounded-lg hover:bg-[#F2F3FF] transition-colors cursor-pointer" title="Tutup">
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

          <!-- Description -->
          <p class="text-xs sm:text-sm text-[#424751] leading-relaxed mb-5">
            <strong>BPS AI Assistant</strong> adalah layanan cerdas resmi dari Badan Pusat Statistik (BPS) Republik Indonesia yang dirancang untuk membantu masyarakat menavigasi, memahami, dan mengakses jutaan data statistik terverifikasi secara instan dan akurat.
          </p>

          <!-- 4-Step Process Section -->
          <div class="mb-5">
            <h4 class="text-xs sm:text-sm font-bold text-[#131B2E] mb-3 flex items-center gap-1.5">
              <span class="material-symbols-outlined text-[#0058A9] text-[18px]">account_tree</span>
              Bagaimana Jawaban Dibuat?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
              <div class="bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl p-3 text-center flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-[#EAEDFF] text-[#0058A9] flex items-center justify-center mb-2">
                  <span class="material-symbols-outlined text-[16px]">chat_bubble_outline</span>
                </div>
                <div class="text-[11.5px] font-bold text-[#131B2E] mb-0.5">1. Pertanyaan</div>
                <div class="text-[10px] text-[#424751]">User mengajukan query statistik.</div>
              </div>
              <div class="bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl p-3 text-center flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-[#D5E3FF] text-[#001B3C] flex items-center justify-center mb-2">
                  <span class="material-symbols-outlined text-[16px]">search</span>
                </div>
                <div class="text-[11.5px] font-bold text-[#131B2E] mb-0.5">2. WebAPI & PPID</div>
                <div class="text-[10px] text-[#424751]">Penarikan data 549 wilayah BPS.</div>
              </div>
              <div class="bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl p-3 text-center flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-[#FFDBC8] text-[#632C00] flex items-center justify-center mb-2">
                  <span class="material-symbols-outlined text-[16px]">memory</span>
                </div>
                <div class="text-[11.5px] font-bold text-[#131B2E] mb-0.5">3. Sintesis AI</div>
                <div class="text-[10px] text-[#424751]">Pemrosesan bahasa santun & terstruktur.</div>
              </div>
              <div class="bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl p-3 text-center flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-[#E6F4EA] text-[#137333] flex items-center justify-center mb-2">
                  <span class="material-symbols-outlined text-[16px]">verified_user</span>
                </div>
                <div class="text-[11.5px] font-bold text-[#131B2E] mb-0.5">4. Sitasi Resmi</div>
                <div class="text-[10px] text-[#424751]">Tautan verifikasi ke portal asli BPS.</div>
              </div>
            </div>
          </div>

          <!-- Feature Capabilities Section -->
          <div class="mb-5">
            <h4 class="text-xs sm:text-sm font-bold text-[#131B2E] mb-3 flex items-center gap-1.5">
              <span class="material-symbols-outlined text-[#FE852B] text-[18px]">bolt</span>
              Fitur & Kapabilitas Sistem
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-[#131B2E]">
              <div class="bg-[#F2F3FF] border border-[#E2E7FF] rounded-xl p-3 flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[#0058A9] text-[20px] shrink-0 mt-0.5">school</span>
                <div>
                  <div class="font-bold text-[#131B2E]">Penjelasan Konsep & Metodologi</div>
                  <div class="text-[11px] text-[#424751] mt-0.5">Definisi teknis Inflasi, PDRB, TPT, Kemiskinan, SIRuSa, Romantik.</div>
                </div>
              </div>
              <div class="bg-[#F2F3FF] border border-[#E2E7FF] rounded-xl p-3 flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[#FE852B] text-[20px] shrink-0 mt-0.5">menu_book</span>
                <div>
                  <div class="font-bold text-[#131B2E]">Katalog Publikasi & Dokumen PDF</div>
                  <div class="text-[11px] text-[#424751] mt-0.5">Pencarian buku statistik dan unduh rujukan resmi 549 wilayah.</div>
                </div>
              </div>
              <div class="bg-[#F2F3FF] border border-[#E2E7FF] rounded-xl p-3 flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[#137333] text-[20px] shrink-0 mt-0.5">account_balance</span>
                <div>
                  <div class="font-bold text-[#131B2E]">Profil PPID & Pejabat Daerah</div>
                  <div class="text-[11px] text-[#424751] mt-0.5">Pemisahan data akurat BPS Provinsi Sulteng vs BPS Kota Palu vs BPS RI.</div>
                </div>
              </div>
              <div class="bg-[#F2F3FF] border border-[#E2E7FF] rounded-xl p-3 flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[#0058A9] text-[20px] shrink-0 mt-0.5">install_mobile</span>
                <div>
                  <div class="font-bold text-[#131B2E]">PWA & Floating Embed Widget</div>
                  <div class="text-[11px] text-[#424751] mt-0.5">1-Click install di HP/Desktop dan kemudahan embed di website eksternal.</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Official Disclaimer Card -->
          <div class="bg-[#FFDBC8]/40 border-l-4 border-[#FE852B] rounded-r-xl p-3.5 flex items-start gap-2.5 mb-5">
            <span class="material-symbols-outlined text-[#FE852B] text-[20px] shrink-0 mt-0.5">warning</span>
            <div class="text-[11px] text-[#424751] leading-relaxed">
              <strong class="text-[#131B2E]">Penting untuk Diketahui:</strong> Meskipun AI Assistant dirancang dengan pengawasan ketat (*ScopeGuard*), sistem ini dapat membuat kesalahan. Harap selalu verifikasi informasi melalui tautan sitasi resmi yang disertakan pada setiap jawaban.
            </div>
          </div>

          <!-- Modal Actions Footer -->
          <div class="flex flex-col sm:flex-row justify-between items-center gap-2 pt-2 border-t border-[#E2E7FF]">
            <a href="https://www.bps.go.id" target="_blank" rel="noopener" class="text-xs font-semibold text-[#0058A9] hover:underline flex items-center gap-1">
              Kunjungi Portal Resmi BPS.go.id
              <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
              <button 
                v-if="isInstallable && !isEmbedded"
                @click="installApp(); showAboutModal = false;" 
                class="bg-[#EAEDFF] hover:bg-[#DAE2FD] text-[#0058A9] border border-[#DAE2FD] text-xs font-semibold px-3 py-2 rounded-lg transition-colors cursor-pointer"
              >
                Install App
              </button>
              <button @click="showAboutModal = false" class="bg-[#0058A9] hover:bg-[#00417F] text-white text-xs font-semibold px-5 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
                Tutup
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Help Modal (Panduan Pertanyaan) -->
      <div 
        v-if="showHelpModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#131B2E]/60 backdrop-blur-xs animate-in fade-in duration-150"
        @click.self="showHelpModal = false"
      >
        <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-[#DAE2FD] animate-in fade-in zoom-in-95 duration-150 relative">
          <div class="flex justify-between items-start mb-3 border-b border-[#E2E7FF] pb-3">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-[#FFDBC8] text-[#984700] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[20px]">help_outline</span>
              </div>
              <div>
                <h3 class="text-base font-bold text-[#131B2E]">Panduan Bertanya</h3>
                <p class="text-[11.5px] text-[#424751]">Tips mendapatkan jawaban data statistik paling akurat</p>
              </div>
            </div>
            <button @click="showHelpModal = false" class="text-[#424751] hover:text-[#131B2E] p-1.5 rounded-lg hover:bg-[#F2F3FF] transition-colors cursor-pointer" title="Tutup">
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

          <div class="space-y-2.5 mb-5 text-[11.5px]">
            <div class="p-3 bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl text-[#131B2E]">
              <div class="font-bold text-[#0058A9] flex items-center gap-1.5 mb-1">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                1. Data Statistik Spesifik (Sebutkan Wilayah & Tahun):
              </div>
              <p class="text-[#424751] italic">"Berapa inflasi Sulawesi Tengah 2024?", "Berapa angka kemiskinan Kota Palu 2023?"</p>
            </div>
            <div class="p-3 bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl text-[#131B2E]">
              <div class="font-bold text-[#FE852B] flex items-center gap-1.5 mb-1">
                <span class="material-symbols-outlined text-[16px]">menu_book</span>
                2. Publikasi & Dokumen:
              </div>
              <p class="text-[#424751] italic">"Cari publikasi Sulawesi Tengah Dalam Angka 2025", "Unduh data sensus pertanian"</p>
            </div>
            <div class="p-3 bg-[#FAF8FF] border border-[#DAE2FD] rounded-xl text-[#131B2E]">
              <div class="font-bold text-[#137333] flex items-center gap-1.5 mb-1">
                <span class="material-symbols-outlined text-[16px]">account_balance</span>
                3. Informasi Kelembagaan & PPID:
              </div>
              <p class="text-[#424751] italic">"Siapa Kepala BPS Kota Palu dan alamat kantornya?", "Siapa Kepala BPS Sulteng?"</p>
            </div>
          </div>

          <div class="flex justify-end">
            <button @click="showHelpModal = false" class="bg-[#0058A9] hover:bg-[#00417F] text-white text-xs font-semibold px-5 py-2 rounded-lg transition-colors cursor-pointer shadow-xs">
              Mengerti
            </button>
          </div>
        </div>
      </div>
    </Teleport>
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
