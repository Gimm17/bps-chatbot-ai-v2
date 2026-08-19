---
name: Statistik Modern
colors:
  surface: '#f8f9ff'
  surface-dim: '#ccdbf3'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e6eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d5e3fc'
  on-surface: '#0d1c2e'
  on-surface-variant: '#3f484f'
  inverse-surface: '#233144'
  inverse-on-surface: '#eaf1ff'
  outline: '#6f787f'
  outline-variant: '#bfc8d0'
  surface-tint: '#00658e'
  primary: '#005d83'
  on-primary: '#ffffff'
  primary-container: '#0077a6'
  on-primary-container: '#ebf5ff'
  inverse-primary: '#83cfff'
  secondary: '#8c4f00'
  on-secondary: '#ffffff'
  secondary-container: '#fd9923'
  on-secondary-container: '#663800'
  tertiary: '#3c6100'
  on-tertiary: '#ffffff'
  tertiary-container: '#4e7c00'
  on-tertiary-container: '#e0ffb4'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c7e7ff'
  primary-fixed-dim: '#83cfff'
  on-primary-fixed: '#001e2e'
  on-primary-fixed-variant: '#004c6c'
  secondary-fixed: '#ffdcbf'
  secondary-fixed-dim: '#ffb874'
  on-secondary-fixed: '#2d1600'
  on-secondary-fixed-variant: '#6b3b00'
  tertiary-fixed: '#b7f567'
  tertiary-fixed-dim: '#9cd84e'
  on-tertiary-fixed: '#102000'
  on-tertiary-fixed-variant: '#304f00'
  background: '#f8f9ff'
  on-background: '#0d1c2e'
  surface-variant: '#d5e3fc'
  brand-blue-vibrant: '#00ADEF'
  brand-blue-deep: '#005F85'
  brand-green-deep: '#527D17'
  brand-orange-deep: '#9A5600'
  surface-blue: '#E8F8FE'
  surface-green: '#F2F9E9'
  surface-orange: '#FFF5E8'
  surface-error: '#FEF3F2'
  bg-main: '#F8FAFC'
  border-default: '#E2E8F0'
typography:
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.25'
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 26px
    fontWeight: '700'
    lineHeight: '1.25'
  section-title:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '650'
    lineHeight: '1.4'
  body-main:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-bold:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1.4'
  label-medium:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.4'
  metadata:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: '1.5'
    letterSpacing: 0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 24px
  xl: 32px
  container-max: 1000px
  reading-max: 820px
---

## Brand & Style

The design system is built for a professional government digital service, prioritizing authority, transparency, and accessibility. It represents a "Trust Before Wow" philosophy, moving away from experimental AI aesthetics toward a structured, data-driven environment.

The style is **Corporate / Modern**, characterized by:
- **Clarity & Structure:** AI responses are treated as formal documents rather than casual chat bubbles.
- **Human-Centric Geometry:** Use of generous rounded corners to soften the institutional nature of the service.
- **Functional Transparency:** Visual emphasis on source verification and citations to reinforce the credibility of Badan Pusat Statistik (BPS).
- **Whitespace:** Ample breathing room to reduce cognitive load when viewing complex statistical data.

## Colors

The palette is a triadic system rooted in the official BPS identity, but optimized for WCAG AA digital accessibility. 

- **Primary & Interactive:** Use `#0077A6` for all primary actions and interactive text. The vibrant `#00ADEF` is reserved for non-textual brand accents, icons, and indicators.
- **Semantic States:** 
    - **Verification:** Use Green derivatives for verified sources and success states.
    - **Notice/Warning:** Use Orange for non-critical alerts and data highlights.
    - **Error:** Use Red tints for service errors and negative feedback.
- **Surfaces:** The interface uses a tiered background system. The main application background is a cool gray (`#F8FAFC`), while specific cards use brand-tinted surfaces (Blue-50, Green-50, Orange-50) to categorize content types (e.g., user messages vs. verified sources).

## Typography

The system utilizes a dual-font approach. **Plus Jakarta Sans** provides a welcoming, modern feel for high-level headings and interface titles. **Inter** is utilized for body text and functional UI labels due to its exceptional legibility and neutral tone, which is critical for statistical data consumption.

- **Scale:** Headlines utilize a tight line-height to maintain impact, while AI-generated body text uses a generous `1.6` line-height to facilitate long-form reading.
- **Citations:** Inline citations and source markers should use the `Label-Bold` style to remain distinct from the narrative text.
- **Accessibility:** Ensure all metadata (12px) maintains at least a 4.5:1 contrast ratio against its background.

## Layout & Spacing

This system follows a **4px base grid** to ensure mathematical consistency across components.

- **Layout Model:** A centered, fixed-width reading column (`820px`) nested within a broader application container (`1000px`). This prevents line lengths from becoming too long, which is essential for readability in AI responses.
- **Responsive Behavior:** 
    - **Desktop:** Side margins of `32px` to `48px`.
    - **Tablet:** Vertical stack for sidebar elements.
    - **Mobile:** Side margins shrink to `16px`.
- **Gaps:** Use a `24px` vertical gap between major content blocks (e.g., User Message vs. Assistant Response). Within an Assistant response, use `16px` gaps between paragraphs, tables, or source cards.

## Elevation & Depth

The system uses a **Low-Contrast / Tonal Layering** approach. Hierarchy is primarily established through background color shifts rather than heavy shadows.

- **Shadow Character:** When used, shadows are extremely diffused (`0 1px 2px` or `0 8px 24px`) with a low-opacity dark blue tint (`rgba(15, 23, 42, 0.05)`) to maintain a "clean" feel.
- **Surface Tiers:**
    - **Tier 1 (Base):** App background (`#F8FAFC`).
    - **Tier 2 (Cards):** Pure white (`#FFFFFF`) with a `1px` border in `#E2E8F0`.
    - **Tier 3 (Floating):** Chat composer and Modals, utilizing `Shadow MD` to indicate they sit above the scrollable content.
- **Interactivity:** Elements like suggested questions or buttons should use a subtle elevation increase or a fill-color shift on hover rather than a shadow change.

## Shapes

The design uses a **Rounded** geometry to balance official professionalism with modern accessibility.

- **Standard Radius:** `0.5rem` (8px) for small labels and metadata tags.
- **Component Radius:** `0.75rem` (12px) for buttons and suggested question cards.
- **Container Radius:** `1rem` (16px) for the Chat Composer, Input fields, and Main response cards.
- **Pill Radius:** Use `999px` strictly for status badges, tags, and circular icon buttons (like "Send").

Borders are consistently `1px` wide. Focus states must use a `3px` translucent blue ring (`rgba(0, 173, 239, 0.15)`) to ensure high visibility.

## Components

- **Primary Buttons:** High-contrast blue (`#0077A6`) with white text. Use `rounded-lg` (12px).
- **Secondary/Ghost Buttons:** Neutral border (`#E2E8F0`) with primary text. Used for less urgent actions.
- **Chat Composer:** A prominent `rounded-xl` (16px) container with a white background and `Shadow MD`. It should feel like a fixed, reliable tool at the bottom of the viewport.
- **Source Cards:** Specialized containers with a `1px` green border if verified. They include a small icon, title, and metadata. Backgrounds should use `surface-green` (`#F2F9E9`) to distinguish them from standard text.
- **Input Fields:** Large tap targets with `16px` padding and `12-16px` corner radius. Use the focus ring protocol defined in the Shapes section.
- **Chips / Citations:** Small, interactive badges within the text flow. Use `surface-blue` with `brand-blue-deep` text to ensure they are easily identifiable as clickable references.
- **Status Badges:** Pill-shaped, using the deep accessible versions of brand colors (`brand-green-deep`, `brand-orange-deep`) for text over their respective light surface tints.