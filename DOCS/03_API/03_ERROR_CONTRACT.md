# Error Contract

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Public error:
```json
{
  "error":{
    "code":"AI_PROVIDER_UNAVAILABLE",
    "message":"Layanan AI sedang tidak tersedia. Silakan coba kembali.",
    "requestId":"req_xxx"
  }
}
```

| Code | HTTP |
|---|---:|
| INVALID_INPUT | 400 |
| RATE_LIMITED | 429 |
| MODEL_NOT_CONFIGURED | 503 |
| AI_PROVIDER_UNAVAILABLE | 503 |
| AI_PROVIDER_TIMEOUT | 504 |
| INTERNAL_ERROR | 500 |

Stack trace/raw provider response tidak dikirim ke client.
