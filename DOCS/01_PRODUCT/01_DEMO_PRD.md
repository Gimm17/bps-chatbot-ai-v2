# PRD — BPS AI Assistant Web Demo

> **Project:** BPS AI Assistant — Web Demo  
> **Target:** Demo publik tanpa login  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** API key server-side only.


## Problem
Banyak pertanyaan masyarakat mengenai BPS bersifat berulang dan dapat dijawab dari knowledge resmi. Respons manual membuat waktu tunggu lebih panjang dan membebani petugas.

## Vision
Asisten publik tanpa login untuk membantu memahami istilah statistik, menemukan publikasi, menavigasi data, dan memahami layanan BPS.

## P0 Features
1. Anonymous chat.
2. Suggested questions.
3. Scope guard.
4. LimitRouter LLM integration.
5. Model abstraction.
6. Demo knowledge retrieval.
7. Citations/source cards.
8. Clarification.
9. No-evidence.
10. Feedback.
11. Responsive.
12. Rate-limit/error states.

## Non-goals Demo
- production crawler;
- full vector RAG;
- fine-tuning;
- private data;
- user account;
- autonomous agent;
- official production SLA.

## Demo Success
Ketua Tim dapat melihat alur:
`Pertanyaan -> Search Knowledge -> LLM -> Validasi -> Jawaban + Sumber`.
