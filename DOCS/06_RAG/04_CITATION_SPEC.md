# Citation Specification

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


```ts
type Citation = {
  sourceId:string;
  title:string;
  url?:string;
  snippet?:string;
};
```

Rules:
- source ID dari backend;
- URL dari registry;
- URL bukan output bebas LLM;
- `noopener noreferrer` untuk external links;
- source URL belum terverifikasi -> jangan buat link palsu.
