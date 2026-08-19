# Session & Retention

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Demo
- chat state dapat disimpan in-memory/browser session;
- tidak membuat profil user;
- minimal server logging.

## Production harus diputuskan
- apakah chat disimpan;
- retention;
- IP retention;
- feedback retention;
- privacy notice;
- siapa boleh membaca raw conversations.

Jangan membuat retention policy diam-diam di kode.
