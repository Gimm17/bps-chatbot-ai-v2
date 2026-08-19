# Security Baseline

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Mandatory:
- secret server-side;
- `.env.local` ignored;
- request validation;
- payload limits;
- rate limit;
- outbound timeout;
- safe markdown;
- no arbitrary HTML;
- URL validation;
- error sanitization;
- no raw provider body logging;
- no secrets in prompt.

Browser DevTools:
- tidak boleh ada request ke `limitrouter.com`;
- browser hanya memanggil aplikasi sendiri.
