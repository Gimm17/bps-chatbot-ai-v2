# Scope & Intent Routing

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## In scope
BPS, statistik, indikator, sensus/survei, publikasi, metadata, metodologi, layanan statistik, navigasi data.

## Out of scope
General entertainment, unrelated coding, general assistant tasks, dan topik lain yang tidak relevan dengan BPS.

## Dua lapis
1. heuristics murah untuk obvious cases;
2. LLM classifier untuk ambiguous cases.

Output:
```json
{
  "inScope":true,
  "intent":"definition",
  "missing":[]
}
```

Untuk pertanyaan numerik, cek kebutuhan:
- indicator
- geography
- period
