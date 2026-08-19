# Recommended Folder Structure

> **Project:** BPS AI Assistant — Web Demo  
> **Target:** Demo publik tanpa login  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** API key server-side only.


```text
bps-ai-demo/
├── app/
│   ├── api/
│   │   ├── chat/route.ts
│   │   ├── models/route.ts
│   │   ├── feedback/route.ts
│   │   └── health/route.ts
│   ├── page.tsx
│   ├── layout.tsx
│   └── globals.css
├── components/
│   ├── chat/
│   │   ├── chat-shell.tsx
│   │   ├── message-list.tsx
│   │   ├── assistant-message.tsx
│   │   ├── user-message.tsx
│   │   ├── chat-composer.tsx
│   │   ├── source-card.tsx
│   │   └── feedback.tsx
│   └── ui/
├── lib/
│   ├── ai/
│   │   ├── limitrouter.ts
│   │   ├── gateway.ts
│   │   ├── prompt.ts
│   │   ├── scope.ts
│   │   └── response-validator.ts
│   ├── rag/
│   │   ├── loader.ts
│   │   ├── retriever.ts
│   │   └── types.ts
│   ├── security/
│   │   ├── rate-limit.ts
│   │   └── input.ts
│   └── env.ts
├── data/knowledge/
├── types/
├── tests/
└── .env.example
```
