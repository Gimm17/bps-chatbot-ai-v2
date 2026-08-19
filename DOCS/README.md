# BPS AI Assistant — Web Demo Implementation Pack

> **Project:** BPS AI Assistant — Web Demo  
> **Target:** Demo publik tanpa login untuk dipresentasikan ke Ketua Tim  
> **LLM Gateway:** LimitRouter (`https://limitrouter.com/v1`)  
> **Status:** Implementation-ready specification  
> **Security rule:** API key hanya server-side; jangan pernah expose ke browser.


Paket ini dibuat agar prototype web bisa langsung dibangun dan didemokan.

## Demo harus memperlihatkan
1. Chat publik tanpa login.
2. Backend memanggil LimitRouter, bukan browser.
3. Scope pertanyaan BPS.
4. Jawaban dari LLM.
5. Knowledge retrieval demo.
6. Source/citation UI.
7. Clarification/no-evidence/out-of-scope.
8. Error/rate-limit.
9. Provider/model dapat diganti lewat gateway.
10. Responsive desktop/mobile.

## Urutan baca
1. `IMPLEMENTATION_PLAN.md`
2. `01_PRODUCT/01_DEMO_PRD.md`
3. `02_ARCHITECTURE/01_SYSTEM_ARCHITECTURE.md`
4. `03_API/01_LIMITROUTER_INTEGRATION.md`
5. `04_FRONTEND/01_UI_UX_SPEC.md`
6. `05_AI/01_AI_RUNTIME_LOGIC.md`
7. `06_RAG/01_RAG_DEMO_TO_PRODUCTION.md`
8. `07_SECURITY/01_SECURITY_BASELINE.md`
9. `09_TESTING/01_TEST_PLAN.md`
10. `10_DEMO/01_KETUA_TIM_DEMO_SCRIPT.md`

## Stack baseline
- Next.js + TypeScript
- Tailwind CSS
- shadcn/ui atau setara
- Server-side Route Handlers
- LimitRouter API
- Local knowledge retrieval untuk demo
- PostgreSQL + Hybrid RAG untuk tahap produksi

Gunakan versi stable saat bootstrap; paket ini tidak mengunci nomor versi framework.
