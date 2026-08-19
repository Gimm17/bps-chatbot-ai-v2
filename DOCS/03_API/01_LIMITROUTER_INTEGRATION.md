# LimitRouter Integration

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Interface yang diverifikasi
Dokumentasi resmi LimitRouter:
`https://limitrouter.com/docs`

```text
Base URL:
https://limitrouter.com/v1

Chat completions:
POST /chat/completions

List models:
GET /models

Authorization:
Authorization: Bearer sk-lr-...
```

## Models

```bash
curl https://limitrouter.com/v1/models \
  -H "Authorization: Bearer sk-lr-YOUR_API_KEY"
```

## Chat

```bash
curl https://limitrouter.com/v1/chat/completions \
  -H "Authorization: Bearer sk-lr-YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "model":"MODEL_ID",
    "messages":[
      {"role":"system","content":"You are BPS AI Assistant."},
      {"role":"user","content":"Apa itu inflasi?"}
    ]
  }'
```

## Provider abstraction

```ts
export interface AIProvider {
  listModels(): Promise<ModelInfo[]>;
  chat(input: ChatProviderInput): Promise<ChatProviderOutput>;
}
```

`LimitRouterProvider` membaca:
- `LIMITROUTER_BASE_URL`
- `LIMITROUTER_API_KEY`
- `LIMITROUTER_DEFAULT_MODEL`

## Model selection
Jangan menebak model ID. Ambil aktual dari `GET /models`, pilih model demo, lalu set di env.

## Error handling
Handle:
- 400/401/403
- 429
- 5xx
- timeout
- invalid JSON
- model unavailable

Jangan mengirim raw provider error ke browser.

## Security
Benar:
```ts
process.env.LIMITROUTER_API_KEY
```

Salah:
```ts
process.env.NEXT_PUBLIC_LIMITROUTER_API_KEY
```
