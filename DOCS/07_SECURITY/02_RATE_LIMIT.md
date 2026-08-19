# Rate Limit Demo

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Baseline demo, bukan production SLA:
- contoh 10 request/menit/IP;
- max 2 concurrent request;
- max input length;
- max conversation context.

HTTP `429`:
```json
{
  "error":{
    "code":"RATE_LIMITED",
    "message":"Terlalu banyak permintaan. Silakan coba beberapa saat lagi."
  }
}
```

Production tidak boleh bergantung pada IP saja.
