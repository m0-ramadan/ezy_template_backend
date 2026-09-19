#!/usr/bin/env python3
from __future__ import annotations

import json, re, os, shutil, hashlib
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
WORK = ROOT / 'storage/work/import'
SRC_EXCEL = ROOT.parents[1] / 'data_ezytemplate/work/excel_src'
DEST_ROOT = ROOT / 'storage/app/public/templates'
PREVIEW_ROOT = ROOT / 'storage/app/public/template-previews'

SUBCAT_EN = {
    'excel-accounting': 'accounting',
    'excel-business-financial-planning': 'business financial planning',
    'excel-finance': 'finance',
    'excel-human-resources': 'human resources',
    'excel-inventory': 'inventory management',
    'excel-personal-financial-planning': 'personal financial planning',
    'excel-project-management': 'project management',
    'excel-social-media-marketing': 'social media marketing',
    'excel-sales-reporting': 'sales reporting and analysis',
}

SUBCAT_ID = {
    'excel-accounting': 89,
    'excel-business-financial-planning': 90,
    'excel-finance': 91,
    'excel-human-resources': 92,
    'excel-inventory': 93,
    'excel-personal-financial-planning': 94,
    'excel-project-management': 95,
    'excel-social-media-marketing': 96,
    'excel-sales-reporting': 102,
}

SUBCAT_DIR = {
    'excel-accounting': 'accounting',
    'excel-business-financial-planning': 'business-financial-planning',
    'excel-finance': 'finance',
    'excel-human-resources': 'human-resources',
    'excel-inventory': 'inventory',
    'excel-personal-financial-planning': 'personal-financial-planning',
    'excel-project-management': 'project-management',
    'excel-social-media-marketing': 'social-media-marketing',
    'excel-sales-reporting': 'sales-reporting',
}

SUBCAT_AR = {
    'excel-accounting': 'المحاسبة',
    'excel-business-financial-planning': 'التخطيط المالي للأعمال',
    'excel-finance': 'التمويل',
    'excel-human-resources': 'الموارد البشرية',
    'excel-inventory': 'المخزون',
    'excel-personal-financial-planning': 'التخطيط المالي الشخصي',
    'excel-project-management': 'إدارة المشروعات',
    'excel-social-media-marketing': 'التسويق عبر وسائل التواصل',
    'excel-sales-reporting': 'تقارير المبيعات',
}


def slugify(title: str) -> str:
    s = re.sub(r'[^\w\s-]', '', title.lower().replace('&', 'and'))
    s = re.sub(r'[\s_]+', '-', s.strip())
    s = re.sub(r'-+', '-', s)
    return s.strip('-')


def title_human(fn: str) -> str:
    base = fn.rsplit('.', 1)[0]
    base = base.replace('_', ' ').replace('-', ' ')
    base = re.sub(r'(?i)\b(v1\.\d|v\d+|xlsm|xls|xlsx)\b', '', base)
    base = re.sub(r'^\d+[\.\s]+', '', base).strip()
    base = base.replace('  ', ' ').strip(' .-')
    base = base.title() if base and not any(ch.islower() for ch in base[:4]) else base
    return base


def ar_desc(subcat, sheet_names, labels):
    sheets = '، '.join(sheet_names[:4]) if sheet_names else 'ورقة/أوراق العمل الرئيسية'
    labels_txt = '، '.join([str(x) for x in labels[:8]]) if labels else 'حقول قابلة للتعديل'
    return (
        f'قالب Excel جاهز للاستخدام ضمن قسم {SUBCAT_AR.get(subcat, "قوالب Excel")}، '
        f'يحتوي على أوراق عمل منظمة ({sheets}) وحقول واضحة ({labels_txt}). '
        'لم يتم أي عرض كامل للتنسيق الأصلي، لذلك تُعرض لقطات فعلية توضح البنية والمحتوى الحقيقي. '
        'استبدل البيانات التجريبية ببياناتك واستخدم القالب مباشرة.'
    )


def en_desc(title, subcat, sheet_names, labels):
    sheets = ', '.join(sheet_names[:4]) if sheet_names else 'core worksheets'
    labels_txt = ', '.join([str(x) for x in labels[:8]]) if labels else 'editable fields'
    return (
        f'{title} is a ready-to-use Microsoft Excel template for {SUBCAT_EN.get(subcat, "spreadsheet tasks")}. '
        f'It contains organized worksheets ({sheets}) with visible fields including {labels_txt}. '
        'The original workbook structure is preserved; replace the sample data with your own and customize freely.'
    )


def load_manifest():
    with open(WORK / 'new_office_manifest_v2.json', encoding='utf-8') as f:
        return json.load(f)


def find_source(filename):
    for root, _, fs in os.walk(SRC_EXCEL):
        if filename in fs:
            return Path(root) / filename
    return None


def main():
    manifest = load_manifest()
    records = []
    errors = []
    used_slugs = set()
    for m in manifest:
        primary = m['primary']
        if m.get('size', 0) < 1000:
            errors.append(f'{primary}: source file is empty/corrupt ({m.get("size")} bytes), skipping')
            continue
        src = find_source(primary)
        if src is None:
            errors.append(f'{primary}: source not found under {SRC_EXCEL}')
            continue
        subcat = m['subcategory_slug']
        subcat_id = m['subcategory_id']
        title = title_human(primary)
        slug = slugify(f'excel-{subcat.replace("excel-","")}-{title}')
        # ensure uniqueness
        n = 2
        while slug in used_slugs:
            slug = slugify(f'excel-{subcat.replace("excel-","")}-{title}-{n}')
            n += 1
        used_slugs.add(slug)

        dirname = SUBCAT_DIR.get(subcat, 'business-financial-planning')
        rel_dir = Path('templates') / dirname / slug
        dest_dir = DEST_ROOT / dirname / slug
        dest_dir.mkdir(parents=True, exist_ok=True)
        ext = m['ext'] or 'xlsx'
        target_name = f'{slug}.{ext}'
        dest_file = dest_dir / target_name

        labels = m.get('top_labels', []) or []
        sheet_names = m.get('sheets', []) or []

        if labels and sheet_names:
            pass  # sanitize below
        labels_clean = [str(x)[:80] for x in labels]
        sheets_clean = [str(x)[:80] for x in sheet_names]

        description = en_desc(title, subcat, sheets_clean, labels_clean)
        description_ar = ar_desc(subcat, sheets_clean, labels_clean)

        records.append({
            'title': title,
            'title_ar': f'قالب {title}',
            'slug': slug,
            'resource_type': 'excel',
            'category_id': 34,
            'main_category_id': 2,
            'subcategory_id': subcat_id,
            'tech_stack': 'Microsoft Excel / XLSX',
            'tech_stack_ar': 'Microsoft Excel / XLSX',
            'description': description,
            'description_ar': description_ar,
            'short_description': description[:150],
            'short_description_ar': description_ar[:150],
            'version': '1.0.0',
            'status': 'published',
            'published_at': 'NOW()',
            'license_type': 'free',
            'license': 'Source license not specified; confirm redistribution and commercial-use rights before public release.',
            'is_free': 1,
            'price': 0,
            'src': str(src),
            'dest': str(dest_file),
            'rel_path': f'{rel_dir}/{target_name}',
            'public_path': f'/storage/{rel_dir}/{target_name}',
            'original_name': primary,
            'ext': ext,
            'size_bytes': m.get('size', 0),
            'sheet_names': sheets_clean,
            'top_labels': labels_clean,
            'preview_dir': f'{PREVIEW_ROOT}/{slug}',
        })
    out = {
        'generated_at': '2026-09-16T00:00:00Z',
        'records': records,
        'errors': errors,
    }
    with open(WORK / 'final_import_plan.json', 'w', encoding='utf-8') as f:
        json.dump(out, f, ensure_ascii=False, indent=2)
    print(f'records: {len(records)}')
    print(f'errors: {len(errors)}')
    for e in errors:
        print('  -', e)
    # summary by subcategory
    from collections import Counter
    c = Counter(r['subcategory_id'] for r in records)
    print('by subcat_id:', dict(c))


if __name__ == '__main__':
    main()