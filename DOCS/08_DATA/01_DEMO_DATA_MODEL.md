# Demo Data Model

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Untuk demo, database permanen opsional.

```text
Conversation
- id
- createdAt

Message
- id
- conversationId
- role
- content
- status
- citations[]

Source
- id
- title
- url
- category
- content

Feedback
- messageId
- rating
- reason
```

Production:
- conversations
- messages
- message_citations
- feedback
- sources
- source_versions
- chunks
- embeddings
- evaluation_runs
