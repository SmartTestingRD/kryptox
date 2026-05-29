# KryptoX — Astro starter

A migration scaffold for moving KryptoX from PHP + Flexr to **Astro + MDX**.
This isn't the whole site yet — it's a working skeleton with the new information
architecture, design tokens, and **two trozos migrated as examples** so you can
feel how the rest will go.

---

## Setup

You need Node.js 18.17+ or 20.3+ installed, and [pnpm](https://pnpm.io) as
package manager (faster, stricter, and avoids the npm supply-chain surface).

```bash
pnpm install
pnpm dev
```

Open <http://localhost:4321> in your browser.

Other scripts:

```bash
pnpm build      # produces a static site in ./dist
pnpm preview    # serves the built site locally
```

---

## What's in this scaffold

| Path | What it is | Maps to (PHP original) |
|---|---|---|
| `src/pages/index.astro` | Home page | `part/page/home.php` |
| `src/pages/security.astro` | Security page | `part/page/security.php` |
| `src/pages/privacy.md` | Privacy Policy as plain Markdown | `part/page/privacy.php` |
| `src/pages/terms.md` | Terms of Use as plain Markdown | `part/page/terms.php` |
| `src/pages/product/[slug].astro` | Auto-route for any MDX in `src/content/product/` | `part/page/product.php` (rescued from huérfana) |
| `src/layouts/BaseLayout.astro` | Page shell with `<head>`, header, footer | `flexr/head.php` + `part/header.php` + `part/footer.php` |
| `src/layouts/LegalLayout.astro` | Chrome for legal pages (TOC + typography) | `part/page/base.legal.php` |
| `src/components/home/Hero.astro` | Hero with cyan/orange title + iframe slot | `part/sub/home/hero.php` |
| `src/components/security/NonCustodial.astro` | Light section explaining non-custodial model | `part/sub/security/non_custodial.php` |
| `src/components/common/Header.astro` | Top nav with **Product + Solutions** dropdowns | `part/header.php` (rebuilt) |
| `src/components/common/Footer.astro` | Footer with parallel columns | `part/footer.php` |
| `src/components/common/{Feature,FeatureGrid,CTA}.astro` | Reusable bits for MDX content | n/a — new |
| `src/content/product/orchestration.mdx` | Example capability page in MDX | n/a — new |
| `src/content/config.ts` | Schema for product MDX frontmatter | n/a — Astro feature |
| `src/assets/` | Build-time optimized images (WebP/AVIF) | `media/` (after migration) |
| `src/i18n/{en,es}.json` | Translation catalogs (reorganized) | `_tradu.json` |
| `src/styles/tokens.css` | All CSS variables | scattered `:root` blocks across PHP |
| `src/styles/global.css` | Reset + base typography | `flexr/css/custom.css` |
| `astro.config.mjs` | Astro + MDX + i18n config | `flexr/ini.php` + `.htaccess` |

---

## The new information architecture

The biggest non-cosmetic change. The PHP project has two competing IAs:
the live header navigates **Solutions by industry** (`?i=financial`, `?i=payments`...),
while `_tradu.json` and the orphaned `product.php` describe **Product by capability**.
Neither was the winner; both shipped, neither finished.

This scaffold picks the hybrid that B2B fintech standards (Stripe, Mercury, Bridge.xyz)
have converged on:

```
/                              Home (single narrative)
/product/                      Product — what it does (canonical capability pages)
  orchestration                ✓ (built as MDX example)
  liquidity
  settlement
  controls
  compliance
/solutions/                    Solutions — who it's for (light vertical pages)
  financial-institutions
  payment-providers
  crypto-platforms
  treasury
  compliance-risk
/security                      Security & Compliance (the differentiator)
/company                       (was about.php, narrowed scope)
/contact                       Contact form (NEEDS BACKEND)
/request-demo                  Demo request flow (NEEDS BACKEND)
/{privacy,terms,cookies,risk-disclosure}    Legal
```

Rule of thumb when adding content:
- **What** the product does → goes under `/product/*` (MDX)
- **Who** it's for → goes under `/solutions/*` (Astro, lighter, with cross-links to `/product/*`)
- Never describe a capability deeply on both sides.

---

## The three ways to author content here

This is the key thing to understand before extending.

### 1. Trozos = Astro components (HTML + CSS + minimal logic)

Use this for **layout-heavy, design-heavy** sections that need precise control:
heroes, complex grids, animated bits, anything where the structure carries the meaning.

Pattern: copy `src/components/home/Hero.astro` and adapt. Each component has its own
scoped `<style>` block — those styles can't leak into other components, which fixes
the "change one trozo, break another" risk the old PHP had.

### 2. MDX = capability pages

Use this for **copy-heavy pages with light layout components**: capability
pages, blog posts, case studies, marketing landing pages where the words
carry the meaning but you occasionally want a `<Feature>` or `<CTA>` block.

Pattern: copy `src/content/product/orchestration.mdx` and rewrite. The file is mostly
markdown, with `<Feature>` and `<CTA>` components dropped in where needed. Marketing
or the client can edit these without touching code.

### 3. Plain Markdown = legal pages

Use this for **prose-only documents**: privacy, terms, cookies, risk disclosures.
No components needed; just a title, an effective date, and numbered sections.

Pattern: copy `src/pages/privacy.md` and rewrite. The frontmatter declares
`layout: ../layouts/LegalLayout.astro`, which gives every legal page the same
chrome (eyebrow, title, effective date, sticky table of contents). A legal
reviewer or counsel can edit these files with no developer involvement.

**When in doubt:** if a non-developer should be able to update the page, use MDX or plain Markdown.

## Image handling — `astro:assets`

The original project bundled ~50 MB of unoptimized images under `media/`. The
scaffold splits that into two destinations with different processing rules:

- `src/assets/` — imported as ES modules, optimized to WebP/AVIF at build time, served with responsive `srcset`. Use this for **most** images.
- `public/` — served verbatim, no processing. Use for SVG logos used inline, favicons, OG images, `robots.txt`.

See `src/assets/README.md` for the full convention and a migration example
from the PHP `media()` helper.

---

## Open decisions before launch

These are content/business questions, not technical ones. They surfaced during the
audit and the scaffold can't decide them for you:

1. **Brand color** — tokens use `#00D7D7` (live code). The design system doc says `#00BFC2`. Pick one.
2. **The hero animation iframe** (the React 19 bundle at `/animate/`) — re-embed as-is, rewrite as an Astro island, or skip in v1? Currently a placeholder in `Hero.astro`.
3. **Compliance claims** — the audit found zero mentions of SOC 2, ISO 27001, GDPR, MiCA. Either certifications exist (and must be added to copy) or they don't (and the roadmap should say so).
4. **Real contact details** — placeholder phone (`+1-800-555-0000`), no real address, no email field on the contact form. Fix before any public exposure.
5. **`info@asmith.agency`** as the fallback contact email — exposes the agency. Replace with a KryptoX address.

---

## What hasn't been done yet (and is intentional)

This is a **starter**, not a complete migration. Roughly 60–80 hours of mechanical
work remains, plus content and backend decisions. The audit estimated **80–100 hours**
total with backend mínimo + i18n cabled.

Still to migrate (just patterns repeated — same as the examples already in the scaffold):

- 6 remaining trozos of `part/sub/home/` (whatis, control_layer, realtime, steps, liquidez, providers)
- 7 remaining trozos of `part/sub/security/` (hero, access, kyc, kyt, approvals, trazabilidad, data_protection)
- Pages: `about` → `/company`, `contact`, `solutions` (5 vertical pages)
- 2 remaining legal pages (`cookies.md`, `risk-disclosure.md` — same pattern as `privacy.md`/`terms.md`)
- 4 remaining product MDX capabilities (liquidity, settlement, controls, compliance)
- The contact form (needs real backend — Formspree, Resend, or a serverless function)
- The i18n cabling in components (currently only the catalogs exist; need to wire `Astro.currentLocale` into templates)
- The media migration from `kryptox/media/` to `src/assets/` (use the convention in `src/assets/README.md`)

The order I'd recommend:
1. **Confirm with the client** the brand color (`#00D7D7` vs `#00BFC2`) and **the IA decision** (Product-first vs Solutions-first). Don't migrate `product.php` or `solutions.php` until this is resolved — both are huge files and rewriting either one twice is the worst path.
2. Migrate all `part/sub/home/*` so the home page is complete (these are independent of the IA decision).
3. Migrate all `part/sub/security/*` so the security page is complete.
4. Migrate the 2 remaining legal pages (`cookies.md`, `risk-disclosure.md`).
5. Move `media/` → `src/assets/`, replacing PHP `media()` calls with `<Image>` from `astro:assets`.
6. **Once IA is decided**: write the product MDX files OR the solutions Astro pages (or both, with a deliberate split).
7. Wire i18n in components using `Astro.currentLocale`.
8. Wire the contact form to a backend.
9. Replace placeholder content (phone, address, `info@asmith.agency`, missing email field).
10. Draft compliance copy (SOC 2, ISO 27001, etc. — or roadmap statements if pre-certification).

---

## Deploy

Static output works on any host: Vercel, Netlify, Cloudflare Pages, GitHub Pages,
or your existing **Coolify** instance.

```bash
npm run build      # → ./dist (static HTML/CSS/JS)
```

Point Coolify at the repo, set build command `npm run build`, output directory `dist`.

---

## Stack reference

- [Astro 5 docs](https://docs.astro.build)
- [Astro content collections](https://docs.astro.build/en/guides/content-collections/)
- [Astro i18n routing](https://docs.astro.build/en/guides/internationalization/)
- [MDX in Astro](https://docs.astro.build/en/guides/integrations-guide/mdx/)
