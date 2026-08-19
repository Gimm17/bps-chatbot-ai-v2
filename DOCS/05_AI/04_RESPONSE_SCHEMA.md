# AI Response Schema

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Preferred internal output:
```json
{
  "status":"answered",
  "answer":"...",
  "citationSourceIds":["SRC-001"],
  "clarificationQuestion":null
}
```

Server:
`citationSourceIds -> trusted retrieval metadata -> source cards`.

Model-generated URL tidak boleh langsung dirender.
