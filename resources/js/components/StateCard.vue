<template>
  <div>
    <!-- State 1: Clarification Required -->
    <div v-if="status === 'clarification_required'" class="bg-[#EBF7FD] border border-[#0093DD]/30 rounded-2xl p-4 md:p-5">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#0093DD] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="flex-1">
          <h4 class="text-sm font-bold text-[#1F2937] mb-1">Perlu Informasi Tambahan</h4>
          <p class="text-sm text-[#4B5563] leading-relaxed mb-3.5">
            {{ clarificationQuestion || 'Wilayah dan periode tahun mana yang Anda maksud untuk data ini?' }}
          </p>

          <!-- Quick Suggestion Chips -->
          <div class="flex flex-wrap gap-2 pt-1">
            <button 
              v-for="chip in clarificationChips" 
              :key="chip"
              @click="$emit('select-chip', chip)"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-[#0093DD] border border-[#0093DD]/30 hover:bg-[#d8f0fb] transition-colors shadow-2xs cursor-pointer"
            >
              <span>{{ chip }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- State 2: No Evidence Found -->
    <div v-else-if="status === 'no_evidence'" class="bg-[#FEF6EB] border border-[#EB891B]/30 rounded-2xl p-4 md:p-5">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#EB891B] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <div class="flex-1">
          <h4 class="text-sm font-bold text-[#1F2937] mb-1">Informasi Belum Ditemukan</h4>
          <p class="text-sm text-[#4B5563] leading-relaxed mb-3.5">
            {{ answer || 'Saya belum menemukan sumber BPS yang cukup untuk memastikan jawaban tersebut.' }}
          </p>
          <div class="flex flex-wrap gap-2">
            <a 
              href="https://www.bps.go.id" 
              target="_blank" 
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-[#EB891B] border border-[#EB891B]/40 hover:bg-[#FEF6EB] transition-colors"
            >
              <span>Cari di Portal BPS.go.id</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- State 3: Out of Scope -->
    <div v-else-if="status === 'out_of_scope'" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-4 md:p-5">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#64748B] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
          <svg class="w-6 h-6 p-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="10" rx="2"/>
            <circle cx="12" cy="5" r="2"/>
            <path d="M12 7v4"/>
          </svg>
        </div>
        <div class="flex-1">
          <h4 class="text-sm font-bold text-[#1F2937] mb-1">Topik di Luar Cakupan BPS</h4>
          <p class="text-sm text-[#4B5563] leading-relaxed mb-3.5">
            {{ answer || 'Saya difokuskan untuk membantu pertanyaan seputar data statistik, publikasi, metodologi, dan layanan Badan Pusat Statistik (BPS) Indonesia.' }}
          </p>
          <div class="text-xs font-semibold text-[#64748B] mb-2">Mungkin Anda ingin menanyakan:</div>
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="suggest in outOfScopeSuggestions" 
              :key="suggest"
              @click="$emit('select-chip', suggest)"
              class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-white text-[#1F2937] border border-[#E2E8F0] hover:border-[#0093DD] hover:text-[#0093DD] hover:bg-[#EBF7FD] transition-colors cursor-pointer"
            >
              {{ suggest }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- State 4: Provider Error / Rate Limit -->
    <div v-else-if="status === 'provider_error' || status === 'rate_limited'" class="bg-rose-50/80 border border-rose-200 rounded-2xl p-4 md:p-5">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="flex-1">
          <h4 class="text-sm font-bold text-rose-900 mb-1">
            {{ status === 'rate_limited' ? 'Batas Permintaan Tercapai' : 'Layanan AI Tidak Tersedia' }}
          </h4>
          <p class="text-sm text-rose-800 leading-relaxed mb-3">
            {{ answer || 'Silakan coba beberapa saat lagi atau akses data melalui portal resmi BPS.' }}
          </p>
          <div class="flex gap-2">
            <button 
              @click="$emit('retry')"
              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white transition-colors cursor-pointer"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Coba Lagi
            </button>
            <a 
              href="https://www.bps.go.id" 
              target="_blank" 
              class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-[#E2E8F0] hover:bg-slate-100 transition-colors"
            >
              Kunjungi BPS.go.id
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  status: {
    type: String,
    required: true
  },
  answer: {
    type: String,
    default: null
  },
  clarificationQuestion: {
    type: String,
    default: null
  }
});

defineEmits(['select-chip', 'retry']);

const clarificationChips = [
  'Seluruh Indonesia',
  'Provinsi Sulawesi Tengah',
  'Provinsi Jawa Barat',
  'Kota Palu',
  'Tahun 2025',
  'Tahun 2024'
];

const outOfScopeSuggestions = [
  'Apa itu inflasi?',
  'Bagaimana mencari publikasi BPS?',
  'Jelaskan konsep PDRB'
];
</script>
