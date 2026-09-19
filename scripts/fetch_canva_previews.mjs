import fs from 'node:fs/promises';
import path from 'node:path';
import { chromium } from 'playwright';
import sharp from 'sharp';

const projectRoot = path.resolve(process.argv[2] || '.');
const dataFile = path.join(projectRoot, 'database/data/canva_templates_188.json');
const storageRoot = path.join(projectRoot, 'storage/app/public/canva');
const items = JSON.parse(await fs.readFile(dataFile, 'utf8'));

if (items.length !== 188) throw new Error(`Expected 188 templates, found ${items.length}`);
await fs.mkdir(storageRoot, { recursive: true });

const browser = await chromium.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
const context = await browser.newContext({
  viewport: { width: 1440, height: 1100 },
  userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/132 Safari/537.36'
});
const page = await context.newPage();

async function imageCandidates(page) {
  const urls = [];
  for (const selector of ['meta[property="og:image"]', 'meta[name="twitter:image"]']) {
    const value = await page.locator(selector).getAttribute('content').catch(() => null);
    if (value) urls.push(value);
  }
  const dom = await page.locator('img').evaluateAll(imgs => imgs.map(img => ({
    src: img.currentSrc || img.src || '',
    w: img.naturalWidth || img.width || 0,
    h: img.naturalHeight || img.height || 0
  })).filter(x => x.src && x.w >= 300 && x.h >= 300).sort((a,b) => (b.w*b.h)-(a.w*a.h)).map(x => x.src)).catch(() => []);
  urls.push(...dom);
  return [...new Set(urls)].filter(x => /^https?:\/\//i.test(x)).slice(0, 8);
}

async function downloadAsPng(url, dest, referer) {
  const r = await context.request.get(url, { headers: { Referer: referer }, timeout: 60000 });
  if (!r.ok()) return false;
  const buf = await r.body();
  await sharp(buf).resize({ width: 1400, height: 1400, fit: 'inside', withoutEnlargement: true }).png({ compressionLevel: 9 }).toFile(dest);
  return true;
}

async function makeCover(sampleFiles, dest) {
  const slots = [
    { left: 30, top: 30, width: 1140, height: 520 },
    { left: 30, top: 580, width: 550, height: 290 },
    { left: 620, top: 580, width: 550, height: 290 },
  ];
  const composites = [];
  for (let i = 0; i < Math.min(sampleFiles.length, 3); i++) {
    const resized = await sharp(sampleFiles[i]).resize(slots[i].width, slots[i].height, { fit: 'contain', background: '#ffffff' }).png().toBuffer();
    composites.push({ input: resized, left: slots[i].left, top: slots[i].top });
  }
  await sharp({ create: { width: 1200, height: 900, channels: 4, background: '#f8fafc' } })
    .composite(composites).png({ compressionLevel: 9 }).toFile(dest);
}

let done = 0;
for (const item of items) {
  const dir = path.join(storageRoot, item.slug);
  await fs.mkdir(dir, { recursive: true });
  console.log(`[${++done}/188] ${item.name}`);
  try {
    await page.goto(item.canva_preview_url || item.source_url, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await page.waitForTimeout(4500);
    const urls = await imageCandidates(page);
    const samples = [];
    for (let i = 0; i < Math.min(urls.length, 3); i++) {
      const dest = path.join(dir, `sample-${i + 1}.png`);
      if (await downloadAsPng(urls[i], dest, page.url()).catch(() => false)) samples.push(dest);
    }
    if (!samples.length) {
      const dest = path.join(dir, 'sample-1.png');
      await page.screenshot({ path: dest, fullPage: false });
      samples.push(dest);
    }
    // If fewer than 3 source images are exposed by Canva, keep existing clearly-labelled placeholders for remaining slots.
    for (let i = samples.length + 1; i <= 3; i++) {
      const existing = path.join(dir, `sample-${i}.png`);
      try { await fs.access(existing); samples.push(existing); } catch {}
    }
    await makeCover(samples, path.join(dir, 'cover.png'));
  } catch (e) {
    console.error(`  Preview sync failed: ${e.message}`);
  }
}

await browser.close();
console.log('Canva preview sync completed.');
