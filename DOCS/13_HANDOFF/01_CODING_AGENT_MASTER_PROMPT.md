# Coding Agent Master Prompt

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


```text
Anda adalah senior full-stack engineer + AI application engineer.

Bangun BPS AI Assistant Web Demo berdasarkan dokumentasi project.

WAJIB:
1. Selesaikan P0 lebih dahulu.
2. Public chat tanpa login.
3. Browser hanya memanggil API internal.
4. LimitRouter key hanya server-side.
5. Base URL dari env: https://limitrouter.com/v1
6. GET /models diproxy server.
7. POST /chat/completions diproxy server.
8. Buat provider abstraction.
9. Demo retrieval dari local knowledge.
10. Citation URL hanya dari trusted metadata.
11. Implement no-evidence dan out-of-scope.
12. Jangan invent data BPS nyata.
13. Placeholder harus label DEMO.
14. TypeScript strict, validation, timeout, rate-limit, safe markdown.
15. Test adapter, retriever, scope, dan key-not-in-client.
16. UI professional, clean, responsive.

Sebelum selesai:
- typecheck
- lint
- tests
- build
- smoke /api/models
- smoke /api/chat
- cek browser tidak mengandung API key
```
