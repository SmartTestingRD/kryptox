# Image assets

This folder holds images that should be **automatically optimized** by Astro
during the build. Images here are processed through `astro:assets` and emitted
as WebP/AVIF where supported, with responsive `srcset` and lazy loading by
default.

For the original PHP project, all 50 MB of `media/` lived in one public
folder and was served as-is. Here we split that into two directories:

```
src/assets/    ← imported and optimized at build time (recommended for most)
public/        ← served as-is, no processing (use for SVG, OG images, robots.txt)
```

## Folder layout

The structure mirrors the original `media/` so migration is mechanical:

```
src/assets/
├── home/        ← whatis, hero stage, liquidity video poster, etc.
├── product/     ← capability page imagery
├── providers/   ← zodia.svg, 1konto.svg, utila.svg, sumsub.svg, ...
└── security/    ← kyt mockups, secured visuals, icons used in /security
```

For `public/` we keep:
- `favicon.svg`
- Any SVG logos used inline in the header (no need to process those)
- `og-image.jpg` for social previews (Astro doesn't optimize these well)
- `robots.txt`, `sitemap.xml` (once added)

## How to use

**Inside an Astro component:**

```astro
---
import { Image } from 'astro:assets';
import zodiaLogo from '../assets/providers/zodia.svg';
---

<Image
  src={zodiaLogo}
  alt="Zodia Markets"
  width={120}
  height={32}
/>
```

**Inside MDX:**

```mdx
import { Image } from 'astro:assets';
import settlementDiagram from '../../assets/product/settlement-flow.png';

<Image src={settlementDiagram} alt="Five-stage settlement flow" />
```

## Migrating from `kryptox/media/`

When porting an asset:

1. Copy the file from `kryptox/media/<category>/<file>` to the matching
   `src/assets/<category>/<file>` here.
2. Replace any `media('<category>/<file>')` PHP helper call with an
   `import` of the file at the top of the component, then use `<Image />`.
3. Where the original used a JPEG/PNG, you don't need to convert it —
   Astro will serve a WebP automatically to browsers that accept it.

## What to leave in `public/`

Anything that needs a stable, predictable URL with no hash in the filename:
favicons, the OG-image for social cards, `robots.txt`, `manifest.webmanifest`.
