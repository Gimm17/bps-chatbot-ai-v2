# Internal API Contract

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## POST `/api/chat`

Request:
```json
{
  "conversationId":"optional",
  "message":"Apa itu inflasi?",
  "locale":"id-ID"
}
```

Response:
```json
{
  "requestId":"req_xxx",
  "status":"answered",
  "answer":"...",
  "citations":[
    {
      "sourceId":"SRC-001",
      "title":"...",
      "url":"https://...",
      "snippet":"..."
    }
  ]
}
```

Status:
- `answered`
- `clarification_required`
- `no_evidence`
- `out_of_scope`
- `rate_limited`
- `provider_error`

## GET `/api/models`

Return client-safe subset only:
```json
{
  "models":[{"id":"model-id","label":"model-id"}]
}
```

## POST `/api/feedback`

```json
{
  "messageId":"msg_xxx",
  "rating":"helpful",
  "reason":null
}
```
