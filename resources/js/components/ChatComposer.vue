<template>
  <div class="w-full bg-white sm:bg-transparent border-t border-slate-200/80 sm:border-0 pt-2 pb-2 sm:pb-3 px-3 sm:px-4 shrink-0 shadow-xs sm:shadow-none">
    <div class="max-w-3xl mx-auto flex flex-col items-center w-full">
      <!-- Main Composer Box -->
      <div 
        class="w-full bg-slate-50 sm:bg-white border border-slate-300/90 focus-within:border-[#00ADEF] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#00ADEF]/20 rounded-2xl flex items-end gap-2 shadow-2xs transition-all p-1.5 sm:p-2"
      >
        <!-- Textarea -->
        <textarea
          ref="textareaRef"
          v-model="inputMessage"
          @keydown="handleKeyDown"
          @input="autoResize"
          :disabled="loading"
          rows="1"
          placeholder="Tanyakan data/layanan BPS..."
          class="flex-1 max-h-28 resize-none border-0 bg-transparent text-slate-900 placeholder:text-slate-400 focus:ring-0 focus:outline-hidden py-1 px-2 text-xs sm:text-sm leading-relaxed"
        ></textarea>

        <!-- Send Button -->
        <button
          @click="submitMessage"
          :disabled="!canSend"
          :class="['w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center transition-all shrink-0 cursor-pointer', canSend 
            ? 'bg-gradient-to-r from-[#0077A6] to-[#00ADEF] hover:from-[#005F85] hover:to-[#0095CC] text-white shadow-2xs active:scale-95' 
            : 'bg-slate-200/80 text-slate-400 cursor-not-allowed']"
          title="Kirim pesan (Enter)"
        >
          <svg v-if="!loading" class="w-3.5 h-3.5 sm:w-4 sm:h-4 transform rotate-45 -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
          <svg v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 animate-spin text-[#0077A6]" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
        </button>
      </div>

      <!-- Legal / AI Accuracy Disclaimer -->
      <p class="text-[10px] sm:text-[11px] text-slate-400 text-center leading-tight mt-1">
        BPS AI dapat melakukan kesalahan. Harap verifikasi melalui rujukan resmi BPS.
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
      textareaRef.value.style.height = Math.min(textareaRef.value.scrollHeight, 110) + 'px';
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
