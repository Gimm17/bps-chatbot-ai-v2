# AI Runtime Logic

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


```text
receive input
  ↓
validate
  ↓
rate limit
  ↓
scope/intent
  ↓
extract indicator/geography/period
  ↓
if ambiguous -> clarification
  ↓
retrieve knowledge
  ↓
if no evidence -> controlled no-answer
  ↓
build prompt
  ↓
call LimitRouter
  ↓
validate output
  ↓
map trusted citation metadata
  ↓
return
```

Model tidak berwenang menentukan URL source.

Intent v0:
- definition
- numeric_statistic
- publication
- metadata_methodology
- bps_service
- navigation
- out_of_scope
