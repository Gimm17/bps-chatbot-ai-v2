<template>
  <div class="h-[100dvh] max-h-[100dvh] w-full flex flex-col bg-[#F8FAFC] overflow-hidden select-text">
    <!-- 1. Top Fixed Header (Shrink-0) -->
    <Header 
      class="shrink-0" 
      :has-messages="messages.length > 0" 
      :is-embedded="isEmbedded" 
      @reset="resetChat" 
    />

    <!-- 2. Middle Scrollable Conversation Area (Flex-1) -->
    <main 
      ref="chatAreaRef"
      class="flex-1 w-full max-w-3xl mx-auto overflow-y-auto overscroll-contain px-3 sm:px-4 py-2 sm:py-3 flex flex-col justify-start"
    >
      <!-- Welcome Screen (When no messages) -->
      <WelcomeScreen 
        v-if="messages.length === 0" 
        :is-embedded="isEmbedded"
        @select="handleSendMessage" 
      />

      <!-- Active Chat Messages List -->
      <div v-else class="space-y-3 w-full py-1">
        <MessageItem 
          v-for="(msg, index) in messages" 
          :key="index"
          :message="msg"
          @select-chip="handleSendMessage"
          @retry="handleRetry"
        />

        <!-- Loading Indicator -->
        <div v-if="loading" class="flex items-start gap-2.5 w-full py-1.5">
          <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-[#00ADEF] to-[#0077A6] text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5 animate-pulse">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2"/>
              <circle cx="12" cy="5" r="2"/>
              <path d="M12 7v4"/>
            </svg>
          </div>
          <div class="bg-white border border-slate-200 rounded-2xl px-3.5 py-2.5 shadow-2xs flex items-center gap-2 text-xs text-slate-600">
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

    <!-- 3. Bottom Sticky Chat Composer (Shrink-0) -->
    <ChatComposer 
      ref="composerRef"
      class="shrink-0"
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
    if (chatAreaRef.value) {
      chatAreaRef.value.scrollTo({
        top: chatAreaRef.value.scrollHeight,
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
