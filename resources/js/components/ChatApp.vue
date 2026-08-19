<template>
  <div class="min-h-screen flex flex-col bg-[#F8FAFC]">
    <!-- Header -->
    <Header :has-messages="messages.length > 0" @reset="resetChat" />

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col justify-between max-w-4xl mx-auto w-full px-4 pt-4 pb-2">
      <!-- 1. Welcome Screen (When no messages) -->
      <WelcomeScreen 
        v-if="messages.length === 0" 
        @select="handleSendMessage" 
      />

      <!-- 2. Active Chat Conversation -->
      <div v-else class="flex-1 space-y-4 py-2" ref="chatAreaRef">
        <MessageItem 
          v-for="(msg, index) in messages" 
          :key="index"
          :message="msg"
          @select-chip="handleSendMessage"
          @retry="handleRetry"
        />

        <!-- Loading Indicator -->
        <div v-if="loading" class="flex items-start gap-3 w-full py-2">
          <div class="w-8 h-8 rounded-xl bg-[#00ADEF] text-white flex items-center justify-center shrink-0 shadow-2xs mt-1 animate-pulse">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2"/>
              <circle cx="12" cy="5" r="2"/>
              <path d="M12 7v4"/>
            </svg>
          </div>
          <div class="bg-white border border-slate-200 rounded-2xl px-4 py-3 shadow-2xs flex items-center gap-2.5 text-xs text-slate-600">
            <div class="flex space-x-1">
              <div class="w-2 h-2 bg-[#00ADEF] rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-[#00ADEF] rounded-full animate-bounce [animation-delay:0.2s]"></div>
              <div class="w-2 h-2 bg-[#00ADEF] rounded-full animate-bounce [animation-delay:0.4s]"></div>
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
      @send="handleSendMessage" 
    />

    <!-- Floating Embed Widget Demo -->
    <WidgetToggle />
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import Header from './Header.vue';
import WelcomeScreen from './WelcomeScreen.vue';
import MessageItem from './MessageItem.vue';
import ChatComposer from './ChatComposer.vue';
import WidgetToggle from './WidgetToggle.vue';

const messages = ref([]);
const loading = ref(false);
const loadingStatusText = ref('Mencari sumber BPS...');
const composerRef = ref(null);
const chatAreaRef = ref(null);

const scrollToBottom = () => {
  nextTick(() => {
    window.scrollTo({
      top: document.documentElement.scrollHeight,
      behavior: 'smooth'
    });
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

  // Switch loading text after 1.5s
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
