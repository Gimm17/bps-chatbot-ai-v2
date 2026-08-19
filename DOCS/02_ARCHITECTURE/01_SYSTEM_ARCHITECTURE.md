# System Architecture — Demo

> **Project:** BPS AI Assistant — Web Demo  
> **Target:** Demo publik tanpa login  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** API key server-side only.


```mermaid
flowchart TB
    U[Anonymous User] --> UI[Next.js UI]
    UI --> AC[/api/chat]
    UI --> AM[/api/models]

    AC --> RL[Rate Limit]
    RL --> VG[Validation + Scope Guard]
    VG --> DR[Demo Knowledge Retriever]
    DR --> PB[Prompt Builder]
    PB --> LG[LimitRouter Adapter]
    LG --> LRC[POST /v1/chat/completions]

    AM --> LRM[GET /v1/models]

    LRC --> RV[Response Validator]
    RV --> AC

    DR --> KD[(Local Demo Knowledge)]
    AC --> LOG[Minimal Telemetry]
```

## Browser
Tidak memiliki API key dan tidak memanggil LimitRouter langsung.

## Server
Menangani secret, scope, retrieval, prompt, provider adapter, validation, error normalization.

## Provider
LimitRouter diperlakukan sebagai dependency yang dapat diganti.
