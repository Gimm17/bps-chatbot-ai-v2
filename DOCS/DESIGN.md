# DESIGN.md — BPS AI Assistant
## UI/UX Design Specification for Google Stitch

**Project:** BPS AI Assistant  
**Product Type:** Public AI Chatbot / Statistical Information Assistant  
**Primary Audience:** Masyarakat umum, mahasiswa, peneliti, pelaku usaha, media, dan pengguna layanan BPS  
**Authentication:** Tidak ada login untuk pengguna publik  
**Primary Channel:** Standalone web application  
**Secondary Channel:** Embedded chatbot/widget pada website BPS  
**Design Goal:** Modern, profesional, terpercaya, sederhana, mudah dipahami, dan tetap terasa sebagai produk digital BPS.

---

# 1. MASTER PROMPT UNTUK GOOGLE STITCH

> Gunakan bagian ini sebagai prompt utama ketika membuat desain di Google Stitch.

```text
Design a modern, professional, responsive public AI assistant web application named
"BPS AI Assistant" for Badan Pusat Statistik (BPS) Indonesia.

The product is a public-facing chatbot that anyone can use without login.
Its purpose is to help citizens ask questions about BPS, statistical concepts,
publications, statistical data, methodology, census/survey information, and BPS services.

The interface must feel trustworthy, official, clean, and accessible.
Avoid futuristic sci-fi AI styling, excessive gradients, neon glows, glassmorphism,
or a generic ChatGPT clone appearance.

BRAND VISUAL DIRECTION:
Use the visual identity from the supplied BPS logo as the main inspiration.

Primary BPS colors extracted from the supplied logo:
- BPS Blue: #00ADEF
- BPS Orange: #F7941D
- BPS Green: #8CC63E

Because the original BPS blue is bright, use a darker derived blue (#0077A6)
for filled primary buttons with white text when accessibility requires higher contrast.
The original #00ADEF should still remain highly visible as a signature brand color
in icons, active indicators, borders, small surfaces, highlights, and illustrations.

Use:
- BPS Blue as the main brand color.
- BPS Green as a supporting color for success, verified data, valid source, and positive states.
- BPS Orange as a supporting accent for important information, highlights, attention,
  publication/data-related accents, and non-critical warnings.
- Neutral white, soft gray, and dark navy/slate for backgrounds and readable typography.

PRODUCT PERSONALITY:
- Official but friendly
- Helpful
- Clear
- Intelligent but not intimidating
- Data-oriented
- Transparent
- Trustworthy
- Human-centered
- Minimal
- Professional government digital service

MAIN DESKTOP LAYOUT:
Create a full-page AI assistant interface.

Top header:
- BPS logo on the left.
- Product name: "BPS AI Assistant".
- Small label: "Asisten Statistik Publik".
- On the right: optional buttons for "Tentang", "Bantuan", and theme/settings icon.
- Include a subtle "Prototype / Demo" badge when in demo mode.
- Header should be clean, white, lightly bordered, not oversized.

Main content:
Create a centered chat experience with generous whitespace.

For the empty/welcome state:
- A small BPS AI icon/avatar using blue, orange, and green brand accents.
- Headline: "Halo, ada yang bisa saya bantu?"
- Supporting text:
  "Tanyakan data, istilah statistik, publikasi, metodologi, atau informasi layanan BPS."
- A large rounded chat input.
- Suggested question cards:
  "Apa itu inflasi?"
  "Apa itu PDRB?"
  "Bagaimana mencari publikasi BPS?"
  "Di mana saya bisa menemukan data penduduk?"
- Suggested cards should be compact, subtle, and visually organized in a 2x2 grid on desktop.

For the active chat state:
- User messages aligned to the right.
- AI messages aligned to the left.
- AI response should use a clean white or very light blue card, not a dark chat bubble.
- The AI answer must support rich structured content:
  headings, paragraphs, bullets, small data highlights, and citations.
- Show a BPS AI avatar next to assistant messages.
- Show a subtle status such as:
  "Mencari sumber BPS..."
  "Menyusun jawaban..."
- Do not use language that claims the AI is literally thinking.

SOURCE / CITATION UX:
This is a critical feature.
Every factual answer should visually support source cards.

Below AI answers, display a section:
"Sumber"
with one or more source cards.

Each source card contains:
- Source number [1], [2], etc.
- Publication/page/source title.
- BPS unit or source type.
- Optional year/period.
- Small external-link icon.
- CTA: "Buka sumber".

Verified source cards can use a subtle green indicator.
Do not make source cards visually dominant over the answer.

FEEDBACK:
Below every assistant answer:
- Helpful / not helpful icon buttons.
- Copy answer button.
- Optional "Laporkan jawaban" action.
Keep controls subtle.

CHAT COMPOSER:
Sticky at the bottom of the main chat area.
Large rounded rectangular textarea.
Placeholder:
"Tanyakan sesuatu tentang BPS..."
Include:
- Send button.
- Optional attachment/source icon only if implemented later.
- Clear focus state.
- Keyboard-friendly interaction.
Under composer include a short disclaimer:
"BPS AI dapat melakukan kesalahan. Verifikasi informasi melalui sumber yang ditampilkan."

NO LOGIN:
Do not show sign-in, account creation, profile requirement, or paywall.

OPTIONAL DESKTOP SIDE PANEL:
If a sidebar is used, keep it minimal.
It may contain:
- "Chat baru"
- Recent conversations from the current local/browser session only
- Quick navigation: Tentang, Cara menggunakan, Sumber
The sidebar should not imply a registered user account.

IMPORTANT AI STATES:
Design dedicated UI states for:
1. Welcome / Empty
2. Retrieving sources
3. Generating answer
4. Answered with sources
5. Clarification required
6. No evidence found
7. Out of scope
8. Rate limited
9. Provider/service error
10. Feedback submitted

CLARIFICATION STATE:
Example:
User: "Berapa jumlah penduduk di sini?"
AI should present a friendly clarification card:
"Wilayah dan periode mana yang Anda maksud?"
Optionally show compact input suggestion chips:
"Provinsi"
"Kabupaten/Kota"
"Tahun"

NO EVIDENCE STATE:
Use a calm neutral/soft orange information card.
Text:
"Saya belum menemukan sumber BPS yang cukup untuk memastikan jawaban tersebut."
Offer:
- "Perjelas pertanyaan"
- "Cari di website BPS"
- human service/escalation link if configured

OUT OF SCOPE:
Do not use an aggressive error state.
Use a neutral card:
"Saya difokuskan untuk membantu pertanyaan seputar BPS, statistik, publikasi, dan layanan BPS."
Then show 2–3 suggested BPS-related prompts.

ERROR STATE:
Use a clear but non-technical message:
"Layanan AI sedang tidak tersedia. Silakan coba kembali."
Never display raw provider errors, API names, stack traces, or secrets.

VISUAL STYLE:
- White/light background
- Large whitespace
- Thin borders
- Rounded corners 12–16px
- Subtle shadows only
- Avoid heavy cards everywhere
- Avoid excessive gradients
- Avoid glossy effects
- Use brand colors strategically, not on every component
- Content readability is more important than decoration

TYPOGRAPHY:
Use a modern sans-serif font suitable for Indonesian government services.
Preferred:
Inter, Plus Jakarta Sans, or a similar highly readable sans-serif.

Typography hierarchy:
- Page title: 28–32px desktop
- Hero headline: 28–36px
- Section title: 20–24px
- Body: 15–16px
- Metadata: 12–14px
- Line height around 1.5–1.65 for long AI answers

RESPONSIVE:
Desktop:
- Main content max-width around 900–1080px.
- Chat answer text column max-width around 780–860px.
- Suggested prompts 2-column grid.

Tablet:
- Main width fluid.
- Suggested prompts 2 columns or 1 based on available width.

Mobile:
- Single-column.
- Header simplified.
- No permanent sidebar.
- Suggested prompts stacked.
- Chat composer fixed/sticky above safe area.
- Touch targets minimum approximately 44px.
- Source cards full width.

ACCESSIBILITY:
- WCAG AA-oriented contrast.
- Do not use color as the only status indicator.
- Keyboard focus clearly visible.
- Icons must have text labels/tooltips where needed.
- Avoid white text on the bright original BPS blue #00ADEF for small normal text;
  use dark text on #00ADEF or use darker blue #0077A6 for white-text primary buttons.
- Proper semantic hierarchy.
- Source links descriptive.
- Error messages understandable without color.

DESIGN THE FOLLOWING SCREENS:
1. Welcome / Empty Chat — Desktop
2. Active Chat with Answer + Sources — Desktop
3. Clarification Required State
4. No Evidence State
5. Out-of-Scope State
6. Provider Error / Rate Limited State
7. Mobile Welcome Screen
8. Mobile Active Chat Screen
9. Optional Embedded Chat Widget Version
10. Optional Demo/Prototype About Screen

The result should look like an official BPS digital product,
not a third-party AI application with BPS colors pasted on top.
```

---

# 2. DESIGN PRINCIPLES

## 2.1 Trust Before "AI Wow"

Prioritas desain:

1. **Mudah dipercaya**
2. **Mudah dibaca**
3. **Sumber mudah diverifikasi**
4. **Mudah digunakan**
5. Baru kemudian terlihat modern/AI

Jangan membuat tampilan terlalu futuristik karena fungsi utama sistem adalah layanan informasi publik, bukan showcase teknologi.

---

## 2.2 BPS Identity Without Visual Overload

Logo BPS mempunyai tiga karakter warna yang sangat kuat:

| Warna | Hex dari logo | Fungsi utama |
|---|---|---|
| BPS Blue | `#00ADEF` | Identitas utama |
| BPS Orange | `#F7941D` | Accent / highlight |
| BPS Green | `#8CC63E` | Data valid / success / verified |

Ketiga warna **tidak perlu digunakan dengan proporsi sama besar**.

Rekomendasi proporsi visual:

- 65–75% neutral putih/abu
- 15–20% blue family
- 5–8% green
- 3–5% orange

Tujuannya supaya tampilan tetap elegan dan profesional.

---

# 3. COLOR SYSTEM

## 3.1 Brand Colors — Extracted from Provided BPS Logo

### BPS Blue

```css
--bps-blue: #00ADEF;
```

RGB:

```text
0, 173, 239
```

Gunakan untuk:

- logo accent
- active state
- icon
- highlight
- small badge
- link
- focus accent
- AI avatar
- selected navigation
- light brand surfaces

**Catatan aksesibilitas:**  
`#00ADEF` terlalu terang untuk white text kecil/normal. Gunakan dark text di atas warna ini, atau gunakan dark-blue derivative untuk button dengan white text.

---

### BPS Orange

```css
--bps-orange: #F7941D;
```

RGB:

```text
247, 148, 29
```

Gunakan untuk:

- highlight data/publikasi
- secondary brand accent
- notice
- attention
- small decorative indicator
- warning non-critical

Jangan gunakan sebagai background besar.

---

### BPS Green

```css
--bps-green: #8CC63E;
```

RGB:

```text
140, 198, 62
```

Gunakan untuk:

- verified source
- success
- available data
- positive feedback
- active/valid status

Jangan gunakan white text kecil langsung di atas green brand karena kontras kurang kuat.

---

# 4. ACCESSIBLE UI DERIVATIVES

Warna berikut adalah turunan untuk UI, **bukan perubahan warna logo**.

## 4.1 Blue Family

| Token | Hex | Use |
|---|---|---|
| `blue-50` | `#E8F8FE` | very light background |
| `blue-100` | `#CDEFFD` | selected/light surface |
| `blue-300` | `#66CFF5` | decorative |
| `blue-500` | `#00ADEF` | official logo blue |
| `blue-700` | `#0077A6` | accessible primary button |
| `blue-800` | `#005F85` | hover/dark accent |
| `blue-900` | `#06445C` | dark brand text |

### Primary Button

Default:

```css
background: #0077A6;
color: #FFFFFF;
```

Hover:

```css
background: #005F85;
```

Original `#00ADEF` tetap digunakan untuk brand visibility, tetapi bukan background CTA berisi teks kecil putih.

---

## 4.2 Green Family

| Token | Hex | Use |
|---|---|---|
| `green-50` | `#F2F9E9` | verified source background |
| `green-100` | `#E3F2CF` | success surface |
| `green-500` | `#8CC63E` | BPS logo green |
| `green-700` | `#527D17` | accessible green text |

---

## 4.3 Orange Family

| Token | Hex | Use |
|---|---|---|
| `orange-50` | `#FFF5E8` | notice background |
| `orange-100` | `#FFE8C9` | attention surface |
| `orange-500` | `#F7941D` | BPS logo orange |
| `orange-700` | `#9A5600` | accessible warning text |

---

# 5. NEUTRAL PALETTE

```css
--neutral-0: #FFFFFF;
--neutral-25: #FCFCFD;
--neutral-50: #F8FAFC;
--neutral-100: #F1F5F9;
--neutral-200: #E2E8F0;
--neutral-300: #CBD5E1;
--neutral-500: #64748B;
--neutral-600: #475569;
--neutral-700: #334155;
--neutral-800: #1E293B;
--neutral-900: #0F172A;
```

Recommended:

```css
body background: #F8FAFC;
content surface: #FFFFFF;
main text: #0F172A;
secondary text: #475569;
muted text: #64748B;
border: #E2E8F0;
```

---

# 6. SEMANTIC COLOR TOKENS

```css
--color-primary: #0077A6;
--color-primary-brand: #00ADEF;

--color-success: #527D17;
--color-success-brand: #8CC63E;

--color-warning: #9A5600;
--color-warning-brand: #F7941D;

--color-danger: #B42318;

--color-info-bg: #E8F8FE;
--color-success-bg: #F2F9E9;
--color-warning-bg: #FFF5E8;
--color-danger-bg: #FEF3F2;
```

---

# 7. TYPOGRAPHY

## Preferred Font

Prioritas:

1. `Inter`
2. `Plus Jakarta Sans`
3. `Arial`
4. system sans-serif

Untuk web demo paling aman:

```css
font-family:
  Inter,
  "Plus Jakarta Sans",
  Arial,
  sans-serif;
```

---

## Type Scale

### Display / Welcome Headline

```text
Desktop: 32px / 40px
Mobile: 26px / 34px
Weight: 650–700
Color: #0F172A
```

### Page/Product Title

```text
18–20px
Weight: 650–700
```

### Section Title

```text
20–24px
Weight: 650
```

### AI Answer Body

```text
16px
Line height: 1.6
Weight: 400
Color: #1E293B
```

### UI Label

```text
14px
Weight: 500–600
```

### Metadata

```text
12–13px
Color: #64748B
```

---

# 8. SPACING SYSTEM

Gunakan 4px base grid.

```text
4px
8px
12px
16px
20px
24px
32px
40px
48px
64px
```

Standard:

- Button horizontal padding: 16–20px
- Card padding: 16–20px
- Main content vertical gap: 24px
- AI answer sections: 16px
- Page horizontal desktop: 32–48px
- Mobile page padding: 16px

---

# 9. BORDER RADIUS

```css
--radius-xs: 6px;
--radius-sm: 8px;
--radius-md: 12px;
--radius-lg: 16px;
--radius-xl: 20px;
--radius-pill: 999px;
```

Use:

- Button: 10–12px
- Input: 14–16px
- Cards: 12–16px
- Chat composer: 16px
- Badges: pill

Jangan membuat semua item terlalu bulat.

---

# 10. SHADOW

Subtle only.

```css
--shadow-sm:
  0 1px 2px rgba(15, 23, 42, 0.05);

--shadow-md:
  0 8px 24px rgba(15, 23, 42, 0.08);
```

Gunakan shadow hanya pada:

- floating widget
- composer jika sticky
- modal
- source drawer
- elevated important surface

---

# 11. MAIN PAGE ARCHITECTURE

```text
┌──────────────────────────────────────────────────────────┐
│ Header                                                   │
│ BPS Logo | BPS AI Assistant          Bantuan | Tentang   │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                 Main Chat Container                      │
│                                                          │
│                 Welcome / Messages                       │
│                                                          │
│                                                          │
│                                                          │
│                Sticky Chat Composer                      │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ Small disclaimer / footer                               │
└──────────────────────────────────────────────────────────┘
```

Main container:

```text
max-width: 1000px
answer reading column: max-width 820px
center aligned
```

---

# 12. HEADER DESIGN

## Desktop

Height:

```text
64–72px
```

Structure:

```text
[BPS LOGO]
BPS AI Assistant
Asisten Statistik Publik

                         Tentang
                         Bantuan
                         [Prototype]
```

Header:

```css
background: #FFFFFF;
border-bottom: 1px solid #E2E8F0;
```

Logo height:

```text
34–42px
```

Jangan memakai logo terlalu besar.

---

# 13. WELCOME / EMPTY STATE

Center vertically tetapi jangan tepat di tengah layar jika menyebabkan composer terlalu rendah.

## AI Identity

Gunakan simple AI avatar/icon berbentuk:

- circle / rounded square
- blue dominant
- small green + orange visual accent

Jangan mengubah logo BPS menjadi robot.

Logo BPS tetap logo organisasi.  
Avatar AI adalah elemen berbeda yang hanya mengambil warna identitas BPS.

---

## Welcome Copy

### Headline

> **Halo, ada yang bisa saya bantu?**

### Subtext

> Tanyakan data, istilah statistik, publikasi, metodologi, atau informasi layanan BPS.

---

# 14. SUGGESTED QUESTIONS

Desktop:

```text
┌─────────────────────┐ ┌─────────────────────┐
│ Apa itu inflasi?    │ │ Apa itu PDRB?       │
└─────────────────────┘ └─────────────────────┘

┌─────────────────────┐ ┌─────────────────────┐
│ Cari publikasi BPS  │ │ Data penduduk       │
└─────────────────────┘ └─────────────────────┘
```

Style:

```css
background: white;
border: 1px solid #E2E8F0;
border-radius: 12px;
```

Hover:

```css
border-color: #00ADEF;
background: #E8F8FE;
```

Small icon optional.

---

# 15. CHAT COMPOSER

Composer adalah interaction focal point.

Desktop:

```text
┌────────────────────────────────────────────────────┐
│ Tanyakan sesuatu tentang BPS...                    │
│                                                    │
│                                          [Kirim →] │
└────────────────────────────────────────────────────┘
```

Container:

```css
background: white;
border: 1px solid #CBD5E1;
border-radius: 16px;
```

Focus:

```css
border-color: #00ADEF;
box-shadow: 0 0 0 3px rgba(0, 173, 239, 0.15);
```

Send button:

```css
background: #0077A6;
color: white;
```

Disabled:

```css
background: #CBD5E1;
color: #64748B;
```

---

# 16. ACTIVE CHAT DESIGN

## User Message

Align right.

```css
background: #E8F8FE;
color: #0F172A;
border: 1px solid #CDEFFD;
```

Radius:

```text
16px 16px 4px 16px
```

Max-width:

```text
75%
```

---

## Assistant Message

Do **not** use a strong colored bubble.

Recommended:

```text
[AI Avatar]  BPS AI Assistant
             Jawaban panjang...
```

Use:

```css
background: transparent;
```

or optionally:

```css
background: #FFFFFF;
border: 1px solid #E2E8F0;
```

Long answers should feel like readable content, not a messenger bubble.

---

# 17. AI ANSWER CONTENT DESIGN

AI answer can contain:

- paragraph
- heading
- numbered list
- bullets
- simple table
- bold statistic
- citation marker

Example:

```text
Inflasi adalah kenaikan harga barang dan jasa secara umum
dan terus menerus dalam periode tertentu. [1]

Beberapa hal penting:

• ...
• ...
• ...

Sumber
[1] Metadata BPS — Inflasi
```

Avoid giant markdown-style headings.

---

# 18. CITATION DESIGN — CRITICAL

Citation adalah salah satu visual signature utama aplikasi.

## Inline Citation

Example:

```text
... pada periode tertentu. [1]
```

Chip:

```css
background: #E8F8FE;
color: #0077A6;
border-radius: 6px;
```

---

## Source Section

```text
Sumber

┌────────────────────────────────────────────────┐
│ ✓ [1] Metadata Statistik — Inflasi            │
│ Badan Pusat Statistik                         │
│ Metadata / Konsep                             │
│                                  Buka sumber ↗ │
└────────────────────────────────────────────────┘
```

Verified indicator:

- BPS Green dot/check
- text: `Sumber BPS`

Card:

```css
background: #FFFFFF;
border: 1px solid #E2E8F0;
```

Hover:

```css
border-color: #8CC63E;
```

---

# 19. FEEDBACK CONTROLS

Immediately after answer:

```text
Apakah jawaban ini membantu?

👍   👎   Salin   Laporkan
```

Do not use large buttons.

Use icon/text ghost buttons.

Positive selected:

```css
color: #527D17;
background: #F2F9E9;
```

Negative selected:

```css
color: #B42318;
background: #FEF3F2;
```

---

# 20. STATUS: RETRIEVING

Display:

```text
● Mencari sumber BPS...
```

or

```text
Mencari sumber BPS...
```

Animate only the small indicator.

Do not display:

- "AI sedang berpikir keras"
- fake chain-of-thought
- internal system activity

---

# 21. STATUS: GENERATING

Text:

> **Menyusun jawaban...**

Use subtle blue animated dot/bar.

Avoid aggressive loading spinner in center of page.

---

# 22. CLARIFICATION REQUIRED

Example:

```text
┌───────────────────────────────────────────────┐
│ Saya perlu sedikit informasi tambahan.       │
│                                               │
│ Wilayah dan periode mana yang Anda maksud?   │
│                                               │
│ [Provinsi] [Kabupaten/Kota] [Tahun]           │
└───────────────────────────────────────────────┘
```

Colors:

- light blue background
- blue border
- dark text

This should feel helpful, not like an error.

---

# 23. NO EVIDENCE

```text
┌─────────────────────────────────────────────────┐
│ i  Informasi belum ditemukan                   │
│                                                 │
│ Saya belum menemukan sumber BPS yang cukup      │
│ untuk memastikan jawaban tersebut.              │
│                                                 │
│ [Perjelas pertanyaan] [Cari di website BPS]     │
└─────────────────────────────────────────────────┘
```

Use:

```css
background: #FFF5E8;
border-left: 3px solid #F7941D;
```

Not red.

No evidence is not necessarily a system error.

---

# 24. OUT-OF-SCOPE

Use neutral/blue-gray card.

Text:

> Saya difokuskan untuk membantu pertanyaan seputar BPS, statistik, publikasi, dan layanan BPS.

Then:

```text
Coba tanyakan:
• Apa itu inflasi?
• Bagaimana mencari data penduduk?
• Di mana saya bisa menemukan publikasi BPS?
```

No red error treatment.

---

# 25. RATE LIMIT

Use warning state.

Headline:

> **Terlalu banyak permintaan**

Body:

> Silakan tunggu sebentar sebelum mengirim pertanyaan berikutnya.

CTA:

> Coba lagi

Orange accent allowed.

---

# 26. SERVICE ERROR

Use simple error state.

```text
Layanan AI sedang tidak tersedia.
Silakan coba kembali beberapa saat lagi.

[Coba lagi]
```

Use subtle red accent only.

Never expose:

- LimitRouter
- OpenAI-compatible
- HTTP response
- stack trace
- API key
- internal model name

to public error UI.

---

# 27. PROTOTYPE / DEMO BADGE

Because the first version is for presentation:

```text
Prototype
```

or

```text
Demo
```

Style:

```css
background: #FFF5E8;
color: #9A5600;
border: 1px solid #FFE8C9;
```

Position near product title, not floating in the center.

---

# 28. DISCLAIMER

At bottom/composer:

> **BPS AI dapat melakukan kesalahan. Verifikasi informasi melalui sumber yang ditampilkan.**

Style:

```text
12px
neutral-500
center
```

Do not make the disclaimer visually frightening.

---

# 29. DESKTOP SCREEN — WELCOME

```text
┌───────────────────────────────────────────────────────────────┐
│ [BPS] BPS AI Assistant             Tentang  Bantuan  Demo    │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│                                                               │
│                    [ BPS AI AVATAR ]                          │
│                                                               │
│              Halo, ada yang bisa saya bantu?                  │
│                                                               │
│   Tanyakan data, istilah statistik, publikasi, metodologi,    │
│                   atau layanan BPS.                           │
│                                                               │
│  ┌──────────────────────┐  ┌──────────────────────┐           │
│  │ Apa itu inflasi?     │  │ Apa itu PDRB?       │           │
│  └──────────────────────┘  └──────────────────────┘           │
│  ┌──────────────────────┐  ┌──────────────────────┐           │
│  │ Cari publikasi       │  │ Data penduduk       │           │
│  └──────────────────────┘  └──────────────────────┘           │
│                                                               │
│   ┌───────────────────────────────────────────────────────┐   │
│   │ Tanyakan sesuatu tentang BPS...               [Kirim]│   │
│   └───────────────────────────────────────────────────────┘   │
│                                                               │
│  BPS AI dapat melakukan kesalahan. Verifikasi sumber.         │
└───────────────────────────────────────────────────────────────┘
```

---

# 30. DESKTOP SCREEN — ACTIVE CHAT

```text
┌───────────────────────────────────────────────────────────────┐
│ [BPS] BPS AI Assistant             Tentang  Bantuan  Demo    │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│                        ┌────────────────────────────┐          │
│                        │ Apa itu inflasi?          │          │
│                        └────────────────────────────┘          │
│                                                               │
│ [AI] BPS AI Assistant                                        │
│                                                               │
│ Inflasi adalah ... [1]                                        │
│                                                               │
│ Beberapa poin penting:                                        │
│ • ...                                                         │
│ • ...                                                         │
│                                                               │
│ Sumber                                                        │
│ ┌───────────────────────────────────────────────────────────┐ │
│ │ ✓ [1] Metadata Statistik — Inflasi          Buka sumber ↗│ │
│ └───────────────────────────────────────────────────────────┘ │
│                                                               │
│ Apakah membantu?   👍  👎   Salin                            │
│                                                               │
│ ┌───────────────────────────────────────────────────────────┐ │
│ │ Tanyakan lanjutan...                              [Kirim] │ │
│ └───────────────────────────────────────────────────────────┘ │
└───────────────────────────────────────────────────────────────┘
```

---

# 31. MOBILE DESIGN

Viewport:

```text
360px–430px
```

## Header

```text
[BPS] BPS AI
             [?]
```

Avoid full desktop nav.

---

## Welcome

```text
[AI]

Halo, ada yang bisa
saya bantu?

Tanyakan informasi seputar BPS.

[Apa itu inflasi?]
[Apa itu PDRB?]
[Cari publikasi]
[Data penduduk]

[ Tanyakan sesuatu...      ↑ ]
```

---

## Mobile Active Chat

- user message width max 88%
- assistant answer full width
- source cards 100%
- composer sticky bottom
- buttons 44px minimum touch height
- source title max 2–3 lines before wrap naturally

---

# 32. OPTIONAL SIDEBAR

For standalone desktop only.

Width:

```text
240–272px
```

Items:

```text
+ Chat baru

Percakapan sesi ini
- Apa itu inflasi?
- Data penduduk...

───────────────
Tentang BPS AI
Cara menggunakan
```

Important:

- session-local
- no profile/avatar
- no account settings
- no "upgrade"

Sidebar background:

```css
#F8FAFC
```

---

# 33. OPTIONAL EMBEDDED WIDGET

Launcher bottom-right:

```text
[ icon ] Tanya BPS
```

Use BPS blue family.

Widget size desktop:

```text
width: 400–440px
height: 600–680px
```

Mobile:

```text
full-screen sheet
```

Widget content:

```text
Header
Welcome/messages
Composer
Disclaimer
```

Main website must continue working even if chatbot unavailable.

---

# 34. AI AVATAR

Do not alter official BPS logo into an AI character.

Create a separate abstract assistant symbol:

Concept:

- rounded circle
- central speech bubble/data dot
- blue dominant
- one orange node
- one green node

It should subtly echo BPS colors.

Avatar size:

```text
32px message
48–64px welcome
```

---

# 35. ICON STYLE

Use:

- Lucide-style outline icons
- stroke 1.75–2px
- rounded geometry
- simple

Examples:

- Send
- Search
- ExternalLink
- Copy
- ThumbsUp
- ThumbsDown
- Info
- AlertCircle
- RefreshCw
- BookOpen
- Database
- ChartNoAxesColumnIncreasing

Avoid mixed icon families.

---

# 36. DATA VISUAL LANGUAGE

Because BPS is a statistical institution, use subtle data-related visual motifs:

- small bar chart icon
- dot grid
- data nodes
- lightweight chart patterns

But avoid large decorative charts in chat UI unless actual user data is being visualized.

---

# 37. TABLE DESIGN

When AI returns a comparison:

```text
┌──────────────┬─────────┬─────────┐
│ Wilayah      │ Tahun   │ Nilai   │
├──────────────┼─────────┼─────────┤
│ ...          │ ...     │ ...     │
└──────────────┴─────────┴─────────┘
```

Style:

- white background
- subtle gray border
- blue-tinted header
- horizontal scrolling on mobile
- numeric data right aligned
- metadata clearly visible

---

# 38. BUTTON SYSTEM

## Primary

```css
background: #0077A6;
color: #FFFFFF;
```

## Secondary

```css
background: #FFFFFF;
color: #0077A6;
border: 1px solid #00ADEF;
```

## Ghost

```css
background: transparent;
color: #475569;
```

## Success

Use only when semantically correct.

```css
background: #F2F9E9;
color: #527D17;
```

---

# 39. INPUT SYSTEM

Input text:

```text
16px
```

Minimum height:

```text
44px
```

Default:

```css
border: 1px solid #CBD5E1;
```

Focus:

```css
border: 1px solid #00ADEF;
box-shadow: 0 0 0 3px rgba(0, 173, 239, 0.15);
```

Error:

```css
border-color: #B42318;
```

---

# 40. LINKS

Standard link:

```css
color: #0077A6;
```

Hover:

```css
color: #005F85;
text-decoration: underline;
```

Citation link may include external-link icon.

---

# 41. DARK MODE

Not required for first demo.

If generated by Stitch, keep dark mode secondary.

Do **not** sacrifice light mode quality.

Primary presentation should be light mode because:

- closer to BPS public digital-service identity
- citations easier to read
- data tables clearer
- logo works naturally on white

---

# 42. MOTION

Keep animation subtle.

Allowed:

- fade-in answer
- small source retrieval shimmer
- send button microinteraction
- source card hover
- 150–250ms transitions

Avoid:

- bouncing AI avatar
- constantly moving background
- particle effects
- excessive skeletons
- long splash animation

---

# 43. LOADING SKELETON

For initial conversation history/source cards:

Use neutral light gray.

For active LLM answer:
prefer status text over large skeleton block.

---

# 44. COPY TONE

Use Bahasa Indonesia:

- jelas
- tidak terlalu formal
- tidak sok akrab
- tidak terlalu teknis
- tidak menggurui

Good:

> Saya belum menemukan sumber BPS yang cukup untuk memastikan jawaban tersebut.

Avoid:

> Oops! Sepertinya aku lagi bingung nih 😅

This is a government statistical assistant.

---

# 45. SOURCE TRUST INDICATORS

Possible labels:

```text
Sumber BPS
Publikasi BPS
Metadata BPS
Data BPS
```

Green check can mean:

> sumber berhasil diverifikasi oleh knowledge registry

Do not use "verified" if backend does not actually verify it.

---

# 46. ACCESSIBILITY REQUIREMENTS

## Contrast

Important brand notes:

- White on `#00ADEF` ≈ insufficient for normal small text.
- Dark text `#0F172A` on `#00ADEF` is much stronger.
- White text can use derived `#0077A6`.

Use this intentionally.

---

## Focus

All interactive controls:

```css
outline/focus ring:
0 0 0 3px rgba(0,173,239,0.22)
```

---

## Keyboard

- Tab navigation
- Enter send
- Shift+Enter newline
- Escape close dialogs
- source cards focusable

---

# 47. RESPONSIVE BREAKPOINT GUIDE

```text
Mobile: < 640px
Tablet: 640–1023px
Desktop: >= 1024px
Large desktop: >= 1440px
```

Content should not become excessively wide on 1440p+ displays.

---

# 48. GRID

Desktop:

```text
12-column general grid
content max-width 1180px
chat max-width 960px
reading width 820px
```

Mobile:

```text
single column
16px side padding
```

---

# 49. SCREEN LIST FOR STITCH

Google Stitch should generate the following designs.

## Screen 01 — Desktop Welcome

Must contain:

- BPS header
- logo
- product name
- Demo badge
- welcome headline
- subtitle
- suggested questions
- chat composer
- disclaimer

---

## Screen 02 — Desktop Active Conversation

Must contain:

- question
- AI answer
- structured answer
- citations
- source card
- feedback
- composer

---

## Screen 03 — Clarification

Use ambiguous population question.

---

## Screen 04 — No Evidence

Show correct graceful failure.

---

## Screen 05 — Out of Scope

Show scope boundary.

---

## Screen 06 — Service Error / Rate Limit

Show non-technical user-facing error.

---

## Screen 07 — Mobile Welcome

360–390px width.

---

## Screen 08 — Mobile Active Conversation

Long answer + citation.

---

## Screen 09 — Embedded Widget

Desktop website floating widget.

---

## Screen 10 — About / How It Works

A simple page/modal explaining:

```text
Pertanyaan
   ↓
Pencarian sumber BPS
   ↓
AI menyusun jawaban
   ↓
Sumber ditampilkan
```

This increases public trust.

---

# 50. OPTIONAL ABOUT PAGE

Headline:

> Tentang BPS AI Assistant

Sections:

### Apa yang dapat dilakukan?

- menjelaskan istilah
- membantu menemukan data
- membantu mencari publikasi
- informasi layanan

### Bagaimana jawaban dibuat?

> Sistem mencari informasi dari sumber BPS yang tersedia, kemudian AI membantu menyusun penjelasan.

### Apa yang perlu diperhatikan?

> AI dapat melakukan kesalahan. Gunakan sumber yang ditampilkan untuk verifikasi.

---

# 51. DO — DESIGN RULES

Do:

- use whitespace
- emphasize source transparency
- use BPS blue as identity
- reserve green/orange for semantics
- make typography highly readable
- create clear states
- design mobile from start
- keep composer prominent
- make source links obvious
- show graceful uncertainty
- use real system behavior in UI

---

# 52. DON'T — DESIGN RULES

Do not:

- create ChatGPT clone
- use black/dark sidebar by default
- use neon gradient
- use glassmorphism everywhere
- put all 3 BPS colors on every component
- turn BPS logo into robot
- make fake official statistics
- show fake source URLs
- hide sources
- show login
- show pricing/subscription
- show account avatar
- expose model/provider technical details to public
- show chain-of-thought
- use huge AI illustration above the fold
- use generic purple AI branding
- make the interface look like cryptocurrency/SaaS dashboard

---

# 53. COMPONENT STATE MATRIX

| Component | Default | Hover | Active | Disabled | Error |
|---|---|---|---|---|---|
| Primary Button | blue-700 | blue-800 | blue-900 | neutral-200 | — |
| Suggested Card | white | blue-50 | blue-100 | neutral-50 | — |
| Composer | neutral border | — | blue focus | neutral | red border |
| Source Card | white | green-50 border | — | — | — |
| Feedback + | ghost | green-50 | green selected | — | — |
| Feedback - | ghost | red light | red selected | — | — |

---

# 54. DESIGN TOKENS — READY FOR IMPLEMENTATION

```css
:root {
  /* Brand */
  --bps-blue: #00ADEF;
  --bps-orange: #F7941D;
  --bps-green: #8CC63E;

  /* Accessible brand derivatives */
  --bps-blue-dark: #0077A6;
  --bps-blue-darker: #005F85;
  --bps-green-dark: #527D17;
  --bps-orange-dark: #9A5600;

  /* Brand surfaces */
  --bps-blue-50: #E8F8FE;
  --bps-blue-100: #CDEFFD;
  --bps-green-50: #F2F9E9;
  --bps-green-100: #E3F2CF;
  --bps-orange-50: #FFF5E8;
  --bps-orange-100: #FFE8C9;

  /* Neutral */
  --white: #FFFFFF;
  --neutral-25: #FCFCFD;
  --neutral-50: #F8FAFC;
  --neutral-100: #F1F5F9;
  --neutral-200: #E2E8F0;
  --neutral-300: #CBD5E1;
  --neutral-500: #64748B;
  --neutral-600: #475569;
  --neutral-700: #334155;
  --neutral-800: #1E293B;
  --neutral-900: #0F172A;

  /* Semantic */
  --danger: #B42318;
  --danger-bg: #FEF3F2;

  /* Radius */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.05);
  --shadow-md: 0 8px 24px rgba(15, 23, 42, 0.08);
}
```

---

# 55. FINAL VISUAL DIRECTION

The final interface should visually communicate:

```text
BPS
+
Statistical Data
+
Public Service
+
Modern AI
+
Trusted Sources
```

Not:

```text
Generic AI Chat
+
BPS Logo
```

A user should immediately understand:

1. This is a BPS product.
2. This is an AI assistant.
3. It is designed for statistical/public information.
4. Answers can be verified.
5. The system is simple enough for ordinary citizens.

---

# 56. FINAL GOOGLE STITCH INSTRUCTION

When Google Stitch generates alternatives:

**Prioritize this variant:**

- white/light interface
- BPS Blue dominant
- orange and green accents
- compact official header
- centered conversation experience
- spacious chat
- prominent source cards
- minimal sidebar
- highly polished desktop and mobile
- professional Indonesian public-service aesthetic

**Reject variants that:**

- look like ChatGPT/Claude clones
- use dark sidebar as main identity
- use purple/gradient AI aesthetic
- hide citation/source areas
- use oversaturated BPS colors over large surfaces
- use excessive dashboard cards
- add login/account features
- add unrelated AI features

---

# 57. DESIGN HANDOFF PRIORITY

Implementation order:

1. Foundation + tokens
2. Header
3. Welcome state
4. Chat composer
5. User/assistant messages
6. Citation/source card
7. Loading states
8. Clarification
9. No evidence
10. Out-of-scope
11. Error/rate-limit
12. Feedback
13. Mobile responsive
14. Embedded widget
15. Optional about/how-it-works

---

# 58. BRAND COLOR SOURCE NOTE

The base brand colors in this document were extracted directly from the BPS logo image supplied for this design request:

```text
Blue   #00ADEF
Orange #F7941D
Green  #8CC63E
```

Derived UI colors are intentionally added to support accessibility, readable text,
hover/focus states, surfaces, and semantic states while preserving the visual identity
of the supplied BPS logo.
