<template>
  <div class="w-full bg-[#FAF8FF]/90 backdrop-blur-md border-t border-[#E2E7FF] pt-2 sm:pt-3 pb-2 sm:pb-4 px-3 sm:px-6 shrink-0 shadow-[0_-1px_8px_rgba(0,0,0,0.03)]">
    <div class="max-w-3xl mx-auto flex flex-col items-center w-full">
      <!-- Main Composer Pill Box -->
      <div 
        class="w-full bg-white border border-[#DAE2FD] focus-within:border-[#0058A9] focus-within:ring-2 focus-within:ring-[#0058A9]/20 rounded-2xl flex items-end gap-2 shadow-[0_2px_10px_rgba(0,0,0,0.04)] transition-all p-1.5 sm:p-2"
      >
        <!-- Quick Topic / Sparkle Icon -->
        <div class="p-1.5 sm:p-2 text-[#0058A9] flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[20px]">smart_toy</span>
        </div>

        <!-- Textarea Input -->
        <textarea
          ref="textareaRef"
          v-model="inputMessage"
          @keydown="handleKeyDown"
          @input="autoResize"
          :disabled="loading"
          rows="1"
          placeholder="Tanyakan sesuatu tentang data atau layanan BPS..."
          class="flex-1 max-h-32 resize-none border-0 bg-transparent text-[#131B2E] placeholder:text-[#727783] focus:ring-0 focus:outline-hidden py-1 px-1 text-xs sm:text-[14.5px] leading-relaxed"
        ></textarea>

        <!-- Send Button -->
        <button
          @click="submitMessage"
          :disabled="!canSend"
          :class="['w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-all shrink-0 cursor-pointer', canSend 
            ? 'bg-[#FE852B] hover:bg-[#984700] text-white shadow-xs active:scale-95' 
            : 'bg-[#F2F3FF] text-[#C2C6D3] cursor-not-allowed']"
          title="Kirim pesan (Enter)"
        >
          <span v-if="!loading" class="material-symbols-outlined text-[18px] sm:text-[20px]" style="font-variation-settings: 'FILL' 1;">send</span>
          <svg v-else class="w-4 h-4 animate-spin text-[#0058A9]" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
        </button>
      </div>

      <!-- Legal / AI Accuracy Disclaimer -->
      <p class="text-[10.5px] sm:text-[11.5px] text-[#727783] text-center leading-tight mt-2">
        BPS AI dapat melakukan kesalahan. Verifikasi informasi penting melalui rujukan resmi BPS.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';

const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  isEmbedded: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['send']);

const inputMessage = ref('');
const textareaRef = ref(null);

const canSend = computed(() => {
  return inputMessage.value.trim().length > 0 && !props.loading;
});

const autoResize = () => {
  nextTick(() => {
    if (textareaRef.value) {
      textareaRef.value.style.height = 'auto';
      textareaRef.value.style.height = Math.min(textareaRef.value.scrollHeight, 120) + 'px';
    }
  });
};

const handleKeyDown = (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    submitMessage();
  }
};

const submitMessage = () => {
  if (!canSend.value) return;
  const msg = inputMessage.value.trim();
  inputMessage.value = '';
  autoResize();
  emit('send', msg);
};

const setMessage = (text) => {
  inputMessage.value = text;
  autoResize();
  if (textareaRef.value) {
    textareaRef.value.focus();
  }
};

defineExpose({ setMessage });
</script>
