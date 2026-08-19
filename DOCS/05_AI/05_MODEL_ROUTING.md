# Model Routing

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Demo
- ambil daftar model dari `/api/models`;
- pilih model yang benar-benar tersedia;
- simpan default di `LIMITROUTER_DEFAULT_MODEL`;
- model selector hanya developer mode bila perlu.

## Future
```text
scope/easy -> small model
complex grounded answer -> stronger model
provider failure -> approved fallback
```

Pemilihan produksi harus berdasarkan benchmark BPS.
