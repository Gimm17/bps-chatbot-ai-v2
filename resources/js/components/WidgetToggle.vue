<template>
  <div class="fixed bottom-5 right-5 z-50 flex flex-col items-end">
    <!-- Floating Chat Window (when open) -->
    <div 
      v-if="isOpen" 
      class="mb-3 w-[92vw] sm:w-[400px] h-[580px] max-h-[85vh] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-5 duration-200"
    >
      <!-- Widget Header -->
      <div class="bg-gradient-to-r from-[#0077A6] to-[#00ADEF] text-white p-3.5 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
            <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2"/>
              <circle cx="12" cy="5" r="2"/>
              <path d="M12 7v4"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-bold leading-tight">BPS AI Assistant</h3>
            <span class="text-[10px] text-white/80">Asisten Statistik Publik</span>
          </div>
        </div>

        <button 
          @click="isOpen = false" 
          class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
          title="Tutup widget"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Widget Chat Body -->
      <div ref="widgetScrollArea" class="flex-1 overflow-y-auto p-3.5 space-y-3 bg-[#F8FAFC]">
        <!-- Welcome Screen in Widget -->
        <div v-if="messages.length === 0" class="text-center py-4 px-2">
          <div class="w-12 h-12 bg-blue-50 text-[#0077A6] rounded-xl flex items-center justify-center mx-auto mb-3 border border-blue-100">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2"/>
              <circle cx="12" cy="5" r="2"/>
              <path d="M12 7v4"/>
            </svg>
          </div>
          <h4 class="text-sm font-bold text-slate-900 mb-1">Halo, ada yang bisa saya bantu?</h4>
          <p class="text-xs text-slate-500 mb-4">Tanyakan data statistik, publikasi, atau layanan BPS.</p>
          
          <div class="space-y-1.5 text-left">
            <button 
              v-for="q in quickWidgetPrompts" 
              :key="q"
              @click="sendMessage(q)"
              class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium text-slate-700 hover:border-[#00ADEF] hover:bg-blue-50/50 transition-colors text-left flex items-center justify-between"
            >
              <span>{{ q }}</span>
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>

        <!-- Messages List in Widget -->
        <template v-else>
          <MessageItem 
            v-for="(msg, idx) in messages" 
            :key="idx"
            :message="msg"
            @select-chip="sendMessage"
            @retry="retryMessage"
          />
        </template>

        <!-- Loading Indicator -->
        <div v-if="loading" class="flex items-center gap-2 p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-600 shadow-2xs">
          <div class="w-2 h-2 rounded-full bg-[#00ADEF] animate-ping"></div>
          <span class="font-medium">{{ loadingStatusText }}</span>
        </div>
      </div>

      <!-- Widget Composer -->
      <div class="p-2.5 bg-white border-t border-slate-200">
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus-within:border-[#00ADEF] focus-within:bg-white transition-colors">
          <input 
            v-model="widgetInput" 
            @keydown.enter.prevent="submitWidget"
            :disabled="loading"
            type="text" 
            placeholder="Tanyakan sesuatu..."
            class="flex-1 bg-transparent border-0 text-xs text-slate-900 focus:ring-0 focus:outline-hidden py-1"
          />
          <button 
            @click="submitWidget"
            :disabled="!widgetInput.trim() || loading"
            class="w-7 h-7 rounded-lg bg-[#0077A6] hover:bg-[#005F85] disabled:bg-slate-200 disabled:text-slate-400 text-white flex items-center justify-center transition-colors cursor-pointer shrink-0"
          >
            <svg class="w-3.5 h-3.5 transform rotate-45 -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Toggle Button (Pill or Circular Icon) -->
    <button 
      @click="isOpen = !isOpen"
      class="group flex items-center gap-2.5 bg-gradient-to-r from-[#0077A6] to-[#00ADEF] hover:from-[#005F85] hover:to-[#0095CC] text-white font-semibold text-xs sm:text-sm px-4 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 cursor-pointer"
    >
      <div class="w-5 h-5 flex items-center justify-center">
        <svg v-if="!isOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>
      <span>{{ isOpen ? 'Tutup Chat' : 'Tanya BPS' }}</span>
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import MessageItem from './MessageItem.vue';

const isOpen = ref(false);
const messages = ref([]);
const loading = ref(false);
const loadingStatusText = ref('Mencari sumber BPS...');
const widgetInput = ref('');
const widgetScrollArea = ref(null);

const quickWidgetPrompts = [
  'Apa itu inflasi?',
  'Bagaimana mencari publikasi BPS?',
  'Jelaskan konsep PDRB',
];

const scrollToBottom = () => {
  nextTick(() => {
    if (widgetScrollArea.value) {
      widgetScrollArea.value.scrollTop = widgetScrollArea.value.scrollHeight;
    }
  });
};

const sendMessage = async (text) => {
  if (!text || loading.value) return;

  messages.value.push({
    role: 'user',
    content: text
  });

  loading.value = true;
  loadingStatusText.value = 'Mencari sumber data BPS...';
  scrollToBottom();

  try {
    const response = await fetch('/api/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ message: text }),
    });

    const data = await response.json();

    messages.value.push({
      role: 'assistant',
      status: data.status,
      content: data.answer,
      answer: data.answer,
      clarificationQuestion: data.clarificationQuestion,
      citations: data.citations || [],
      requestId: data.requestId
    });
  } catch (error) {
    messages.value.push({
      role: 'assistant',
      status: 'provider_error',
      answer: 'Layanan AI sedang tidak dapat dihubungi. Silakan coba kembali.'
    });
  } finally {
    loading.value = false;
    scrollToBottom();
  }
};

const submitWidget = () => {
  const q = widgetInput.value.trim();
  if (q) {
    widgetInput.value = '';
    sendMessage(q);
  }
};

const retryMessage = (msg) => {
  const lastUserMsg = [...messages.value].reverse().find(m => m.role === 'user');
  if (lastUserMsg) {
    sendMessage(lastUserMsg.content);
  }
};
</script>
