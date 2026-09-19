#!/usr/bin/env python3
from __future__ import annotations

import io, json, os, re
from pathlib import Path
from xml.etree import ElementTree as ET
import zipfile

from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[1]
WORK = ROOT / 'storage/work/import'
PREVIEW_ROOT = ROOT / 'storage/app/public/template-previews'
W, H = 1600, 1000

BG = (246, 248, 252)
WHITE = (255, 255, 255)
BORDER = (218, 224, 232)
TEXT = (31, 41, 55)
MUTED = (107, 114, 128)
ACCENT = (37, 99, 235)


def font(size=32, bold=False):
    candidates = [
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf' if bold else '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        '/usr/share/fonts/truetype/liberation2/LiberationSans-Bold.ttf' if bold else '/usr/share/fonts/truetype/liberation2/LiberationSans-Regular.ttf',
    ]
    for p in candidates:
        try:
            return ImageFont.truetype(p, size)
        except Exception:
            pass
    return ImageFont.load_default()


def wrap(draw, text, font, max_w, max_lines):
    words = text.split()
    if not words:
        return ''
    lines, cur = [], ''
    for wd in words:
        trial = (cur + ' ' + wd).strip()
        if draw.textbbox((0, 0), trial, font=font)[2] <= max_w:
            cur = trial
        else:
            lines.append(cur)
            cur = wd
            if len(lines) >= max_lines:
                break
    if len(lines) < max_lines:
        lines.append(cur)
    return lines[:max_lines]


def read_xlsx_sheet_texts(path, max_sheets=4):
    out = []
    try:
        with zipfile.ZipFile(path) as z:
            names = z.namelist()
            shared = []
            if 'xl/sharedStrings.xml' in names:
                root = ET.fromstring(z.read('xl/sharedStrings.xml'))
                for si in root:
                    txt = ''.join(el.text or '' for el in si.iter() if el.tag.endswith('}t'))
                    shared.append(txt.strip())
            sheets = sorted([n for n in names if n.startswith('xl/worksheets/sheet') and n.endswith('.xml')])
            for sn in sheets[:max_sheets]:
                root = ET.fromstring(z.read(sn))
                rows = {}
                for c in root.iter():
                    if not c.tag.endswith('}c'):
                        continue
                    ref = c.attrib.get('r', '')
                    m = re.match(r'([A-Z]+)(\d+)', ref)
                    if not m:
                        continue
                    col, row = m.group(1), int(m.group(2))
                    typ = c.attrib.get('t')
                    v = next((x for x in c if x.tag.endswith('}v')), None)
                    inline = ''.join(
                        x.text or '' for x in c.iter() if x.tag.endswith('}t')
                    ).strip()
                    if typ == 'inlineStr' and inline:
                        val = inline
                    elif v is not None and v.text is not None:
                        val = v.text
                    else:
                        continue
                    if typ == 's':
                        try:
                            val = shared[int(val)]
                        except Exception:
                            continue
                    vv = str(val).strip()
                    if not vv or vv in ('0', '1'):
                        continue
                    if re.match(r'^-?\d+(\.\d+)?$', vv):
                        continue
                    rows.setdefault(row, []).append(vv)
                text_rows = [values for _, values in sorted(rows.items())[:90]]
                out.append(text_rows)
    except Exception as e:
        out.append([f'(error reading xlsx: {str(e)[:40]})'])
    return out


def read_xls_sheet_texts(path, max_sheets=4):
    out = []
    try:
        import xlrd
        wb = xlrd.open_workbook(path)
        for sh in wb.sheets()[:max_sheets]:
            rows = []
            for r in range(min(sh.nrows, 90)):
                vals = []
                for c in range(min(sh.ncols, 14)):
                    v = sh.cell_value(r, c)
                    if isinstance(v, float) and v == int(v):
                        v = int(v)
                    s = str(v).strip()
                    if s and (r == 0 or re.search(r'[A-Za-z]{2,}', s)):
                        vals.append(s)
                if vals:
                    rows.append(vals)
            out.append(rows)
    except Exception as e:
        out.append([[f'(error reading xls: {str(e)[:40]})']])
    return out


def sheet_image(title, rows, page_no, total, accent=ACCENT):
    canvas = Image.new('RGB', (W, H), BG)
    d = ImageDraw.Draw(canvas)

    d.rounded_rectangle([60, 40, W - 60, H - 40], radius=28, fill=WHITE, outline=BORDER, width=2)

    f_title = font(44, True)
    d.text((110, 92), title[:72], font=f_title, fill=TEXT)
    d.text((110, 156), f'Worksheet {page_no} of {total} - live content preview', font=font(22), fill=MUTED)
    d.line([110, 216, W - 110, 216], fill=BORDER, width=2)

    y = 258
    data_rows = rows[:38]
    for row in data_rows:
        row_text = '   |   '.join(str(x)[:34] for x in row[:10])
        lines = wrap(d, row_text, font(24), W - 280, 2)
        for ln in lines:
            if y > H - 120:
                break
            d.text((140, y), ln, font=font(24), fill=TEXT)
            y += 38
        y += 10
        if y > H - 120:
            break

    d.text((140, H - 96), 'EzyTemplate - preview from original workbook data', font=font(20), fill=MUTED)
    d.rounded_rectangle([110, 250, 118, 258], radius=4, fill=accent)

    return canvas


def make_cover(title, sheet_imgs, slug):
    canvas = Image.new('RGB', (W, H), BG)
    d = ImageDraw.Draw(canvas)
    header_h = 170
    d.rectangle([0, 0, W, header_h], fill=ACCENT)
    d.text((90, 46), title[:64], font=font(44, True), fill=WHITE)
    d.text((90, 108), 'Microsoft Excel Template  /  Excel', font=font(22), fill=(230, 238, 255))

    left_w, right_w = 760, 700
    left_img = sheet_imgs[0] if sheet_imgs else None
    right_imgs = sheet_imgs[1:4]
    y0 = header_h + 30
    box_h = H - y0 - 30
    if left_img:
        fill_left = left_img.resize((left_w, box_h), Image.Resampling.LANCZOS)
        canvas.paste(fill_left, (60, y0))
    if right_imgs:
        seg = box_h // len(right_imgs)
        for i, sim in enumerate(right_imgs):
            box = (854, y0 + i * seg, 854 + right_w, y0 + (i + 1) * seg - 8)
            bw, bh = box[2] - box[0], box[3] - box[1]
            rs = sim.resize((bw, bh), Image.Resampling.LANCZOS)
            canvas.paste(rs, (box[0], box[1]))
    canvas = canvas.convert('RGB')
    return canvas


def render(record):
    slug = record['slug']
    src = record['src']
    outdir = PREVIEW_ROOT / slug
    outdir.mkdir(parents=True, exist_ok=True)
    ext = Path(src).suffix.lower()
    if ext == '.xls':
        sheet_texts = read_xls_sheet_texts(src)
    else:
        sheet_texts = read_xlsx_sheet_texts(src)
    if not sheet_texts:
        sheet_texts = [[['(no readable content)']]]
    views = list(sheet_texts[:4])
    if len(views) < 4:
        first = views[0] if views else [['(no readable content)']]
        n = len(views)
        total = len(first)
        step = max(1, total // (4 - n + 1))
        for k in range(1, 4 - n + 1):
            start = min(k * step, total)
            views.append(first[start:start + 26] or first[:26])
        views = views[:4]
    page_imgs = []
    for i, rows in enumerate(views, start=1):
        im = sheet_image(record['title'], rows, i, len(sheet_texts))
        page_imgs.append(im)
        im.save(outdir / f'sample-{i}.webp', 'WEBP', quality=86, method=6)
    cover = make_cover(record['title'], page_imgs, slug)
    cover.save(outdir / 'cover.webp', 'WEBP', quality=86, method=6)
    return outdir


def main():
    plan = json.load(open(WORK / 'final_import_plan.json'))
    done, failed = 0, []
    for record in plan['records']:
        try:
            out = render(record)
            record['preview_dir'] = str(out)
            record['preview_image'] = f'/storage/template-previews/{record["slug"]}/cover.webp'
            record['detail_image'] = f'/storage/template-previews/{record["slug"]}/sample-1.webp'
            record['screenshots'] = [
                f"/storage/template-previews/{record['slug']}/sample-{i}.webp" for i in range(1, 5)
            ]
            record['media_width'] = W
            record['media_height'] = H
            record['gallery_status'] = 'generated-from-source'
            record['media_sync_status'] = 'synced'
            done += 1
        except Exception as e:
            failed.append((record['slug'], str(e)))
    json.dump(plan, open(WORK / 'final_import_plan.json', 'w'), ensure_ascii=False, indent=2)
    print(f'rendered: {done}, failed: {len(failed)}')
    for s, e in failed:
        print('  FAIL', s, e)


if __name__ == '__main__':
    main()
