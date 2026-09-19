#!/usr/bin/env python3
"""
Exact Word (.docx) Document Content Renderer
Reads actual paragraphs, headers, tables, cells, and metadata from .docx files
and renders 1600x1000 high-definition document mockup covers (cover.png / cover.webp).
"""

import json
import os
import re
from pathlib import Path
import docx
from PIL import Image, ImageDraw, ImageFont, ImageFilter

ROOT = Path(__file__).resolve().parents[1]
STORAGE_PUBLIC = ROOT / "storage/app/public"
PREVIEWS_DIR = STORAGE_PUBLIC / "template-previews"

FONT_BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
FONT_REGULAR = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"

def font(size=20, bold=False):
    p = FONT_BOLD if bold else FONT_REGULAR
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

def parse_docx_content(docx_path):
    out = {
        "title": "",
        "paragraphs": [],
        "table_headers": [],
        "table_rows": [],
    }
    if not docx_path or not os.path.exists(docx_path):
        return out

    try:
        doc = docx.Document(docx_path)
        paras = [p.text.strip() for p in doc.paragraphs if p.text.strip()]
        if paras:
            out["title"] = paras[0]
            out["paragraphs"] = paras[1:12]

        if doc.tables:
            table = doc.tables[0]
            headers = [c.text.strip() for c in table.rows[0].cells if c.text.strip()]
            out["table_headers"] = headers[:6]
            rows = []
            for r in table.rows[1:8]:
                r_vals = [c.text.strip() for c in r.cells if c.text.strip()]
                if r_vals:
                    rows.append(r_vals[:6])
            out["table_rows"] = rows
    except Exception as e:
        print(f"Error parsing {docx_path}: {e}")

    return out

def render_word_file_exact_cover(docx_path, slug, title, category, dest_png, dest_webp):
    W, H = 1600, 1000
    content = parse_docx_content(docx_path)

    # Base Background Canvas (Microsoft Word Blue Theme)
    img = Image.new("RGBA", (W, H), (241, 245, 249, 255))
    d = ImageDraw.Draw(img)

    # 1. Top Ribbon / Word Header
    d.rectangle([0, 0, W, 80], fill=(24, 90, 189, 255)) # Word Blue Color #185abd

    # Logo & File Title
    d.rounded_rectangle([30, 16, 78, 64], radius=8, fill=(255, 255, 255, 255))
    d.text((43, 22), "W", font=font(32, bold=True), fill=(24, 90, 189, 255))

    clean_title = re.sub(r'^(Word\s*|Microsoft\s*Word\s*)', '', title, flags=re.I).strip()
    d.text((95, 20), f"{clean_title}.docx", font=font(22, bold=True), fill=(255, 255, 255, 255))
    d.text((95, 48), f"Microsoft Word Document  •  {category}", font=font(14), fill=(219, 234, 254, 220))

    # Document Ruler Bar
    d.rectangle([0, 80, W, 110], fill=(248, 250, 252, 255), outline=(226, 232, 240, 255))
    d.text((40, 88), "Ruler: 1.0  •  1.5  •  2.0  •  2.5  •  3.0  •  3.5  •  4.0  •  4.5  •  5.0  •  5.5  •  6.0  •  6.5  •  7.0", font=font(12), fill=(148, 163, 184, 255))

    # 2. Centered A4 Document Page Canvas
    page_w, page_h = 960, 840
    page_x = (W - page_w) // 2
    page_y = 130

    # Page Drop Shadow
    shadow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    sd = ImageDraw.Draw(shadow)
    sd.rounded_rectangle([page_x+8, page_y+10, page_x+page_w+8, page_y+page_h+10], radius=8, fill=(0, 0, 0, 90))
    shadow = shadow.filter(ImageFilter.GaussianBlur(12))
    img = Image.alpha_composite(img, shadow)
    d = ImageDraw.Draw(img)

    # Main White Paper Sheet
    d.rounded_rectangle([page_x, page_y, page_x+page_w, page_y+page_h], radius=6, fill=(255, 255, 255, 255), outline=(203, 213, 225, 255), width=1)

    # Document Margin Lines & Content Rendering inside paper
    m_left = page_x + 70
    m_top = page_y + 60
    m_right = page_x + page_w - 70

    curr_y = m_top

    # Main Title Header inside Page
    doc_main_title = content["title"] or clean_title.upper()
    d.text((m_left, curr_y), doc_main_title[:55], font=font(26, bold=True), fill=(30, 41, 59, 255))
    curr_y += 40
    d.line([(m_left, curr_y), (m_right, curr_y)], fill=(37, 99, 235, 255), width=3)
    curr_y += 24

    # Paragraphs Rendering
    paras = content["paragraphs"]
    if not paras:
        paras = [
            "This document is a fully customizable Microsoft Word template professionally formatted for commercial and personal use.",
            "All headings, paragraphs, and tables retain their original structure and styling for instant editing and data replacement."
        ]

    for p in paras[:6]:
        if curr_y > page_y + page_h - 180:
            break
        # Wrap paragraph text
        words = p.split()
        curr_line = ""
        for w in words:
            if font(14).getlength(curr_line + " " + w) > (m_right - m_left):
                d.text((m_left, curr_y), curr_line, font=font(14), fill=(71, 85, 105, 255))
                curr_y += 24
                curr_line = w
            else:
                curr_line = (curr_line + " " + w).strip()
        if curr_line:
            d.text((m_left, curr_y), curr_line, font=font(14), fill=(71, 85, 105, 255))
            curr_y += 28

    # Table Rendering inside document if table exists
    if content["table_headers"]:
        curr_y += 10
        headers = content["table_headers"]
        col_count = len(headers)
        table_w = m_right - m_left
        col_w = table_w // col_count if col_count else table_w

        # Table Header Row
        d.rectangle([m_left, curr_y, m_right, curr_y + 32], fill=(24, 90, 189, 255))
        for idx, h_text in enumerate(headers):
            d.text((m_left + idx * col_w + 10, curr_y + 6), h_text[:15], font=font(13, bold=True), fill=(255, 255, 255, 255))
        curr_y += 32

        # Table Data Rows
        for r_idx, r_data in enumerate(content["table_rows"][:5]):
            if curr_y + 30 > page_y + page_h - 50:
                break
            bg_c = (248, 250, 252, 255) if r_idx % 2 == 0 else (255, 255, 255, 255)
            d.rectangle([m_left, curr_y, m_right, curr_y + 30], fill=bg_c, outline=(226, 232, 240, 255))
            for idx, cell_txt in enumerate(r_data[:col_count]):
                d.text((m_left + idx * col_w + 10, curr_y + 6), cell_txt[:16], font=font(13), fill=(30, 41, 59, 255))
            curr_y += 30

    # Footer Page Number
    d.text((page_x + page_w // 2 - 25, page_y + page_h - 35), "Page 1 of 1", font=font(12), fill=(148, 163, 184, 255))

    # Save PNG and WebP
    os.makedirs(os.path.dirname(dest_png), exist_ok=True)
    img_rgb = img.convert("RGB")
    img_rgb.save(dest_png, "PNG", optimize=True)
    img_rgb.save(dest_webp, "WEBP", quality=92)

def main():
    docx_files = [
        ("word-business-financial-planning-financial-management-plan-template", "templates/business-financial-planning/word-business-financial-planning-financial-management-plan-template/word-business-financial-planning-financial-management-plan-template.docx", "Financial Management Plan Template", "Business Financial Planning"),
        ("word-finance-business-financial-plan", "templates/finance/word-finance-business-financial-plan/word-finance-business-financial-plan.docx", "Business Financial Plan", "Finance"),
        ("word-personal-financial-planning-investment-planning-template", "templates/personal-financial-planning/word-personal-financial-planning-investment-planning-template/word-personal-financial-planning-investment-planning-template.docx", "Investment Planning Template", "Personal Financial Planning"),
        ("word-project-management-change-request-template", "templates/project-management/word-project-management-change-request-template/word-project-management-change-request-template.docx", "Change Request Template", "Project Management"),
        ("word-project-management-project-plan-template", "templates/project-management/word-project-management-project-plan-template/word-project-management-project-plan-template.docx", "Project Plan Template", "Project Management"),
    ]

    print(f"Starting Word document covers rendering for {len(docx_files)} files...")
    for slug, rel_path, title, category in docx_files:
        full_path = str(STORAGE_PUBLIC / rel_path)
        dest_dir = PREVIEWS_DIR / slug
        dest_png = str(dest_dir / "cover.png")
        dest_webp = str(dest_dir / "cover.webp")

        if os.path.exists(full_path):
            render_word_file_exact_cover(full_path, slug, title, category, dest_png, dest_webp)
            print(f"Rendered Word cover for: {slug}")
        else:
            print(f"File not found: {full_path}")

    print("Completed Word covers generation.")

if __name__ == "__main__":
    main()

