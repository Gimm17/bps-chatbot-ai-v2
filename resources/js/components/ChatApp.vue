<template>
  <div :class="['min-h-screen flex flex-col bg-[#F8FAFC]', { 'h-screen overflow-hidden': isEmbedded }]">
    <!-- Header -->
    <Header :has-messages="messages.length > 0" :is-embedded="isEmbedded" @reset="resetChat" />

    <!-- Main Content Area -->
    <main :class="['flex-1 flex flex-col justify-between max-w-3xl mx-auto w-full overflow-y-auto', isEmbedded ? 'px-2.5 sm:px-3 py-2' : 'px-3 sm:px-4 pt-3 sm:pt-4 pb-2']">
      <!-- 1. Welcome Screen (When no messages) -->
      <WelcomeScreen 
        v-if="messages.length === 0" 
        :is-embedded="isEmbedded"
        @select="handleSendMessage" 
      />

      <!-- 2. Active Chat Conversation -->
      <div v-else :class="['flex-1 space-y-3', isEmbedded ? 'py-1' : 'py-2']" ref="chatAreaRef">
        <MessageItem 
          v-for="(msg, index) in messages" 
          :key="index"
          :message="msg"
          @select-chip="handleSendMessage"
          @retry="handleRetry"
        />

        <!-- Loading Indicator -->
        <div v-if="loading" class="flex items-start gap-2.5 w-full py-1.5">
          <div class="w-7 h-7 rounded-lg bg-[#00ADEF] text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5 animate-pulse">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2"/>
              <circle cx="12" cy="5" r="2"/>
              <path d="M12 7v4"/>
            </svg>
          </div>
          <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 shadow-2xs flex items-center gap-2 text-xs text-slate-600">
            <div class="flex space-x-1">
              <div class="w-1.5 h-1.5 bg-[#00ADEF] rounded-full animate-bounce"></div>
              <div class="w-1.5 h-1.5 bg-[#00ADEF] rounded-full animate-bounce [animation-delay:0.2s]"></div>
              <div class="w-1.5 h-1.5 bg-[#00ADEF] rounded-full animate-bounce [animation-delay:0.4s]"></div>
            </div>
            <span class="font-medium text-slate-700">{{ loadingStatusText }}</span>
          </div>
        </div>
      </div>
    </main>

    <!-- Bottom Sticky Chat Composer -->
    <ChatComposer 
      ref="composerRef"
      :loading="loading" 
      :is-embedded="isEmbedded"
      @send="handleSendMessage" 
    />
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from 'vue';
import Header from './Header.vue';
import WelcomeScreen from './WelcomeScreen.vue';
import MessageItem from './MessageItem.vue';
import ChatComposer from './ChatComposer.vue';

const messages = ref([]);
const loading = ref(false);
const loadingStatusText = ref('Mencari sumber BPS...');
const composerRef = ref(null);
const chatAreaRef = ref(null);

const isEmbedded = ref(false);

onMounted(() => {
  try {
    isEmbedded.value = (window.self !== window.top) || 
      new URLSearchParams(window.location.search).has('embed');
  } catch (e) {
    isEmbedded.value = true;
  }
});

const scrollToBottom = () => {
  nextTick(() => {
    if (isEmbedded.value && chatAreaRef.value) {
      chatAreaRef.value.scrollTop = chatAreaRef.value.scrollHeight;
    } else {
      window.scrollTo({
        top: document.documentElement.scrollHeight,
        behavior: 'smooth'
      });
    }
  });
};

const handleSendMessage = async (userText) => {
  if (!userText || loading.value) return;

  // Add User Message
  messages.value.push({
    role: 'user',
    content: userText
  });

  loading.value = true;
  loadingStatusText.value = 'Mencari sumber data BPS...';
  scrollToBottom();

  const timer = setTimeout(() => {
    if (loading.value) {
      loadingStatusText.value = 'Menyusun jawaban resmi...';
    }
  }, 1500);

  try {
    const response = await fetch('/api/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        message: userText
      })
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
      answer: 'Layanan AI sedang tidak dapat dihubungi. Silakan coba kembali beberapa saat lagi.'
    });
  } finally {
    clearTimeout(timer);
    loading.value = false;
    scrollToBottom();
  }
};

const handleRetry = (msg) => {
  const lastUserMsg = [...messages.value].reverse().find(m => m.role === 'user');
  if (lastUserMsg) {
    handleSendMessage(lastUserMsg.content);
  }
};

const resetChat = () => {
  messages.value = [];
};
</script>
