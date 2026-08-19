# Test Plan

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Unit
- env validator
- LimitRouter adapter
- input validation
- scope helper
- retriever
- citation mapper

## Integration
- `/api/models`
- `/api/chat`
- provider timeout
- provider 429
- provider 5xx
- malformed AI JSON

## E2E
1. open page
2. send in-scope question
3. see loading
4. see answer
5. see source
6. feedback
7. new chat

## Security
- key absent browser
- prompt extraction
- XSS-like output
- long input
- rapid requests

## Devices
Desktop + mobile.
