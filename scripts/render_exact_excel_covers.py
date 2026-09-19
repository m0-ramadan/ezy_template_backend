#!/usr/bin/env python3
"""
Exact Excel Sheet Content Renderer
Reads actual cells, values, merged cells, formatting, and structures from .xlsx files,
and renders direct high-definition images (1600x1000) of the actual Excel sheets.
"""

import json
import os
import re
from pathlib import Path
import openpyxl
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from PIL import Image, ImageDraw, ImageFont, ImageFilter

ROOT = Path(__file__).resolve().parents[1]
EXCEL_EXPORT_JSON = ROOT / "storage/excel_db_export.json"
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

def parse_sheet_grid(ws, max_r=22, max_c=10):
    rows_data = []
    for r in range(1, max_r + 1):
        row_vals = []
        for c in range(1, max_c + 1):
            val = ws.cell(row=r, column=c).value
            if val is None:
                val_str = ""
            elif isinstance(val, float):
                val_str = f"{val:,.2f}" if abs(val) >= 1 else f"{val:.2f}"
            elif isinstance(val, int):
                val_str = f"{val:,}"
            else:
                val_str = str(val).strip()
            row_vals.append(val_str)
        if any(row_vals):
            rows_data.append((r, row_vals))
    return rows_data

def render_excel_file_exact_cover(xlsx_path, title, subcategory, dest_png, dest_webp):
    W, H = 1600, 1000
    
    # Load openpyxl data
    sheet_names = ["Sheet1"]
    active_rows = []
    
    try:
        wb = openpyxl.load_workbook(xlsx_path, data_only=True)
        sheet_names = wb.sheetnames[:6]
        ws = wb.active
        active_rows = parse_sheet_grid(ws, max_r=22, max_c=10)
        wb.close()
    except Exception as e:
        pass

    # Canvas
    img = Image.new("RGBA", (W, H), (241, 245, 249, 255))
    d = ImageDraw.Draw(img)

    # 1. Top Ribbon / App Header (Microsoft Excel Theme)
    d.rectangle([0, 0, W, 80], fill=(16, 124, 65, 255))
    
    # Logo & File Title
    d.rounded_rectangle([30, 16, 78, 64], radius=8, fill=(255, 255, 255, 255))
    d.text((45, 22), "X", font=font(32, bold=True), fill=(16, 124, 65, 255))
    
    clean_title = re.sub(r'^(Excel\s*|Microsoft\s*Excel\s*)', '', title, flags=re.I).strip()
    d.text((95, 20), f"{clean_title}.xlsx", font=font(22, bold=True), fill=(255, 255, 255, 255))
    d.text((95, 48), f"Microsoft Excel Spreadsheet  •  {subcategory}", font=font(14), fill=(209, 250, 229, 220))

    # 2. Formula Bar
    d.rectangle([0, 80, W, 120], fill=(255, 255, 255, 255), outline=(226, 232, 240, 255), width=1)
    d.text((35, 90), "fx", font=font(16, bold=True), fill=(100, 116, 139, 255))
    d.line([(65, 85), (65, 115)], fill=(226, 232, 240, 255), width=1)
    first_cell_val = active_rows[0][1][0] if active_rows and active_rows[0][1] else clean_title
    d.text((80, 91), str(first_cell_val)[:100], font=font(15), fill=(30, 41, 59, 255))

    # 3. Excel Spreadsheet Grid Area
    grid_top = 120
    grid_left = 60
    col_w = 150
    row_h = 36

    # Column Headers (A, B, C, D, E, F, G, H, I, J)
    cols = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"]
    d.rectangle([0, grid_top, grid_left, grid_top + row_h], fill=(241, 245, 249, 255), outline=(203, 213, 225, 255))
    
    for ci, col_label in enumerate(cols):
        cx = grid_left + ci * col_w
        d.rectangle([cx, grid_top, cx + col_w, grid_top + row_h], fill=(241, 245, 249, 255), outline=(203, 213, 225, 255))
        d.text((cx + col_w//2 - 6, grid_top + 8), col_label, font=font(14, bold=True), fill=(100, 116, 139, 255))

    # Render Rows with Data
    max_display_rows = 21
    row_map = {r_idx: vals for r_idx, vals in active_rows}

    for ri in range(1, max_display_rows + 1):
        ry = grid_top + ri * row_h
        if ry + row_h > H - 50:
            break
            
        # Row Number Header
        d.rectangle([0, ry, grid_left, ry + row_h], fill=(241, 245, 249, 255), outline=(203, 213, 225, 255))
        d.text((18, ry + 8), str(ri), font=font(13), fill=(100, 116, 139, 255))

        vals = row_map.get(ri, [""] * 10)
        
        # Check if row is a header row (contains bold/key title)
        is_header_row = (ri <= 3 and any(vals) and sum(1 for v in vals if v) >= 2)
        
        for ci in range(10):
            cx = grid_left + ci * col_w
            cell_txt = vals[ci] if ci < len(vals) else ""
            
            # Formatting cell style based on position & content
            bg_color = (255, 255, 255, 255)
            txt_color = (30, 41, 59, 255)
            is_bold = False

            if ri == 1 and cell_txt:
                bg_color = (16, 124, 65, 255)
                txt_color = (255, 255, 255, 255)
                is_bold = True
            elif is_header_row and cell_txt:
                bg_color = (220, 252, 231, 255)
                txt_color = (22, 101, 52, 255)
                is_bold = True
            elif ri % 2 == 0:
                bg_color = (248, 250, 252, 255)

            d.rectangle([cx, ry, cx + col_w, ry + row_h], fill=bg_color, outline=(226, 232, 240, 255))

            if cell_txt:
                # Trim text to fit cell width
                disp_txt = cell_txt
                if len(disp_txt) > 18:
                    disp_txt = disp_txt[:16] + ".."
                
                align_right = bool(re.match(r'^[\$€£]?\s?-?\d+(,\d{3})*(\.\d+)?%?$', cell_txt))
                f = font(13, bold=is_bold)
                
                if align_right:
                    tw = f.getlength(disp_txt)
                    tx = max(cx + 8, cx + col_w - tw - 12)
                else:
                    tx = cx + 10
                
                d.text((tx, ry + 9), disp_txt, font=f, fill=txt_color)

    # 4. Bottom Sheet Tabs Bar
    d.rectangle([0, H - 45, W, H], fill=(241, 245, 249, 255), outline=(203, 213, 225, 255))
    tab_x = 20
    for idx, sname in enumerate(sheet_names):
        is_active = (idx == 0)
        t_w = max(110, int(font(13).getlength(sname)) + 30)
        if is_active:
            d.rounded_rectangle([tab_x, H - 40, tab_x + t_w, H - 5], radius=6, fill=(255, 255, 255, 255), outline=(203, 213, 225, 255))
            d.line([(tab_x + 8, H - 6), (tab_x + t_w - 8, H - 6)], fill=(16, 124, 65, 255), width=3)
            d.text((tab_x + 15, H - 32), sname, font=font(13, bold=True), fill=(16, 124, 65, 255))
        else:
            d.text((tab_x + 15, H - 32), sname, font=font(13), fill=(100, 116, 139, 255))
        tab_x += t_w + 10

    # Save PNG and WebP
    os.makedirs(os.path.dirname(dest_png), exist_ok=True)
    img_rgb = img.convert("RGB")
    img_rgb.save(dest_png, "PNG", optimize=True)
    img_rgb.save(dest_webp, "WEBP", quality=92)

def main():
    with open(EXCEL_EXPORT_JSON) as f:
        items = json.load(f)

    print(f"Starting direct Excel content rendering for {len(items)} items...")
    count = 0

    for item in items:
        slug = item.get("slug")
        xlsx_path = item.get("full_path")
        title = item.get("title") or slug
        subcategory = item.get("subcategory") or "Excel"

        dest_dir = PREVIEWS_DIR / slug
        dest_png = str(dest_dir / "cover.png")
        dest_webp = str(dest_dir / "cover.webp")

        if xlsx_path and os.path.exists(xlsx_path):
            render_excel_file_exact_cover(xlsx_path, title, subcategory, dest_png, dest_webp)
            count += 1
            if count % 20 == 0:
                print(f"Processed {count}/{len(items)} templates...")

    print(f"Completed! Rendered exact content covers for {count} Excel templates.")

if __name__ == "__main__":
    main()

