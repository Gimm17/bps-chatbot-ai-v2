<template>
  <div class="w-full flex flex-col gap-3 py-1.5 animate-fade-in-up">
    <!-- 1. USER MESSAGE -->
    <div v-if="message.role === 'user'" class="flex justify-end w-full">
      <div class="bg-[#FE852B] text-white rounded-t-2xl rounded-l-2xl rounded-br-xs px-4 sm:px-5 py-2.5 sm:py-3.5 max-w-[88%] sm:max-w-[78%] shadow-xs">
        <p class="text-[12.5px] sm:text-[14.5px] font-medium leading-relaxed whitespace-pre-wrap break-words">{{ message.content }}</p>
      </div>
    </div>

    <!-- 2. ASSISTANT MESSAGE -->
    <div v-else class="flex items-start gap-2.5 sm:gap-3.5 w-full">
      <!-- AI Bot Avatar -->
      <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#0058A9] text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
        <span class="material-symbols-outlined text-[18px] sm:text-[20px]" style="font-variation-settings: 'FILL' 1;">smart_toy</span>
      </div>

      <!-- AI Content Box -->
      <div class="flex-1 min-w-0 max-w-[92%] sm:max-w-[85%]">
        <!-- If regular Answered state -->
        <div v-if="!message.status || message.status === 'answered'" class="bg-white border border-[#DAE2FD] rounded-t-2xl rounded-r-2xl rounded-bl-xs p-4 sm:p-5 shadow-xs flex flex-col gap-3">
          <!-- Markdown Rendered Answer -->
          <div class="ai-markdown" v-html="renderedAnswer"></div>

          <!-- Source Citations Card -->
          <SourceCard 
            v-if="message.citations && message.citations.length > 0" 
            :sources="message.citations" 
          />

          <!-- Bottom Action Toolbar (Like, Dislike, Copy) -->
          <div class="flex items-center justify-between pt-2 border-t border-[#E2E7FF]/80 mt-1">
            <div class="flex items-center gap-1 text-[#424751]">
              <!-- Thumb Up Button -->
              <button 
                @click="sendFeedback('helpful')"
                :class="['p-1.5 rounded-lg hover:bg-[#F2F3FF] transition-colors cursor-pointer flex items-center justify-center', 
                  feedbackState === 'helpful' ? 'text-[#137333] bg-[#E6F4EA]' : 'text-[#424751] hover:text-[#0058A9]']"
                title="Jawaban Membantu"
              >
                <span class="material-symbols-outlined text-[18px]" :style="feedbackState === 'helpful' ? 'font-variation-settings: \'FILL\' 1;' : ''">thumb_up</span>
              </button>

              <!-- Thumb Down Button -->
              <button 
                @click="sendFeedback('not_helpful')"
                :class="['p-1.5 rounded-lg hover:bg-[#F2F3FF] transition-colors cursor-pointer flex items-center justify-center', 
                  feedbackState === 'not_helpful' ? 'text-[#BA1A1A] bg-[#FFDAD6]' : 'text-[#424751] hover:text-[#BA1A1A]']"
                title="Jawaban Kurang Tepat"
              >
                <span class="material-symbols-outlined text-[18px]" :style="feedbackState === 'not_helpful' ? 'font-variation-settings: \'FILL\' 1;' : ''">thumb_down</span>
              </button>

              <!-- Copy Text Button -->
              <button 
                @click="copyAnswer"
                class="p-1.5 rounded-lg hover:bg-[#F2F3FF] text-[#424751] hover:text-[#0058A9] transition-colors cursor-pointer flex items-center justify-center ml-1"
                title="Salin Teks Jawaban"
              >
                <span class="material-symbols-outlined text-[18px]">{{ isCopied ? 'check' : 'content_copy' }}</span>
              </button>

              <span v-if="isCopied" class="text-[11px] text-[#137333] font-semibold ml-1 animate-in fade-in">Tersalin!</span>
              <span v-else-if="feedbackThanks" class="text-[11px] text-[#0058A9] font-medium ml-1 animate-in fade-in">Terima kasih atas feedback Anda!</span>
            </div>

            <!-- Official BPS Indicator Tag -->
            <span class="text-[10px] text-[#727783] font-medium hidden sm:inline-block">
              Verifikasi BPS
            </span>
          </div>
        </div>

        <!-- If State Card (Clarification, No Evidence, Out of Scope, Error) -->
        <StateCard 
          v-else 
          :status="message.status" 
          :answer="message.answer || message.content"
          :clarification-question="message.clarificationQuestion"
          @select-chip="$emit('select-chip', $event)"
          @retry="$emit('retry', message)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import SourceCard from './SourceCard.vue';
import StateCard from './StateCard.vue';

const props = defineProps({
  message: {
    type: Object,
    required: true
  }
});

defineEmits(['select-chip', 'retry']);

const feedbackState = ref(null);
const feedbackThanks = ref(false);
const isCopied = ref(false);

const renderedAnswer = computed(() => {
  const text = props.message.answer || props.message.content || '';
  if (!text) return '';
  
  // Format inline citation [1], [2] into styled badges
  let formattedText = text.replace(/\[(\d+)\]/g, (match, num) => {
    return `<span class="inline-flex items-center justify-center w-5 h-5 rounded bg-[#EAEDFF] text-[10px] font-bold text-[#0058A9] cursor-pointer align-middle hover:bg-[#DAE2FD] transition-colors">[${num}]</span>`;
  });

  const parsed = marked.parse(formattedText, { breaks: true, gfm: true });
  return DOMPurify.sanitize(parsed);
});

const sendFeedback = async (rating) => {
  if (feedbackState.value === rating) return;
  feedbackState.value = rating;

  try {
    await fetch('/api/feedback', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        requestId: props.message.requestId || null,
        rating: rating,
        comment: null
      })
    });
    feedbackThanks.value = true;
    setTimeout(() => {
      feedbackThanks.value = false;
    }, 4000);
  } catch (e) {
    // Fail silently in demo
  }
};

const copyAnswer = async () => {
  const text = props.message.answer || props.message.content || '';
  if (!text) return;

  try {
    await navigator.clipboard.writeText(text);
    isCopied.value = true;
    setTimeout(() => {
      isCopied.value = false;
    }, 2000);
  } catch (e) {
    // Fallback
  }
};
</script>
