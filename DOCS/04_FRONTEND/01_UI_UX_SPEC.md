# UI/UX Specification — BPS AI Assistant v2

> **Project:** BPS AI Assistant v2  
> **Brand Identity:** Official Badan Pusat Statistik Palette (8-Color System)  
> **Layout:** Single-Viewport Locked (`100dvh`) — Mobile & Desktop  

## 🎯 Design Direction & Principles
- **Professional & Trustworthy:** Tampilan bersih, elegan, dan berorientasi pada transparansi rujukan data statistik resmi.
- **Official BPS Color Palette:** Menggunakan palet resmi BPS: Biru (`#0093DD`), Oranye (`#EB891B`), Hijau (`#68B92E`), Charcoal Text (`#1F2937`), Muted Text (`#64748B`), Border (`#E2E8F0`), Background (`#F8FAFC`), dan Surface (`#FFFFFF`).
- **Unified Cloud Bubble Motif:** Ikon awan statistik 3-pilar diterapkan seragam di Header Logo, Avatar Bot, Avatar Chat, PWA Icon, dan Floating Widget.

## 📱 Mobile Architecture & Layout (`100dvh`)
- **Single-Viewport Height:** Seluruh elemen (Header, Chat Area, Composer) terkunci tepat di ketinggian layar perangkat menggunakan unit `100dvh` untuk mencegah *double scrollbar* atau tombol input tertutup keyboard virtual.
- **Mobile Navigation (320px–430px):**
  - Tombol aksi utama (Install PWA & Chat Baru) disajikan sebagai icon button 32px.
  - Menu tambahan (*Tentang BPS AI* dan *Panduan Pertanyaan*) diletakkan di dropdown menu 3-titik (*3-dots dropdown*).
- **Responsive Embed Modal:**
  - Pada layar ponsel, popup chat embed tidak menutupi seluruh layar (*non-fullscreen*), melainkan kotak berukuran proporsional yang diposisikan di atas tombol toggle floating agar tombol Enter tidak tertutup tombol Close.

## 🧩 Komponen Antarmuka Utama
1. **Header Component (`Header.vue`):** Logo Cloud Bubble gradien `#0093DD`, brand title, badge DEMO `#EB891B`, tombol Install PWA, Chat Baru, Tentang, dan Bantuan.
2. **Welcome Screen (`WelcomeScreen.vue`):** Avatar bot Cloud Bubble, headline ramah, dan 4 kartu pertanyaan statistik populer.
3. **Chat Stream & Messages (`MessageItem.vue`):** Bubble chat pengguna `#EBF7FD` bergaris `#0093DD/25`, Markdown parser aman (DOMPurify), kartu sumber rujukan resmi berstatus *"Resmi BPS"* (`#68B92E`), tombol salin jawaban, dan feedback helpful/not helpful.
4. **Chat Composer (`ChatComposer.vue`):** Input multiline elastis auto-resize, tombol kirim gradien `#0093DD`, dan disclaimer akurasi AI.
5. **State Handling (`StateCard.vue`):** Kartu khusus untuk status `clarification_required`, `no_evidence`, `out_of_scope`, `rate_limited`, dan `provider_error`.
