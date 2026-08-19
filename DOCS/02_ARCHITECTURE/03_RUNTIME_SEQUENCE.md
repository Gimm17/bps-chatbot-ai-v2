# Runtime Sequence

> **Project:** BPS AI Assistant — Web Demo  
> **Target:** Demo publik tanpa login  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** API key server-side only.


```mermaid
sequenceDiagram
    participant U as User
    participant W as Web UI
    participant A as /api/chat
    participant S as Scope Guard
    participant R as Retriever
    participant L as LimitRouter
    participant V as Validator

    U->>W: Pertanyaan
    W->>A: POST /api/chat
    A->>S: Validate + classify
    alt out-of-scope
        S-->>A: reject
        A-->>W: out_of_scope
    else in-scope
        S->>R: retrieve
        R-->>A: evidence + sources
        A->>L: messages + evidence
        L-->>A: completion
        A->>V: validate
        V-->>A: normalized answer
        A-->>W: answer + citations
    end
```
