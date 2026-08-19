# System Architecture — BPS AI Assistant v2

> **Project:** BPS AI Assistant v2  
> **Framework:** Laravel 11 (PHP 8.2+) REST API + Vue 3 SPA + Vite  
> **Integrasi:** BPS WebAPI (549 Wilayah) + PPID Portal + OpenRouter Multi-Key Resilience Router  

## 🏗️ Architecture Diagram

```mermaid
flowchart TB
    subgraph Presentation Layer
        U1[Web Client / Mobile Browser] --> VUE[Vue 3 SPA]
        U2[PWA Standalone App] --> SW[Service Worker Cache]
        SW --> VUE
        U3[Host Website External] --> EMB[Cloud Bubble Embed Widget]
        EMB --> VUE
    end

    subgraph Backend Application (Laravel 11)
        VUE --> API[/api/chat Controller]
        API --> RL[Rate Limiting & Telemetry]
        RL --> SG[ScopeGuard & Security Filter]
        
        SG -->|Intent: Live Stat/Publication| BA[BpsAgent & Domain Resolver]
        SG -->|Intent: Pejabat/PPID/Definisi| KR[KnowledgeRetriever]
        
        BA --> WEBAPI[(Live BPS WebAPI 549 Satker)]
        KR --> PPID[(PPID & SIRuSa Local Corpus)]
        
        BA --> PB[PromptBuilder]
        KR --> PB
        
        PB --> LR[LimitRouterProvider Multi-Key Pool]
        LR --> LLM[OpenRouter / Gemini / Qwen API]
        
        LLM --> RP[Response Parser & Citation Binder]
        RP --> API
    end
```

## 🔒 Presentation Layer
- **Vue 3 SPA + Vite:** Single-Page Application ultra-ringan dengan state management Composition API.
- **PWA (Progressive Web App):** Service Worker caching untuk kecepatan instan dan instalasi homescreen.
- **Embed Widget (`embed.js`):** Script mandiri untuk menyematkan chatbot ke portal eksternal via container iframe terisolasi.
- **Security:** API Key sepenuhnya tersimpan aman di server-side (tidak pernah bocor ke browser client).

## ⚙️ Backend Application Layer (Laravel 11)
- **ScopeGuard:** Melindungi sistem dari prompt injection, mendeteksi sapaan instan, dan memfilter topik di luar BPS.
- **Domain Resolver:** Mengarahkan query ke Satker BPS yang tepat (Pusat `0000`, Provinsi `7200`, Kab/Kota `7271`).
- **PromptBuilder & Citation Binder:** Mengikat bukti resmi BPS ke dalam respons AI dengan tautan verifikasi transparan.
