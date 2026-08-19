# Demo Knowledge Format

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


Format:
```yaml
id: SRC-DEMO-001
title: Definisi Inflasi
category: definition
source_url:
source_status: DEMO_NOT_VERIFIED
```

Content mengikuti metadata.

## Rule
- jika source belum diverifikasi, label DEMO;
- jangan membuat URL resmi palsu;
- jangan membuat angka BPS aktual yang belum dicek.

Minimum seed 10–20 entry:
inflasi, deflasi, PDRB, sensus, survei, publikasi, metadata, layanan, cara mencari data, data wilayah.
