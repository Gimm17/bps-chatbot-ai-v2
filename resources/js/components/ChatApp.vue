<template>
  <div class="h-[100dvh] max-h-[100dvh] w-full flex flex-col bg-[#FAF8FF] overflow-hidden select-text">
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
        <div v-if="loading" class="flex items-start gap-2.5 sm:gap-3.5 w-full py-1.5 animate-in fade-in">
          <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#0058A9] text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5 animate-pulse">
            <span class="material-symbols-outlined text-[18px] sm:text-[20px]" style="font-variation-settings: 'FILL' 1;">smart_toy</span>
          </div>
          <div class="bg-white border border-[#DAE2FD] rounded-2xl px-4 py-2.5 shadow-2xs flex items-center gap-2.5 text-xs text-[#424751]">
            <div class="flex space-x-1">
              <div class="w-1.5 h-1.5 bg-[#0058A9] rounded-full animate-bounce"></div>
              <div class="w-1.5 h-1.5 bg-[#0058A9] rounded-full animate-bounce [animation-delay:0.2s]"></div>
              <div class="w-1.5 h-1.5 bg-[#0058A9] rounded-full animate-bounce [animation-delay:0.4s]"></div>
            </div>
            <span class="font-semibold text-[#131B2E]">{{ loadingStatusText }}</span>
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

  // Add User Message to local state
  messages.value.push({
    role: 'user',
    content: userText
  });

  loading.value = true;
  loadingStatusText.value = 'Mencari data resmi BPS...';
  scrollToBottom();

  const timer = setTimeout(() => {
    if (loading.value) {
      loadingStatusText.value = 'Menyusun jawaban terverifikasi...';
    }
  }, 1500);

  try {
    // Build conversation history (max 10 turns)
    const allMessages = messages.value.slice(0, -1);
    const history = allMessages
      .filter(m => m.role === 'user' || m.role === 'assistant')
      .map(m => ({
        role: m.role,
        content: m.role === 'assistant'
          ? (m.answer || m.clarificationQuestion || m.content || '')
          : (m.content || '')
      }))
      .filter(m => m.content.trim().length > 0)
      .slice(-10);

    const response = await fetch('/api/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        message: userText,
        history: history
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
