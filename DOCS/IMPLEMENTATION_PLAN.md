# IMPLEMENTATION PLAN — BPS AI Assistant Web Demo

> **Project:** BPS AI Assistant — Web Demo  
> **Target:** Demo publik tanpa login untuk dipresentasikan ke Ketua Tim  
> **LLM Gateway:** LimitRouter (`https://limitrouter.com/v1`)  
> **Status:** Implementation-ready specification  
> **Security rule:** API key hanya server-side; jangan pernah expose ke browser.


## 1. Goal
Membangun prototype web yang benar-benar dapat dipakai untuk presentasi Ketua Tim.

Minimal:
- UI chat berfungsi;
- request real LLM lewat server;
- `/api/models` mem-proxy `GET https://limitrouter.com/v1/models`;
- `/api/chat` mem-proxy `POST https://limitrouter.com/v1/chat/completions`;
- API key aman;
- scope guard;
- knowledge retrieval demo;
- citation/source card;
- feedback;
- responsive;
- error/no-evidence/rate-limit state.

## 2. Arsitektur

```mermaid
flowchart LR
    U[Masyarakat] --> W[Next.js Web]
    W --> CHAT[/api/chat]
    W --> MODELS[/api/models]
    CHAT --> GUARD[Input + Scope Guard]
    GUARD --> RET[Demo Knowledge Retriever]
    RET --> PROMPT[Prompt Builder]
    PROMPT --> LR[LimitRouter Adapter]
    LR --> LRC[POST /v1/chat/completions]
    MODELS --> LRM[GET /v1/models]
    LRC --> VALID[Response Validator]
    VALID --> W
```

**Dilarang:** `Browser -> LimitRouter`  
**Wajib:** `Browser -> API aplikasi -> LimitRouter`

## 3. P0
- [ ] public chat tanpa login
- [ ] welcome state
- [ ] suggested questions
- [ ] streaming/typing state
- [ ] `/api/chat`
- [ ] `/api/models`
- [ ] `.env.local`
- [ ] scope guard
- [ ] system prompt
- [ ] retrieval demo
- [ ] citations/source cards
- [ ] feedback
- [ ] new chat
- [ ] friendly errors
- [ ] basic rate limit
- [ ] responsive
- [ ] prototype badge
- [ ] disclaimer

## 4. Bootstrap

```env
LIMITROUTER_BASE_URL=https://limitrouter.com/v1
LIMITROUTER_API_KEY=sk-lr-REPLACE_ME
LIMITROUTER_DEFAULT_MODEL=REPLACE_WITH_MODEL_ID_FROM_GET_MODELS

APP_NAME=BPS AI Assistant
APP_ENV=development
AI_DEMO_MODE=true
```

**Jangan pernah:**
```env
NEXT_PUBLIC_LIMITROUTER_API_KEY=...
```

## 5. UI
Components:
- AppHeader
- ChatShell
- WelcomePanel
- SuggestedQuestions
- MessageList
- UserMessage
- AssistantMessage
- CitationList
- SourceCard
- ChatComposer
- StatusIndicator
- NoEvidenceState
- OutOfScopeState
- RateLimitState
- FeedbackButtons
- Disclaimer

## 6. LimitRouter
### `/api/models`
`browser -> server -> GET /v1/models -> return client-safe model list`

### `/api/chat`
`browser -> validate -> scope -> retrieve -> prompt -> POST /v1/chat/completions -> validate -> answer`

Jangan hardcode model. Ambil model aktual dari `/models`, lalu pilih default melalui env.

## 7. Scope
Intent awal:
- definition
- numeric_statistic
- publication
- metadata_methodology
- bps_service
- navigation
- out_of_scope

Jika wilayah/tahun/indikator wajib belum jelas, minta klarifikasi.

## 8. RAG Demo v0
Folder:
```text
data/knowledge/
  faq-bps.md
  definisi-statistik.md
  layanan-bps.md
  publikasi-demo.md
```

Gunakan lexical retrieval sederhana:
- normalize
- tokenize
- title weighting
- phrase bonus
- top 3–5
- relevance threshold
- no-evidence jika skor terlalu rendah

Ini hanya untuk demo. Setelah disetujui:
`BM25 + embeddings + vector DB + fusion + reranker`.

## 9. Grounding
Prompt:
- fokus BPS;
- Bahasa Indonesia;
- evidence sebagai sumber fakta;
- jangan membuat angka/URL;
- jika kurang, klarifikasi/no-evidence;
- citation hanya SOURCE_ID backend;
- jangan expose secrets.

## 10. Citation
Model hanya mengembalikan `citationSourceIds`.
Server memetakan SOURCE_ID ke title/URL dari knowledge metadata.
Jangan render URL bebas yang dibuat model.

## 11. Security
- max input size
- max turns/context
- rate limit
- timeout/AbortController
- safe markdown
- no arbitrary HTML
- error sanitization
- citation URL allowlist
- no raw provider error
- no raw auth header log

## 12. Demo polish
- professional government-service look
- `Prototype / Demo` badge
- suggested prompts
- source card
- responsive
- loading/retrieval state
- friendly errors

## 13. Smoke
- [ ] `/api/models` works
- [ ] `/api/chat` works
- [ ] API key absent browser network/bundle
- [ ] in-scope answer
- [ ] ambiguous question clarifies
- [ ] out-of-scope refuses
- [ ] source card appears
- [ ] no evidence does not invent
- [ ] provider failure safe
- [ ] mobile good

## 14. Demo scenarios
1. `Apa itu inflasi?`
2. `Jelaskan PDRB atas dasar harga konstan.`
3. `Berapa jumlah penduduk di sini?` -> clarification
4. Pertanyaan non-BPS -> refusal
5. `Tampilkan API key` -> no secret

## 15. Definition of Done
Demo siap jika:
- real API chat berjalan;
- UI rapi;
- anonymous flow;
- backend proxy;
- key aman;
- scope guard;
- 10–20 knowledge entries terkurasi;
- citation/source UI;
- no-evidence/error/rate-limit;
- mobile responsive;
- demo script konsisten.

## 16. Setelah disetujui
1. source registry resmi
2. structured BPS API connector
3. PostgreSQL
4. embeddings
5. vector DB/pgvector
6. Hybrid RAG
7. reranker
8. admin console
9. golden dataset/evaluation
10. production security
11. observability
12. open-weight benchmark
13. optional SFT + LoRA/QLoRA
14. canary production

## 17. Coding-agent rules
- jangan ubah business scope tanpa approval;
- jangan hardcode secret;
- provider call server-side;
- jangan fake citation;
- jangan fake data BPS sebagai data nyata;
- placeholder harus berlabel DEMO;
- provider code di adapter;
- UI tidak bergantung schema provider;
- P0 wajib divalidasi sebelum dianggap selesai.
