# RAG Demo -> Production

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Demo v0
```text
local markdown knowledge
  ↓
parse
  ↓
lexical scoring
  ↓
top passages
  ↓
LLM context
  ↓
answer + source
```

Kelebihan: cepat, murah, transparan.  
Keterbatasan: semantic retrieval lemah.

## Production
```text
query
  ↓
entity + metadata normalization
  ↓
BM25 + vector
  ↓
fusion
  ↓
reranker
  ↓
top evidence
  ↓
LLM
```

Tambahkan:
- PostgreSQL
- embeddings
- pgvector/vector DB
- source versioning
- ingestion
- structured BPS connector
- evaluation
