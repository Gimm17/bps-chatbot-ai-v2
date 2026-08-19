<template>
  <div class="w-full flex flex-col gap-3 py-1.5">
    <!-- 1. USER MESSAGE -->
    <div v-if="message.role === 'user'" class="flex justify-end w-full">
      <div class="bg-[#E8F8FE] text-slate-900 border border-blue-200/80 rounded-2xl rounded-tr-xs px-3.5 sm:px-4 py-2.5 sm:py-3 max-w-[90%] sm:max-w-[78%] shadow-2xs">
        <p class="text-[13.5px] sm:text-[14.5px] leading-relaxed whitespace-pre-wrap break-words">{{ message.content }}</p>
      </div>
    </div>

    <!-- 2. ASSISTANT MESSAGE -->
    <div v-else class="flex items-start gap-2 sm:gap-3 w-full">
      <!-- AI Avatar -->
      <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-gradient-to-br from-[#00ADEF] to-[#0077A6] text-white flex items-center justify-center shrink-0 shadow-2xs mt-1 overflow-hidden">
        <svg width="14" height="14" class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="11" width="18" height="10" rx="2"/>
          <circle cx="12" cy="5" r="2"/>
          <path d="M12 7v4"/>
        </svg>
      </div>

      <!-- AI Content Box -->
      <div class="flex-1 min-w-0 max-w-full space-y-2.5">
        <!-- Dedicated State Card (if not standard answered) -->
        <StateCard 
          v-if="message.status && message.status !== 'answered'"
          :status="message.status"
          :answer="message.answer"
          :clarification-question="message.clarificationQuestion"
          @select-chip="$emit('select-chip', $event)"
          @retry="$emit('retry', message)"
        />

        <!-- Standard Answered Card -->
        <div v-else class="bg-white border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 md:p-5 shadow-xs overflow-hidden">
          <!-- Formatted Markdown Body -->
          <div class="ai-markdown overflow-x-auto break-words" v-html="renderedMarkdown"></div>

          <!-- Sources / Citations Section -->
          <div v-if="message.citations && message.citations.length > 0" class="mt-4 pt-3.5 border-t border-slate-100">
            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-2">
              <svg class="w-3.5 h-3.5 text-[#0077A6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              <span>Sumber Rujukan</span>
            </div>

            <div class="space-y-2">
              <SourceCard 
                v-for="(citation, idx) in message.citations" 
                :key="citation.sourceId || idx"
                :citation="citation"
                :index="idx"
              />
            </div>
          </div>

          <!-- Feedback Action Bar -->
          <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-slate-400 text-xs flex-wrap gap-2">
            <span class="text-[10.5px] sm:text-[11px] text-slate-400">Apakah jawaban ini membantu?</span>
            <div class="flex items-center gap-1">
              <!-- Thumbs Up -->
              <button 
                @click="sendFeedback('helpful')" 
                class="p-1.5 rounded-lg hover:bg-slate-100 transition-colors"
                :class="{ 'text-emerald-600 bg-emerald-50': feedbackGiven === 'helpful' }"
                title="Membantu"
              >
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                </svg>
              </button>

              <!-- Thumbs Down -->
              <button 
                @click="sendFeedback('not_helpful')" 
                class="p-1.5 rounded-lg hover:bg-slate-100 transition-colors"
                :class="{ 'text-rose-600 bg-rose-50': feedbackGiven === 'not_helpful' }"
                title="Kurang membantu"
              >
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76 1.94m-7 9v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                </svg>
              </button>

              <!-- Copy Answer -->
              <button 
                @click="copyAnswer" 
                class="p-1.5 rounded-lg hover:bg-slate-100 transition-colors text-slate-500"
                :title="copied ? 'Disalin!' : 'Salin jawaban'"
              >
                <svg v-if="!copied" class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <svg v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
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

const feedbackGiven = ref(null);
const copied = ref(false);

const renderedMarkdown = computed(() => {
  const text = props.message.content || props.message.answer || '';
  const parsed = marked.parse(text);
  return DOMPurify.sanitize(parsed);
});

const sendFeedback = async (rating) => {
  feedbackGiven.value = rating;
  try {
    await fetch('/api/feedback', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        requestId: props.message.requestId,
        rating: rating,
      })
    });
  } catch (e) {
    // Silent fail for feedback
  }
};

const copyAnswer = () => {
  const text = props.message.content || props.message.answer || '';
  navigator.clipboard.writeText(text);
  copied.value = true;
  setTimeout(() => {
    copied.value = false;
  }, 2000);
};
</script>
