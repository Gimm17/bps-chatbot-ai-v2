# Official BPS Design Tokens

> **Project:** BPS AI Assistant v2  
> **Brand Identity:** Official Badan Pusat Statistik Palette

## 🎨 Palet Warna Resmi BPS (8-Color System)

| Token CSS | Fungsi | HEX | Penggunaan di Aplikasi |
|---|---|---|---|
| `--bps-blue` | **Primary Blue** | `#0093DD` | Navbar aktif, logo gradient, tombol utama, link rujukan, focus ring |
| `--bps-orange` | **Secondary Orange** | `#EB891B` | Badge DEMO, highlight modal bantuan, statistik penting |
| `--bps-green` | **Accent Green** | `#68B92E` | Badge *"Resmi BPS"*, status sukses, indikator positif |
| `--text-primary` | **Dark / Text** | `#1F2937` | Heading, teks pertanyaan dan respons AI |
| `--text-muted` | **Muted Text** | `#64748B` | Subteks hero, deskripsi kartu saran, placeholder input |
| `--border-color` | **Border** | `#E2E8F0` | Border kartu pertanyaan, input composer, modal dialog |
| `--bg-main` | **Background** | `#F8FAFC` | Background seluruh halaman & iframe |
| `--surface` | **Surface** | `#FFFFFF` | Permukaan kartu chat AI, modal dialog, popup widget |

```css
:root {
  --bps-blue: #0093DD;
  --bps-orange: #EB891B;
  --bps-green: #68B92E;
  --text-primary: #1F2937;
  --text-muted: #64748B;
  --border-color: #E2E8F0;
  --bg-main: #F8FAFC;
  --surface: #FFFFFF;

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;
}
```
