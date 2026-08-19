<template>
  <div class="bg-[#FFFFFF] border border-[#E2E8F0] rounded-xl p-3 hover:border-[#0093DD]/40 transition-colors shadow-2xs">
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
      <div class="flex items-start gap-2 min-w-0">
        <!-- Number Index Badge -->
        <span class="inline-flex items-center justify-center w-4.5 h-4.5 rounded-md bg-[#EBF7FD] text-[#0093DD] font-bold text-[10px] shrink-0 mt-0.5 border border-[#0093DD]/30">
          [{{ index + 1 }}]
        </span>

        <div class="min-w-0 flex-1">
          <!-- Title & Verified Badge -->
          <div class="flex items-center gap-1.5 flex-wrap">
            <h4 class="text-xs font-bold text-[#1F2937] leading-snug break-words">{{ citation.title }}</h4>
            <span 
              v-if="citation.verified" 
              class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9.5px] font-semibold bg-[#F0F9EA] text-[#68B92E] border border-[#68B92E]/30 shrink-0"
              title="Terverifikasi dari BPS WebAPI Resmi"
            >
              <svg class="w-2.5 h-2.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              Resmi BPS
            </span>
            <span 
              v-else 
              class="inline-flex items-center px-1.5 py-0.2 rounded text-[9.5px] font-medium bg-slate-100 text-[#64748B] border border-[#E2E8F0] shrink-0"
            >
              Knowledge Base
            </span>
          </div>

          <!-- Snippet (if available) -->
          <p v-if="citation.snippet" class="text-[11px] text-[#64748B] mt-1 line-clamp-2 leading-relaxed break-words">
            {{ citation.snippet }}
          </p>
        </div>
      </div>

      <!-- External Link CTA -->
      <a 
        v-if="citation.url" 
        :href="citation.url" 
        target="_blank" 
        rel="noopener noreferrer"
        class="text-[11px] font-semibold text-[#0093DD] hover:text-[#007bbd] hover:underline flex items-center justify-center gap-1 shrink-0 bg-[#EBF7FD] hover:bg-[#d8f0fb] px-2.5 py-1 rounded-lg border border-[#0093DD]/30 transition-colors self-start sm:self-auto"
      >
        <span>Buka sumber</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  citation: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    default: 0
  }
});
</script>
