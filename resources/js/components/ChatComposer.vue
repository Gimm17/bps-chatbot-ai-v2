<template>
  <div class="w-full bg-gradient-to-t from-[#F8FAFC] via-[#F8FAFC] to-transparent pt-4 pb-3 px-4 sticky bottom-0 z-20">
    <div class="max-w-3xl mx-auto flex flex-col items-center">
      <!-- Main Composer Box -->
      <div 
        class="w-full bg-white border border-slate-300 focus-within:border-[#00ADEF] focus-within:ring-2 focus-within:ring-[#00ADEF]/20 rounded-2xl p-2.5 flex items-end gap-2 shadow-sm transition-all"
      >
        <!-- Textarea -->
        <textarea
          ref="textareaRef"
          v-model="inputMessage"
          @keydown="handleKeyDown"
          @input="autoResize"
          :disabled="loading"
          rows="1"
          placeholder="Tanyakan sesuatu tentang BPS..."
          class="flex-1 max-h-36 resize-none border-0 bg-transparent text-sm md:text-[15px] text-slate-900 placeholder:text-slate-400 focus:ring-0 focus:outline-hidden py-1 px-2.5 leading-relaxed"
        ></textarea>

        <!-- Send Button -->
        <button
          @click="submitMessage"
          :disabled="!canSend"
          class="w-9 h-9 rounded-xl flex items-center justify-center transition-all shrink-0 cursor-pointer"
          :class="canSend 
            ? 'bg-[#0077A6] hover:bg-[#005F85] text-white shadow-xs' 
            : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
          title="Kirim pesan (Enter)"
        >
          <svg v-if="!loading" class="w-4 h-4 transform rotate-45 -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
          <svg v-else class="w-4 h-4 animate-spin text-[#0077A6]" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
        </button>
      </div>

      <!-- Legal / AI Accuracy Disclaimer -->
      <p class="text-[11px] text-slate-400 text-center mt-2">
        BPS AI dapat melakukan kesalahan. Harap verifikasi informasi melalui sumber data resmi BPS.
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
      textareaRef.value.style.height = Math.min(textareaRef.value.scrollHeight, 144) + 'px';
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
