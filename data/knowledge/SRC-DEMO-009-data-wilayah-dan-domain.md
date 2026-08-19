---
id: SRC-DEMO-009
title: Kode Wilayah dan Struktur Domain Data BPS
category: geographic
source_url: https://sirusa.bps.go.id/sirusa/index.php/dasar/mfd
source_status: OFFICIAL_BPS
---
# Kode Wilayah dan Struktur Domain Data BPS

## Master File Desa (MFD) dan Kode Wilayah BPS
BPS menggunakan sistem pengkodean wilayah administrasi pemerintahan yang terstandarisasi di seluruh Indonesia:
- **Kode 2 digit:** Provinsi (contoh: `11` Aceh, `31` DKI Jakarta, `32` Jawa Barat, `35` Jawa Timur, `51` Bali, dll). Kode `00` atau `0000` merepresentasikan Nasional / Seluruh Indonesia.
- **Kode 4 digit:** Kabupaten/Kota (contoh: `3201` Kab. Bogor, `3273` Kota Bandung, `3171` Kota Jakarta Selatan).
- **Kode 7 digit:** Kecamatan.
- **Kode 10 digit:** Desa/Kelurahan.

## Domain ID dalam BPS WebAPI
Pada sistem BPS WebAPI:
- `0000` = Domain Nasional (BPS Pusat).
- `xx00` = Domain BPS Provinsi (contoh: `3200` BPS Provinsi Jawa Barat).
- `xxyy` = Domain BPS Kabupaten/Kota (contoh: `3273` BPS Kota Bandung).
