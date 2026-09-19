import json, subprocess
from pathlib import Path
from PIL import Image, ImageOps
import os

ROOT = Path('/home/mohamed/Videos/dashboard/بسم الله/ezytemplate_final/backend')
PUBLIC = ROOT / 'storage/app/public'
W, H = 1600, 1000

def conv(inp, outp):
    im = Image.open(inp).convert('RGB')
    im = ImageOps.cover(im, (W, H), Image.Resampling.LANCZOS)
    outp.parent.mkdir(parents=True, exist_ok=True)
    im.save(outp, 'WEBP', quality=86, method=6)
    return im.size

def update(rid, img, imgs, dims):
    # convert to SQL string
    def s(v):
        return "'" + str(v).replace("\\", "\\\\").replace("'", "\\'") + "'"
    q = (f"UPDATE resources SET preview_image={s(img)}, detail_image={s(imgs[0])}, "
         f"screenshots={s(json.dumps(imgs))}, media_width={dims[0]}, media_height={dims[1]}, "
         f"media_sync_status='synced', media_synced_at=NOW() WHERE id={rid};")
    r = subprocess.run(['mysql', '-h', '127.0.0.1', '-P', '3307', '-u', 'root', '-proot_secret',
                        'ezytemplate', '-e', q], capture_output=True, text=True)
    return r.returncode == 0

def main():
    out = subprocess.run(['mysql', '-h', '127.0.0.1', '-P', '3307', '-u', 'root', '-proot_secret',
                          '-N', '-e', "USE ezytemplate; SELECT id, slug, resource_type, preview_image FROM resources WHERE id<=305 ORDER BY id;"],
                         capture_output=True, text=True).stdout
    converted, failed = 0, []
    for line in out.strip().splitlines():
        if not line:
            continue
        rid, slug, rtype, preview = line.split('\t')
        rid = int(rid)
        if rtype == 'design':
            dirp = PUBLIC / 'canva' / slug
        else:
            dirp = PUBLIC / 'template-previews' / slug
        if not dirp.exists():
            failed.append((slug, 'dir missing')); continue
        if (dirp / 'cover.webp').exists() and any(dirp.glob('sample-*.webp')):
            continue
        pngs = sorted(dirp.glob('*.png'))
        if not pngs:
            continue
        outs, urls, dims = [], [], None
        ok = True
        for png in pngs:
            name = png.stem
            if name.startswith('cover'):
                stem = 'cover'
            elif name.startswith('sample'):
                stem = name
            else:
                stem = name
            outp = dirp / f'{stem}.webp'
            try:
                dims = conv(png, outp)
            except Exception as e:
                ok = False; failed.append((slug, str(e))); break
            if rtype == 'design':
                url = f'/storage/canva/{slug}/{stem}.webp'
            else:
                url = f'/storage/template-previews/{slug}/{stem}.webp'
            urls.append((url, stem))
        if not ok or not dims:
            continue
        urls.sort(key=lambda t: (t[1] != 'cover', t[1]))
        urls = [u[0] for u in urls]
        cover_u, rest = urls[0], urls[1:]
        rest = [u for u in rest if 'sample' in u][:4]
        detail = rest[0] if rest else cover_u
        scr = rest if rest else [detail]
        if update(rid, cover_u, [detail] + [s for s in scr[1:] if s != detail], dims):
            converted += 1
        else:
            failed.append((slug, 'sql'))
    print('converted:', converted, 'failed:', len(failed))
    for f in failed[:10]:
        print('  FAIL', f)

if __name__ == '__main__':
    main()