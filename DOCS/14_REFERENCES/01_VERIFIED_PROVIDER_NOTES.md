# Verified Provider Notes — LimitRouter

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Verified date
2026-08-18

## Official docs
`https://limitrouter.com/docs`

## Confirmed
- Base URL: `https://limitrouter.com/v1`
- Chat completions: `POST /chat/completions`
- List models: `GET /models`
- Auth: `Authorization: Bearer sk-lr-...`

## Not assumed
- specific model availability
- pricing
- provider-side rate limits
- embeddings endpoint
- structured output guarantees
- streaming behavior for every model

Capability tambahan harus diverifikasi sebelum dijadikan requirement.
