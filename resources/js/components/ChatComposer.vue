<template>
  <div :class="['w-full bg-gradient-to-t from-[#F8FAFC] via-[#F8FAFC] to-transparent sticky bottom-0 z-20', isEmbedded ? 'pt-2 pb-2 px-2.5' : 'pt-4 pb-3 px-4']">
    <div class="max-w-3xl mx-auto flex flex-col items-center w-full">
      <!-- Main Composer Box -->
      <div 
        :class="['w-full bg-white border border-slate-300 focus-within:border-[#00ADEF] focus-within:ring-2 focus-within:ring-[#00ADEF]/20 rounded-2xl flex items-end gap-2 shadow-2xs transition-all', isEmbedded ? 'p-2' : 'p-2.5']"
      >
        <!-- Textarea -->
        <textarea
          ref="textareaRef"
          v-model="inputMessage"
          @keydown="handleKeyDown"
          @input="autoResize"
          :disabled="loading"
          rows="1"
          placeholder="Tanyakan sesuatu tentang data/layanan BPS..."
          :class="['flex-1 max-h-32 resize-none border-0 bg-transparent text-slate-900 placeholder:text-slate-400 focus:ring-0 focus:outline-hidden py-1 leading-relaxed', isEmbedded ? 'text-xs px-1.5' : 'text-sm md:text-[15px] px-2.5']"
        ></textarea>

        <!-- Send Button -->
        <button
          @click="submitMessage"
          :disabled="!canSend"
          :class="['rounded-xl flex items-center justify-center transition-all shrink-0 cursor-pointer', isEmbedded ? 'w-8 h-8' : 'w-9 h-9', canSend 
            ? 'bg-gradient-to-r from-[#0077A6] to-[#00ADEF] hover:from-[#005F85] hover:to-[#0095CC] text-white shadow-2xs' 
            : 'bg-slate-100 text-slate-400 cursor-not-allowed']"
          title="Kirim pesan (Enter)"
        >
          <svg v-if="!loading" :class="['transform rotate-45 -translate-x-0.5', isEmbedded ? 'w-3.5 h-3.5' : 'w-4 h-4']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
          <svg v-else :class="['animate-spin text-[#0077A6]', isEmbedded ? 'w-3.5 h-3.5' : 'w-4 h-4']" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
        </button>
      </div>

      <!-- Legal / AI Accuracy Disclaimer -->
      <p :class="['text-slate-400 text-center leading-tight', isEmbedded ? 'text-[10px] mt-1' : 'text-[11px] mt-2']">
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
