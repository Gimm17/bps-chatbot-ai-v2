<template>
  <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
    <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
      <!-- Logo & Brand -->
      <div class="flex items-center gap-3 cursor-pointer" @click="$emit('reset')">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#00ADEF] to-[#0077A6] flex items-center justify-center shadow-xs text-white shrink-0 overflow-hidden">
          <svg width="24" height="24" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="flex flex-col">
          <div class="flex items-center gap-2">
            <h1 class="text-lg font-bold text-slate-900 leading-tight">BPS AI Assistant</h1>
            <span class="text-[11px] font-semibold uppercase tracking-wider bg-blue-50 text-[#0077A6] px-2 py-0.5 rounded-full border border-blue-200">Demo</span>
          </div>
          <span class="text-xs text-slate-500">Asisten Statistik Publik Resmi</span>
        </div>
      </div>

      <!-- Action Nav -->
      <div class="flex items-center gap-3">
        <button 
          v-if="hasMessages"
          @click="$emit('reset')"
          class="text-xs font-medium text-slate-600 hover:text-[#0077A6] flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors"
          title="Mulai percakapan baru"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span class="hidden sm:inline">Chat Baru</span>
        </button>

        <button 
          @click="showAboutModal = true"
          class="text-xs font-medium text-slate-600 hover:text-[#0077A6] px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors"
        >
          Tentang
        </button>

        <button 
          @click="showHelpModal = true"
          class="text-xs font-medium text-slate-600 hover:text-[#0077A6] px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors"
        >
          Bantuan
        </button>
      </div>
    </div>

    <!-- About Modal -->
    <div v-if="showAboutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="flex justify-between items-start mb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#0077A6] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Tentang BPS AI Assistant</h3>
          </div>
          <button @click="showAboutModal = false" class="text-slate-400 hover:text-slate-600 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-sm text-slate-600 leading-relaxed mb-4">
          <strong>BPS AI Assistant</strong> adalah asisten informasi publik berbasis kecerdasan buatan untuk membantu masyarakat, mahasiswa, akademisi, dan pembuat kebijakan menelusuri data statistik resmi, definisi indikator, publikasi, metodologi, dan layanan Badan Pusat Statistik.
        </p>
        <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-3 text-xs text-slate-700 space-y-1 mb-4">
          <div class="font-semibold text-[#0077A6]">Keunggulan Sistem:</div>
          <div>• Terkoneksi ke <strong>BPS WebAPI</strong> dan basis pengetahuan resmi BPS.</div>
          <div>• Dilengkapi <strong>Source Verification</strong> dan kartu rujukan sumber terpercaya.</div>
          <div>• Siap di-embed sebagai widget pada seluruh portal website BPS.</div>
        </div>
        <div class="flex justify-end">
          <button @click="showAboutModal = false" class="bg-[#0077A6] hover:bg-[#005F85] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Help Modal -->
    <div v-if="showHelpModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 animate-in fade-in zoom-in duration-200">
        <div class="flex justify-between items-start mb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-50 text-[#F7941D] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Panduan & Contoh Pertanyaan</h3>
          </div>
          <button @click="showHelpModal = false" class="text-slate-400 hover:text-slate-600 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-sm text-slate-600 mb-3">Anda dapat menanyakan hal-hal seputar BPS:</p>
        <div class="space-y-2 mb-4 text-xs">
          <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
            <div class="font-semibold text-slate-900">1. Konsep & Istilah Statistik:</div>
            "Apa itu inflasi?", "Jelaskan perbedaan PDRB harga berlaku dan harga konstan", "Apa itu TPT?"
          </div>
          <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
            <div class="font-semibold text-slate-900">2. Publikasi & Berita Resmi:</div>
            "Bagaimana cara mencari publikasi sensus penduduk?", "Tampilkan publikasi statistik terbaru"
          </div>
          <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
            <div class="font-semibold text-slate-900">3. Layanan & Konsultasi Data:</div>
            "Bagaimana cara mengajukan rekomendasi statistik ke BPS?", "Apa saja layanan PST?"
          </div>
        </div>
        <div class="flex justify-end">
          <button @click="showHelpModal = false" class="bg-[#0077A6] hover:bg-[#005F85] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
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
  }
});

defineEmits(['reset']);

const showAboutModal = ref(false);
const showHelpModal = ref(false);
</script>
