import fs from 'node:fs/promises';
import fssync from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { chromium } from 'playwright';
import sharp from 'sharp';

const ROOT = path.resolve(process.argv[2] || '.');
const DATA = path.join(ROOT, 'database/data/canva_templates_188.json');
const OUT_ROOT = path.join(ROOT, 'storage/app/public/canva');
const shardArg = process.argv.find(arg => arg.startsWith('--shard='))?.slice(8);
const [shardIndex, shardCount] = shardArg ? shardArg.split('/').map(Number) : [0, 1];
if (!Number.isInteger(shardIndex) || !Number.isInteger(shardCount) || shardIndex < 0 || shardIndex >= shardCount) {
  throw new Error(`Invalid --shard value: ${shardArg}`);
}
const REPORT = path.join(ROOT, `storage/app/canva-media-sync-report${shardCount > 1 ? `-${shardIndex}-of-${shardCount}` : ''}.json`);
const WIDTH = 1200;
const HEIGHT = 900;
const allItems = JSON.parse(await fs.readFile(DATA, 'utf8'));
if (allItems.length !== 188) throw new Error(`Expected 188 Canva templates, got ${allItems.length}`);
const slugArg = process.argv.find(arg => arg.startsWith('--slug='))?.slice(7);
let items = slugArg
  ? allItems.filter(item => item.slug === slugArg)
  : allItems.filter((_, index) => index % shardCount === shardIndex);
if (process.argv.includes('--pending')) {
  items = items.filter(item => !fssync.existsSync(path.join(OUT_ROOT, item.slug, 'media.json')));
}
if (!items.length) throw new Error(`No Canva template found for slug: ${slugArg}`);

await fs.mkdir(OUT_ROOT, { recursive: true });

const browser = await chromium.launch({
  headless: true,
  args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
});
const context = await browser.newContext({
  viewport: { width: 1600, height: 1200 },
  deviceScaleFactor: 1,
  userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36'
});
const page = await context.newPage();
page.setDefaultTimeout(15000);

function sha(buf) { return crypto.createHash('sha256').update(buf).digest('hex'); }
function cleanUrl(u) { try { return new URL(u).href; } catch { return null; } }
function rejectUiUrl(u) {
  const x = (u || '').toLowerCase();
  return !u || x.includes('avatar') || x.includes('logo') || x.includes('favicon') || x.includes('emoji') || x.includes('profile') || x.includes('sprite');
}

async function normalizeBuffer(buf) {
  // Source image remains dominant; padding only standardizes card size.
  return sharp(buf)
    .rotate()
    .resize({ width: WIDTH - 64, height: HEIGHT - 64, fit: 'contain', withoutEnlargement: false, background: '#ffffff' })
    .extend({ top: 32, bottom: 32, left: 32, right: 32, background: '#f6f8fc' })
    .resize(WIDTH, HEIGHT, { fit: 'fill' })
    .png({ compressionLevel: 9 })
    .toBuffer();
}

async function saveNormalized(buf, dest) {
  const out = await normalizeBuffer(buf);
  await fs.writeFile(dest, out);
  return out;
}

async function fetchBuffer(url, referer) {
  const r = await context.request.get(url, { timeout: 60000, headers: { Referer: referer } });
  if (!r.ok()) return null;
  const ct = r.headers()['content-type'] || '';
  if (!ct.includes('image') && !/\.(png|jpe?g|webp)(\?|$)/i.test(url)) return null;
  const b = await r.body();
  if (b.length < 12_000) return null;
  try {
    const m = await sharp(b).metadata();
    if ((m.width || 0) < 300 || (m.height || 0) < 240) return null;
  } catch { return null; }
  return b;
}

async function collectFrameCandidates(frame) {
  const out = [];
  for (const sel of ['meta[property="og:image"]', 'meta[name="twitter:image"]', 'link[rel="image_src"]']) {
    const attr = sel.startsWith('link') ? 'href' : 'content';
    const val = await frame.locator(sel).first().getAttribute(attr).catch(() => null);
    if (val) out.push({ url: val, score: 50_000_000, source: sel });
  }
  const imgs = await frame.locator('img').evaluateAll(nodes => nodes.map(img => {
    const r = img.getBoundingClientRect();
    return {
      url: img.currentSrc || img.src || '',
      nw: img.naturalWidth || 0,
      nh: img.naturalHeight || 0,
      vw: r.width || 0,
      vh: r.height || 0,
      alt: img.alt || ''
    };
  }).filter(x => x.url && x.nw >= 250 && x.nh >= 180)).catch(() => []);
  for (const x of imgs) {
    const visibleBonus = Math.min(x.vw * x.vh, 2_000_000);
    out.push({ url: x.url, score: (x.nw * x.nh) + visibleBonus, source: 'img' });
  }
  // Canva sometimes paints previews as CSS backgrounds.
  const bgs = await frame.locator('body *').evaluateAll(nodes => {
    const vals = [];
    for (const el of nodes.slice(0, 5000)) {
      const bg = getComputedStyle(el).backgroundImage || '';
      const m = bg.match(/url\(["']?(https?:\/\/[^"')]+)["']?\)/i);
      if (m) {
        const r = el.getBoundingClientRect();
        if (r.width >= 280 && r.height >= 180) vals.push({ url: m[1], score: r.width * r.height });
      }
    }
    return vals;
  }).catch(() => []);
  for (const x of bgs) out.push({ url: x.url, score: x.score + 500_000, source: 'background' });
  return out;
}

async function screenshotDesignCandidates(page) {
  const locators = [
    '[data-testid*="design"]', '[data-testid*="page"]', '[class*="design"]',
    'main canvas', 'main [role="img"]', 'main img'
  ];
  const shots = [];
  for (const sel of locators) {
    const all = page.locator(sel);
    const count = Math.min(await all.count().catch(() => 0), 6);
    for (let i = 0; i < count; i++) {
      const el = all.nth(i);
      const box = await el.boundingBox().catch(() => null);
      if (!box || box.width < 420 || box.height < 260 || box.width > 1550 || box.height > 1150) continue;
      const buf = await el.screenshot({ type: 'png' }).catch(() => null);
      if (buf && buf.length > 20_000) shots.push(buf);
      if (shots.length >= 3) return shots;
    }
  }
  return shots;
}

async function extractSourceImages(page) {
  // Prefer screenshots of Canva's rendered design surface. These are the
  // actual template; page-level images/meta often point to Canva UI artwork.
  const designShots = await screenshotDesignCandidates(page);
  if (designShots.length) {
    const rendered = [];
    for (const shot of designShots.slice(0, 3)) {
      rendered.push({
        buffer: await normalizeBuffer(shot),
        sourceUrl: page.url(),
        sourceType: 'rendered-design',
      });
    }
    while (rendered.length < 3) {
      const base = rendered[0].buffer;
      const pos = rendered.length === 1 ? 'north' : 'south';
      const crop = await sharp(base)
        .resize(1500, 1125, { fit: 'cover', position: pos })
        .resize(WIDTH, HEIGHT, { fit: 'cover' })
        .png({ compressionLevel: 9 }).toBuffer();
      rendered.push({ buffer: crop, sourceUrl: page.url(), sourceType: `rendered-detail-${pos}` });
    }
    return rendered;
  }

  const allCandidates = [];
  for (const frame of page.frames()) {
    try { allCandidates.push(...await collectFrameCandidates(frame)); } catch {}
  }
  const unique = new Map();
  for (const c of allCandidates.sort((a,b) => b.score - a.score)) {
    const u = cleanUrl(c.url);
    if (!u || rejectUiUrl(u) || u.startsWith('data:')) continue;
    if (!unique.has(u)) unique.set(u, c);
  }
  const buffers = [];
  const hashes = new Set();
  for (const c of [...unique.values()].slice(0, 24)) {
    const b = await fetchBuffer(c.url, page.url()).catch(() => null);
    if (!b) continue;
    const normalized = await normalizeBuffer(b).catch(() => null);
    if (!normalized) continue;
    const h = sha(normalized);
    if (hashes.has(h)) continue;
    hashes.add(h);
    buffers.push({ buffer: normalized, sourceUrl: c.url, sourceType: c.source });
    if (buffers.length >= 3) break;
  }
  if (buffers.length < 3) {
    const shots = await screenshotDesignCandidates(page);
    for (const b of shots) {
      const normalized = await normalizeBuffer(b).catch(() => null);
      if (!normalized) continue;
      const h = sha(normalized);
      if (hashes.has(h)) continue;
      hashes.add(h);
      buffers.push({ buffer: normalized, sourceUrl: page.url(), sourceType: 'element-screenshot' });
      if (buffers.length >= 3) break;
    }
  }
  if (!buffers.length) {
    const shot = await page.screenshot({ type: 'png', fullPage: false });
    const normalized = await normalizeBuffer(shot);
    buffers.push({ buffer: normalized, sourceUrl: page.url(), sourceType: 'viewport-screenshot' });
  }
  // If Canva only exposes one preview, derive detail views from the SAME source image.
  while (buffers.length < 3) {
    const base = buffers[0].buffer;
    const pos = buffers.length === 1 ? 'north' : 'south';
    const crop = await sharp(base)
      .resize(1500, 1125, { fit: 'cover', position: pos })
      .resize(WIDTH, HEIGHT, { fit: 'cover' })
      .png({ compressionLevel: 9 }).toBuffer();
    buffers.push({ buffer: crop, sourceUrl: buffers[0].sourceUrl, sourceType: `source-detail-${pos}` });
  }
  return buffers.slice(0,3);
}

async function buildCover(samplePaths, dest) {
  const slots = [
    { left: 24, top: 24, width: 760, height: 852 },
    { left: 812, top: 24, width: 364, height: 410 },
    { left: 812, top: 466, width: 364, height: 410 }
  ];
  const composites = [];
  for (let i=0; i<3; i++) {
    const b = await sharp(samplePaths[i]).resize(slots[i].width, slots[i].height, { fit: 'cover', position: 'centre' }).png().toBuffer();
    composites.push({ input: b, left: slots[i].left, top: slots[i].top });
  }
  await sharp({ create: { width: WIDTH, height: HEIGHT, channels: 4, background: '#f6f8fc' } })
    .composite(composites).png({ compressionLevel: 9 }).toFile(dest);
}

const report = [];
let ok = 0;
for (let idx=0; idx<items.length; idx++) {
  const item = items[idx];
  const dir = path.join(OUT_ROOT, item.slug);
  await fs.mkdir(dir, { recursive: true });
  const started = new Date().toISOString();
  process.stdout.write(`[${String(idx+1).padStart(3,'0')}/${items.length}] ${item.name || item.title} ... `);
  try {
    const target = item.canva_preview_url || item.canva_url || item.source_url;
    let navigationError;
    for (let attempt = 1; attempt <= 3; attempt++) {
      try {
        await page.goto(target, { waitUntil: 'domcontentloaded', timeout: 90000 });
        navigationError = null;
        break;
      } catch (error) {
        navigationError = error;
        if (attempt < 3) await page.waitForTimeout(1500 * attempt);
      }
    }
    if (navigationError) throw navigationError;
    // Canva keeps analytics/network requests open indefinitely. Waiting for a
    // real design canvas is both faster and a more reliable readiness signal.
    await page.locator('canvas, [data-testid*="design"], [data-testid*="page"]')
      .first().waitFor({ state: 'visible', timeout: 15000 }).catch(() => {});
    await page.waitForTimeout(2500);
    const sources = await extractSourceImages(page);
    const samplePaths=[];
    for (let i=0;i<3;i++) {
      const p=path.join(dir,`sample-${i+1}.png`);
      await fs.writeFile(p,sources[i].buffer);
      samplePaths.push(p);
    }
    await buildCover(samplePaths,path.join(dir,'cover.png'));
    // The live catalog currently references WebP assets. Keep PNG originals
    // and replace the WebP variants atomically with the same real previews.
    for (let i=0;i<3;i++) {
      await sharp(samplePaths[i]).webp({ quality: 88 }).toFile(path.join(dir,`sample-${i+1}.webp`));
    }
    await sharp(path.join(dir,'cover.png')).webp({ quality: 88 }).toFile(path.join(dir,'cover.webp'));
    const meta = {
      slug: item.slug,
      sourcePage: target,
      width: WIDTH,
      height: HEIGHT,
      syncedAt: new Date().toISOString(),
      sources: sources.map(s => ({ url: s.sourceUrl, type: s.sourceType }))
    };
    await fs.writeFile(path.join(dir,'media.json'),JSON.stringify(meta,null,2));
    report.push({ slug:item.slug,status:'synced',startedAt:started,...meta });
    ok++;
    console.log('OK');
  } catch (e) {
    report.push({ slug:item.slug,status:'failed',startedAt:started,finishedAt:new Date().toISOString(),error:String(e?.message||e) });
    console.log(`FAILED: ${e.message}`);
  }
  await fs.writeFile(REPORT,JSON.stringify(report,null,2));
}

await browser.close();
console.log(`\nCanva media sync finished: ${ok}/${items.length} succeeded${slugArg ? ` for ${slugArg}` : ''}.`);
console.log(`Report: ${REPORT}`);
process.exit(ok === items.length ? 0 : 2);
