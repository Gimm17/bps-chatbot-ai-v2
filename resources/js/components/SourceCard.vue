<template>
  <div class="bg-white border border-slate-200 rounded-xl p-3.5 hover:border-blue-300 transition-colors shadow-xs">
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-start gap-2.5">
        <!-- Number Index Badge -->
        <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-50 text-[#0077A6] font-bold text-[11px] shrink-0 mt-0.5 border border-blue-200/60">
          [{{ index + 1 }}]
        </span>

        <div>
          <!-- Title & Verified Badge -->
          <div class="flex items-center gap-1.5 flex-wrap">
            <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ citation.title }}</h4>
            <span 
              v-if="citation.verified" 
              class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"
              title="Terverifikasi dari BPS WebAPI Resmi"
            >
              <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              Resmi BPS
            </span>
            <span 
              v-else 
              class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200"
            >
              Knowledge Base
            </span>
          </div>

          <!-- Snippet (if available) -->
          <p v-if="citation.snippet" class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
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
        class="text-xs font-semibold text-[#0077A6] hover:text-[#005F85] hover:underline flex items-center gap-1 shrink-0 bg-blue-50/70 hover:bg-blue-100 px-2.5 py-1 rounded-lg border border-blue-200/50 transition-colors"
      >
        <span>Buka sumber</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
