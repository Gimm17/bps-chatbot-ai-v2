<template>
  <div class="mt-2.5 pt-2.5 border-t border-[#DAE2FD]/80 flex flex-col gap-2">
    <div class="flex items-center gap-1.5 text-[#424751]">
      <span class="material-symbols-outlined text-[16px] text-[#0058A9]">menu_book</span>
      <span class="text-xs font-bold text-[#131B2E]">Sumber Rujukan Resmi BPS</span>
    </div>

    <div class="flex flex-col gap-1.5">
      <a 
        v-for="(source, idx) in sources" 
        :key="idx"
        :href="source.url || '#'" 
        target="_blank" 
        rel="noopener noreferrer"
        class="flex items-center justify-between p-2.5 rounded-xl border border-[#DAE2FD] bg-[#FAF8FF] hover:bg-[#EAEDFF] transition-all group shadow-2xs"
      >
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
          <!-- Green Verified Shield Badge -->
          <div class="w-8 h-8 rounded-lg bg-[#E6F4EA] text-[#137333] flex items-center justify-center shrink-0 shadow-2xs">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">verified</span>
          </div>
          
          <div class="flex flex-col min-w-0 flex-1">
            <div class="flex items-center gap-1.5">
              <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-[#DAE2FD] text-[#00417F]">
                [{{ idx + 1 }}]
              </span>
              <span class="text-xs font-bold text-[#131B2E] group-hover:text-[#0058A9] transition-colors truncate">
                {{ source.title || 'Publikasi Resmi BPS' }}
              </span>
            </div>
            <span class="text-[10.5px] text-[#424751] truncate mt-0.5">
              {{ source.snippet || 'Badan Pusat Statistik (BPS) Republik Indonesia' }}
            </span>
          </div>
        </div>

        <div class="flex items-center gap-1 text-[#0058A9] text-xs font-semibold shrink-0 ml-2 group-hover:translate-x-0.5 transition-transform">
          <span class="hidden sm:inline">Buka</span>
          <span class="material-symbols-outlined text-[15px]">open_in_new</span>
        </div>
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  sources: {
    type: Array,
    default: () => []
  }
});
</script>
