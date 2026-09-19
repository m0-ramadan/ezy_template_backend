#!/usr/bin/env python3
from __future__ import annotations

import json, re, shutil, zipfile, html, math
from pathlib import Path
from xml.etree import ElementTree as ET
from PIL import Image, ImageDraw, ImageFont, ImageFilter

ROOT = Path(__file__).resolve().parents[1]
DATA_FILE = ROOT / 'database/data/office_templates_117.json'
STORAGE = ROOT / 'storage/app/public/template-previews'
SOURCE_ROOT = ROOT / 'storage/app/public/templates'
W, H = 1200, 900
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
        try: return ImageFont.truetype(p, size)
        except: pass
    return ImageFont.load_default()


def fit_canvas(src: Image.Image, size=(W,H), bg=BG, margin=36):
    src = src.convert('RGB')
    canvas = Image.new('RGB', size, bg)
    maxw, maxh = size[0]-2*margin, size[1]-2*margin
    scale = min(maxw/src.width, maxh/src.height)
    nw, nh = max(1,int(src.width*scale)), max(1,int(src.height*scale))
    rs = src.resize((nw,nh), Image.Resampling.LANCZOS)
    x,y=(size[0]-nw)//2,(size[1]-nh)//2
    # shadow
    shadow = Image.new('RGBA', size, (0,0,0,0))
    sd=ImageDraw.Draw(shadow)
    sd.rounded_rectangle([x+7,y+9,x+nw+7,y+nh+9], radius=16, fill=(0,0,0,32))
    shadow=shadow.filter(ImageFilter.GaussianBlur(10))
    canvas=Image.alpha_composite(canvas.convert('RGBA'),shadow)
    white=Image.new('RGB',(nw+4,nh+4),WHITE)
    canvas.paste(white,(x-2,y-2))
    canvas.paste(rs,(x,y))
    return canvas.convert('RGB')


def make_cover(samples, title, dest):
    imgs=[Image.open(p).convert('RGB') for p in samples[:3] if Path(p).exists()]
    if not imgs: return
    while len(imgs)<3: imgs.append(imgs[-1].copy())
    canvas=Image.new('RGB',(W,H),BG)
    d=ImageDraw.Draw(canvas)
    # subtle header strip only; visual is still source-dominant
    d.rounded_rectangle([24,18,W-24,H-18], radius=24, fill=WHITE, outline=BORDER, width=2)
    # title
    f=font(28,True)
    title_short=title if len(title)<=58 else title[:55]+'…'
    d.text((48,38), title_short, font=f, fill=TEXT)
    d.text((48,78), 'Template preview', font=font(17), fill=MUTED)
    # slots
    slots=[(48,118,760,824),(835,118,1152,446),(835,492,1152,824)]
    for img,box in zip(imgs,slots):
        x1,y1,x2,y2=box; sw,sh=x2-x1,y2-y1
        framed=fit_canvas(img,(sw,sh),bg=(250,251,253),margin=16)
        canvas.paste(framed,(x1,y1))
        d.rounded_rectangle([x1,y1,x2,y2],radius=12,outline=BORDER,width=2)
    canvas.save(dest,'PNG',optimize=True)


def xml_texts_from_docx(path):
    texts=[]; media=[]
    with zipfile.ZipFile(path) as z:
        if 'word/document.xml' in z.namelist():
            root=ET.fromstring(z.read('word/document.xml'))
            for el in root.iter():
                if el.tag.endswith('}t') and el.text and el.text.strip(): texts.append(el.text.strip())
        for n in z.namelist():
            if n.startswith('word/media/') and not n.endswith('/'):
                try: media.append((n,z.read(n)))
                except: pass
    return texts, media


def xml_texts_from_pptx(path):
    slides=[]; media=[]
    with zipfile.ZipFile(path) as z:
        snames=sorted([n for n in z.namelist() if re.fullmatch(r'ppt/slides/slide\d+\.xml',n)], key=lambda s:int(re.search(r'(\d+)',s).group(1)))
        for n in snames:
            root=ET.fromstring(z.read(n)); t=[]
            for el in root.iter():
                if el.tag.endswith('}t') and el.text and el.text.strip(): t.append(el.text.strip())
            slides.append(t)
        for n in z.namelist():
            if n.startswith('ppt/media/') and not n.endswith('/'):
                try: media.append((n,z.read(n)))
                except: pass
    return slides, media


def xlsx_texts(path):
    out=[]
    with zipfile.ZipFile(path) as z:
        shared=[]
        if 'xl/sharedStrings.xml' in z.namelist():
            root=ET.fromstring(z.read('xl/sharedStrings.xml'))
            for si in root:
                chunks=[]
                for el in si.iter():
                    if el.tag.endswith('}t') and el.text: chunks.append(el.text)
                shared.append(''.join(chunks))
        sheets=sorted([n for n in z.namelist() if n.startswith('xl/worksheets/sheet') and n.endswith('.xml')])
        for sn in sheets[:3]:
            root=ET.fromstring(z.read(sn)); vals=[]
            for c in root.iter():
                if not c.tag.endswith('}c'): continue
                typ=c.attrib.get('t')
                v=next((x for x in c if x.tag.endswith('}v')),None)
                if v is None or v.text is None: continue
                val=v.text
                if typ=='s':
                    try: val=shared[int(val)]
                    except: pass
                if str(val).strip(): vals.append(str(val).strip())
                if len(vals)>=80: break
            out.append(vals)
    return out


def text_preview(title, lines, page_no=1, image_blob=None):
    canvas=Image.new('RGB',(W,H),BG)
    d=ImageDraw.Draw(canvas)
    # document page
    d.rounded_rectangle([110,50,1090,850],radius=16,fill=WHITE,outline=BORDER,width=2)
    d.text((160,100), title[:75],font=font(34,True),fill=TEXT)
    d.text((160,148), f'Preview {page_no}',font=font(18),fill=MUTED)
    y=200
    if image_blob:
        try:
            import io
            im=Image.open(io.BytesIO(image_blob)).convert('RGB')
            im.thumbnail((380,240),Image.Resampling.LANCZOS)
            canvas.paste(im,(650,190))
        except: pass
    # wrap text manually
    f=font(22); maxchars=74
    for raw in lines:
        raw=re.sub(r'\s+',' ',raw).strip()
        if not raw: continue
        words=raw.split(); cur=''
        chunks=[]
        for word in words:
            if len(cur)+len(word)+1>maxchars:
                chunks.append(cur); cur=word
            else: cur=(cur+' '+word).strip()
        if cur: chunks.append(cur)
        for ch in chunks:
            if y>800: break
            d.text((160,y),ch,font=f,fill=TEXT); y+=34
        y+=8
        if y>800: break
    return canvas


def extract_media_images(media):
    import io
    imgs=[]
    for name,blob in media:
        try:
            im=Image.open(io.BytesIO(blob)).convert('RGB')
            if im.width>=120 and im.height>=80: imgs.append((name,im))
        except: pass
    return imgs


def build_source_samples(item, srcfile, outdir):
    ext=srcfile.suffix.lower(); samples=[]
    if ext=='.docx':
        texts,media=xml_texts_from_docx(srcfile); imgs=extract_media_images(media)
        chunks=[texts[i:i+18] for i in range(0,min(len(texts),54),18)] or [[item['title']]]
        while len(chunks)<3: chunks.append(chunks[-1])
        for i in range(3):
            blob=None
            if i<len(imgs):
                import io; b=io.BytesIO(); imgs[i][1].save(b,'PNG'); blob=b.getvalue()
            im=text_preview(item['title'],chunks[i],i+1,blob)
            p=outdir/f'sample-{i+1}.png'; im.save(p,'PNG',optimize=True); samples.append(p)
    elif ext=='.pptx':
        slides,media=xml_texts_from_pptx(srcfile); imgs=extract_media_images(media)
        pages=(slides[:3] or [[item['title']]])
        while len(pages)<3: pages.append(pages[-1])
        for i in range(3):
            blob=None
            if i<len(imgs):
                import io; b=io.BytesIO(); imgs[i][1].save(b,'PNG'); blob=b.getvalue()
            im=text_preview(item['title'],pages[i],i+1,blob)
            p=outdir/f'sample-{i+1}.png'; im.save(p,'PNG',optimize=True); samples.append(p)
    elif ext=='.xlsx':
        sheets=xlsx_texts(srcfile); pages=sheets[:3] or [[item['title']]]
        while len(pages)<3: pages.append(pages[-1])
        for i in range(3):
            im=text_preview(item['title'],pages[i][:35],i+1,None)
            p=outdir/f'sample-{i+1}.png'; im.save(p,'PNG',optimize=True); samples.append(p)
    return samples


def main():
    items=json.loads(DATA_FILE.read_text(encoding='utf-8'))
    changed=0
    for idx,item in enumerate(items,1):
        outdir=STORAGE/item['slug']; outdir.mkdir(parents=True,exist_ok=True)
        existing=[outdir/f'sample-{i}.png' for i in (1,2,3)]
        source_mode=item.get('gallery_status')=='generated-from-source' and all(p.exists() for p in existing)
        if source_mode:
            # Preserve content, normalize only.
            for p in existing:
                im=Image.open(p).convert('RGB')
                fit_canvas(im).save(p,'PNG',optimize=True)
            samples=existing
        else:
            rel=item.get('download_relative_path') or item.get('download_path')
            srcfile=ROOT/'storage/app/public'/rel
            if not srcfile.exists():
                # Attempt source path from public file path
                pp=str(item.get('public_file_path','')).replace('/storage/','')
                srcfile=ROOT/'storage/app/public'/pp
            samples=build_source_samples(item,srcfile,outdir) if srcfile.exists() else []
            if not samples and all(p.exists() for p in existing):
                for p in existing:
                    fit_canvas(Image.open(p).convert('RGB')).save(p,'PNG',optimize=True)
                samples=existing
        if samples:
            make_cover(samples,item['title'],outdir/'cover.png')
            item['gallery_status']='source-normalized' if source_mode else 'source-rebuilt'
            item['preview_type']='Source-specific normalized preview'
            item['media_width']=W; item['media_height']=H
            changed+=1
        print(f'[{idx:03}/{len(items)}] {item["slug"]}: {item.get("gallery_status")}')
    DATA_FILE.write_text(json.dumps(items,ensure_ascii=False,indent=2),encoding='utf-8')
    print(f'Updated {changed}/{len(items)} Office template media sets to {W}x{H}.')

if __name__=='__main__': main()
