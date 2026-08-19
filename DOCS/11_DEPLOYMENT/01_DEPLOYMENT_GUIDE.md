# Deployment Guide — Demo

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Target hosting
Platform harus:
- menjalankan Next.js server routes;
- mendukung environment secrets;
- HTTPS;
- logs;
- tidak expose env server ke client.

## Env
```env
LIMITROUTER_BASE_URL=https://limitrouter.com/v1
LIMITROUTER_API_KEY=<secret>
LIMITROUTER_DEFAULT_MODEL=<verified-model-id>
AI_DEMO_MODE=true
```

## Before deploy
- verify `/models`;
- verify default model;
- set secret via platform;
- never commit `.env.local`;
- enable rate limit;
- run smoke checklist.

## Health
Implement `GET /api/health`.

Jangan expose provider/key/internal hostname di health public.
