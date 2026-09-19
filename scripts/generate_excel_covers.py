#!/usr/bin/env python3
"""
Professional Excel Cover Generator (1600x1000) for EzyTemplate
Inspects actual .xlsx contents (sheet names, column headers, cell data),
extracts category and template info, and generates unique, high-fidelity
Excel Dashboard mockup covers with perspective laptop/window layout,
KPI cards, actual table columns & rows, charts, Excel branding, and typography.
"""

import json
import re
import os
import shutil
import math
from pathlib import Path
import openpyxl
from PIL import Image, ImageDraw, ImageFont, ImageFilter

ROOT = Path(__file__).resolve().parents[1]
EXCEL_EXPORT_JSON = ROOT / "storage/excel_db_export.json"
STORAGE_PUBLIC = ROOT / "storage/app/public"
PREVIEWS_DIR = STORAGE_PUBLIC / "template-previews"
BACKUP_DIR = STORAGE_PUBLIC / "template-previews-backup-covers"

W, H = 1600, 1000

# Fonts
FONT_BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
FONT_REGULAR = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"

def font(size=24, bold=False):
    p = FONT_BOLD if bold else FONT_REGULAR
    try:
        return ImageFont.truetype(p, size)
    except Exception:
        return ImageFont.load_default()

# Color Palettes per Category
THEMES = {
    "accounting": {
        "bg_dark": (4, 47, 38), "bg_light": (6, 78, 59),
        "primary": (16, 185, 129), "accent": (52, 211, 153),
        "kpi1": (16, 185, 129), "kpi2": (59, 130, 246), "kpi3": (245, 158, 11),
        "badge_bg": (6, 95, 70), "badge_text": (167, 243, 208)
    },
    "sales-reporting": {
        "bg_dark": (6, 44, 34), "bg_light": (4, 120, 87),
        "primary": (34, 197, 94), "accent": (74, 222, 128),
        "kpi1": (34, 197, 94), "kpi2": (14, 165, 233), "kpi3": (168, 85, 247),
        "badge_bg": (20, 83, 45), "badge_text": (187, 247, 208)
    },
    "project-management": {
        "bg_dark": (15, 42, 54), "bg_light": (13, 148, 136),
        "primary": (20, 184, 166), "accent": (94, 234, 212),
        "kpi1": (20, 184, 166), "kpi2": (245, 158, 11), "kpi3": (239, 68, 68),
        "badge_bg": (19, 78, 74), "badge_text": (153, 246, 228)
    },
    "human-resources": {
        "bg_dark": (20, 50, 40), "bg_light": (16, 185, 129),
        "primary": (16, 185, 129), "accent": (110, 231, 183),
        "kpi1": (16, 185, 129), "kpi2": (99, 102, 241), "kpi3": (236, 72, 153),
        "badge_bg": (6, 95, 70), "badge_text": (209, 250, 229)
    },
    "inventory": {
        "bg_dark": (24, 55, 45), "bg_light": (5, 150, 105),
        "primary": (16, 185, 129), "accent": (52, 211, 153),
        "kpi1": (245, 158, 11), "kpi2": (16, 185, 129), "kpi3": (59, 130, 246),
        "badge_bg": (6, 95, 70), "badge_text": (167, 243, 208)
    },
    "personal-financial-planning": {
        "bg_dark": (10, 45, 40), "bg_light": (4, 120, 87),
        "primary": (34, 197, 94), "accent": (134, 239, 172),
        "kpi1": (34, 197, 94), "kpi2": (59, 130, 246), "kpi3": (236, 72, 153),
        "badge_bg": (20, 83, 45), "badge_text": (187, 247, 208)
    },
    "default": {
        "bg_dark": (6, 44, 34), "bg_light": (4, 120, 87),
        "primary": (16, 185, 129), "accent": (52, 211, 153),
        "kpi1": (16, 185, 129), "kpi2": (59, 130, 246), "kpi3": (245, 158, 11),
        "badge_bg": (6, 95, 70), "badge_text": (167, 243, 208)
    }
}

def get_theme(subcategory_slug):
    slug = (subcategory_slug or "").lower()
    for k in THEMES:
        if k in slug:
            return THEMES[k]
    return THEMES["default"]

def read_excel_content(file_path):
    """
    Parses openpyxl file safely and extracts title, sheets, headers, and sample data rows.
    """
    out = {
        "sheets": ["Sheet1"],
        "headers": [],
        "rows": [],
        "kpis": [],
        "has_data": False
    }
    if not file_path or not os.path.exists(file_path):
        return out

    try:
        wb = openpyxl.load_workbook(file_path, read_only=True, data_only=True)
        out["sheets"] = wb.sheetnames[:5]
        ws = wb.active
        
        extracted_rows = []
        for r in ws.iter_rows(max_row=25, max_col=12, values_only=True):
            clean_r = [str(c).strip() if c is not None else "" for c in r]
            if any(clean_r):
                extracted_rows.append(clean_r)
        wb.close()

        if extracted_rows:
            out["has_data"] = True
            # Find best header row (row with most non-empty text cells)
            header_idx = 0
            max_non_empty = 0
            for idx, r in enumerate(extracted_rows[:6]):
                non_empty = sum(1 for c in r if c and len(c) > 1 and not re.match(r'^[\d.,$%]+$', c))
                if non_empty > max_non_empty:
                    max_non_empty = non_empty
                    header_idx = idx

            out["headers"] = [c for c in extracted_rows[header_idx] if c][:8]
            out["rows"] = extracted_rows[header_idx+1:header_idx+10]

            # Try to extract KPI numbers
            for r in extracted_rows:
                for c in r:
                    if re.search(r'[\$€£]?\s?\d{1,3}(,\d{3})+(\.\d+)?', c) or re.search(r'^\d+(\.\d+)?%', c):
                        if c not in out["kpis"] and len(c) < 15:
                            out["kpis"].append(c)
                            if len(out["kpis"]) >= 3:
                                break
                if len(out["kpis"]) >= 3:
                    break

    except Exception as e:
        pass

    return out

def draw_rounded_rect(draw, box, radius, fill, outline=None, width=1):
    x1, y1, x2, y2 = box
    draw.rounded_rectangle([x1, y1, x2, y2], radius=radius, fill=fill, outline=outline, width=width)

def draw_excel_logo(draw, x, y, size=40):
    # Excel green icon box with 3D shadow effect
    draw.rounded_rectangle([x, y, x+size, y+size], radius=8, fill=(16, 124, 65))
    draw.rounded_rectangle([x+size//4, y+size//4, x+size+4, y+size+4], radius=4, fill=(16, 124, 65, 100))
    f = font(int(size * 0.65), bold=True)
    draw.text((x + size*0.25, y + size*0.12), "X", font=f, fill=(255, 255, 255))

def generate_cover_image(item, content, dest_png, dest_webp):
    title = item.get("title") or "Excel Template"
    subcategory = item.get("subcategory") or item.get("category") or "Spreadsheets"
    sub_slug = item.get("subcategory_slug") or ""
    theme = get_theme(sub_slug)

    # Base Background Canvas
    img = Image.new("RGBA", (W, H), (15, 23, 42, 255))
    d = ImageDraw.Draw(img)

    # Gradient Background (Deep Emerald/Teal to Slate Dark)
    c1, c2 = theme["bg_dark"], theme["bg_light"]
    for y in range(H):
        ratio = y / H
        r = int(c1[0] * (1 - ratio) + c2[0] * ratio)
        g = int(c1[1] * (1 - ratio) + c2[1] * ratio)
        b = int(c1[2] * (1 - ratio) + c2[2] * ratio)
        d.line([(0, y), (W, y)], fill=(r, g, b, 255))

    # Abstract Graphic Patterns (Grid dots & Glowing Orbs)
    overlay = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    od = ImageDraw.Draw(overlay)
    
    # Glowing Orbs
    od.ellipse([1000, -100, 1700, 600], fill=(theme["primary"][0], theme["primary"][1], theme["primary"][2], 30))
    od.ellipse([-200, 500, 500, 1200], fill=(theme["accent"][0], theme["accent"][1], theme["accent"][2], 25))

    # Subtle Grid Dots
    for gx in range(60, W, 40):
        for gy in range(60, H, 40):
            od.ellipse([gx, gy, gx+2, gy+2], fill=(255, 255, 255, 18))
    img = Image.alpha_composite(img, overlay)
    d = ImageDraw.Draw(img)

    # TOP HEADER & BRANDING
    # Excel Badge
    draw_excel_logo(d, 80, 60, size=48)
    d.text((142, 66), "Microsoft Excel Template", font=font(20, bold=True), fill=(255, 255, 255, 230))
    d.text((142, 92), "100% Fully Editable & Automated Spreadsheet", font=font(14), fill=(167, 243, 208, 200))

    # Category Badge Pill
    cat_text = subcategory.upper()
    cat_w = font(12, bold=True).getlength(cat_text) + 24
    draw_rounded_rect(d, [W - 80 - cat_w, 64, W - 80, 94], radius=15, fill=theme["badge_bg"], outline=theme["primary"], width=1)
    d.text((W - 80 - cat_w + 12, 72), cat_text, font=font(12, bold=True), fill=theme["badge_text"])

    # TEMPLATE TITLE
    # Smart multi-line wrap
    clean_title = re.sub(r'^(Excel\s*|Microsoft\s*Excel\s*)', '', title, flags=re.I).strip()
    words = clean_title.split()
    lines = []
    curr = ""
    for w in words:
        if len(curr + " " + w) > 35:
            lines.append(curr)
            curr = w
        else:
            curr = (curr + " " + w).strip()
    if curr:
        lines.append(curr)
    
    title_display = "\n".join(lines[:2])
    d.multiline_text((80, 140), title_display, font=font(38, bold=True), fill=(255, 255, 255, 255), spacing=8)

    # MAIN WINDOW / DASHBOARD MOCKUP CONTAINER
    # Window Frame Bounds
    win_x, win_y = 80, 240
    win_w, win_h = 1440, 700

    # Shadow layer
    shadow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    sd = ImageDraw.Draw(shadow)
    sd.rounded_rectangle([win_x+12, win_y+16, win_x+win_w+12, win_y+win_h+16], radius=20, fill=(0, 0, 0, 140))
    shadow = shadow.filter(ImageFilter.GaussianBlur(16))
    img = Image.alpha_composite(img, shadow)
    d = ImageDraw.Draw(img)

    # Main Window Container
    draw_rounded_rect(d, [win_x, win_y, win_x+win_w, win_y+win_h], radius=16, fill=(248, 250, 252, 255), outline=(203, 213, 225, 255), width=2)

    # EXCEL RIBBON & TITLE BAR
    draw_rounded_rect(d, [win_x, win_y, win_x+win_w, win_y+46], radius=14, fill=(16, 124, 65, 255))
    # Window Control Buttons (Red, Yellow, Green)
    d.ellipse([win_x+18, win_y+16, win_x+30, win_y+28], fill=(239, 68, 68, 255))
    d.ellipse([win_x+36, win_y+16, win_x+48, win_y+28], fill=(245, 158, 11, 255))
    d.ellipse([win_x+54, win_y+16, win_x+66, win_y+28], fill=(34, 197, 94, 255))
    d.text((win_x+84, win_y+14), f"Book1.xlsx - Excel  [{clean_title}]", font=font(14, bold=True), fill=(255, 255, 255, 230))

    # Ribbon Tabs Bar
    draw_rounded_rect(d, [win_x, win_y+46, win_x+win_w, win_y+82], radius=0, fill=(241, 245, 249, 255))
    tabs_list = ["File", "Home", "Insert", "Page Layout", "Formulas", "Data", "Review", "View"]
    tx = win_x + 20
    for idx, tb in enumerate(tabs_list):
        tb_w = font(13, bold=(idx==1)).getlength(tb) + 20
        if idx == 1: # Home active
            draw_rounded_rect(d, [tx, win_y+48, tx+tb_w, win_y+82], radius=4, fill=(255, 255, 255, 255))
            d.text((tx+10, win_y+58), tb, font=font(13, bold=True), fill=(16, 124, 65, 255))
        else:
            d.text((tx+10, win_y+58), tb, font=font(13), fill=(71, 85, 105, 255))
        tx += tb_w + 5

    # Formula Bar
    draw_rounded_rect(d, [win_x, win_y+82, win_x+win_w, win_y+114], radius=0, fill=(255, 255, 255, 255), outline=(226, 232, 240, 255), width=1)
    draw_rounded_rect(d, [win_x+10, win_y+87, win_x+70, win_y+109], radius=4, fill=(241, 245, 249, 255), outline=(203, 213, 225, 255))
    d.text((win_x+25, win_y+91), "A1", font=font(12, bold=True), fill=(71, 85, 105, 255))
    d.text((win_x+80, win_y+91), "fx", font=font(13, bold=True), fill=(148, 163, 184, 255))
    d.text((win_x+110, win_y+91), f"=SUM(B4:B18) // {clean_title} Dashboard", font=font(12), fill=(100, 116, 139, 255))

    # INNER WORKSPACE LAYOUT (KPI Cards on Top + Excel Table & Chart Side-by-Side)
    content_y = win_y + 125

    # 1. KPI CARDS (3 Cards Across Top of Workspace)
    kpi_w = (win_w - 60) // 3
    kpis_data = [
        {"title": "TOTAL REVENUE / VALUE", "val": content["kpis"][0] if len(content["kpis"])>0 else "$128,450.00", "color": theme["kpi1"], "sub": "+14.2% vs target"},
        {"title": "ACTIVE ITEMS / TASKS", "val": content["kpis"][1] if len(content["kpis"])>1 else "1,420 Units", "color": theme["kpi2"], "sub": "98.5% Completion Rate"},
        {"title": "PERFORMANCE / EFFICIENCY", "val": content["kpis"][2] if len(content["kpis"])>2 else "94.8%", "color": theme["kpi3"], "sub": "Optimal Range"}
    ]

    for i, kpi in enumerate(kpis_data):
        kx = win_x + 20 + i * (kpi_w + 10)
        ky = content_y
        draw_rounded_rect(d, [kx, ky, kx+kpi_w, ky+95], radius=10, fill=(255, 255, 255, 255), outline=(226, 232, 240, 255), width=1)
        # Left Accent Border Strip
        draw_rounded_rect(d, [kx, ky, kx+6, ky+95], radius=6, fill=kpi["color"])
        d.text((kx+18, ky+12), kpi["title"], font=font(11, bold=True), fill=(100, 116, 139, 255))
        d.text((kx+18, ky+32), kpi["val"], font=font(22, bold=True), fill=(15, 23, 42, 255))
        d.text((kx+18, ky+65), kpi["sub"], font=font(11), fill=(34, 197, 94, 255) if "+" in kpi["sub"] else (71, 85, 105, 255))

    # 2. MAIN TABLE & CHART SECTION
    main_y = content_y + 110
    tbl_w = 880
    tbl_h = win_y + win_h - main_y - 45

    # Table Container
    draw_rounded_rect(d, [win_x+20, main_y, win_x+20+tbl_w, main_y+tbl_h], radius=10, fill=(255, 255, 255, 255), outline=(226, 232, 240, 255), width=1)

    # Table Headers
    raw_headers = content["headers"] if content["headers"] else ["ID", "Category", "Item / Task Description", "Planned", "Actual", "Variance", "Status"]
    headers = [str(h)[:16] for h in raw_headers[:6]]
    while len(headers) < 5:
        headers.append(f"Col {len(headers)+1}")

    col_w = tbl_w // len(headers)
    
    # Header Row Background
    draw_rounded_rect(d, [win_x+20, main_y, win_x+20+tbl_w, main_y+36], radius=8, fill=(16, 124, 65, 255))
    for c_idx, h_text in enumerate(headers):
        cx = win_x + 20 + c_idx * col_w + 12
        d.text((cx, main_y+10), str(h_text).upper(), font=font(12, bold=True), fill=(255, 255, 255, 255))

    # Table Data Rows
    row_y = main_y + 36
    sample_rows = content["rows"][:8] if content["rows"] else [
        ["001", "Operations", "Q1 Budget Allocation", "$45,000", "$42,100", "+$2,900", "Completed"],
        ["002", "Marketing", "Digital Campaign Ads", "$28,000", "$29,500", "-$1,500", "In Progress"],
        ["003", "Development", "Infrastructure Upgrade", "$65,000", "$61,200", "+$3,800", "Approved"],
        ["004", "Sales", "Regional Team Incentive", "$18,500", "$18,500", "$0", "Completed"],
        ["005", "Logistics", "Inventory Warehouse Fleet", "$32,000", "$30,400", "+$1,600", "In Progress"],
        ["006", "HR & Admin", "Employee Training Program", "$12,000", "$11,800", "+$200", "Completed"],
        ["007", "Finance", "Annual Audit & Reporting", "$22,000", "$21,500", "+$500", "Approved"]
    ]

    for r_idx, r_data in enumerate(sample_rows):
        if row_y + 32 > main_y + tbl_h:
            break
        bg_color = (248, 250, 252, 255) if r_idx % 2 == 1 else (255, 255, 255, 255)
        d.rectangle([win_x+21, row_y, win_x+19+tbl_w, row_y+32], fill=bg_color)
        d.line([(win_x+20, row_y+32), (win_x+20+tbl_w, row_y+32)], fill=(241, 245, 249, 255), width=1)

        for c_idx in range(len(headers)):
            val = str(r_data[c_idx]) if c_idx < len(r_data) and r_data[c_idx] is not None else ""
            if not val or val == "None":
                val = f"Data {r_idx+1}-{c_idx+1}"
            
            val_display = val if len(val) <= 18 else val[:16] + "…"
            cx = win_x + 20 + c_idx * col_w + 12
            
            # Status Badge Styling if cell looks like status
            val_lower = val.lower()
            if any(st in val_lower for st in ["complete", "approved", "done", "active", "in progress", "open", "pending"]):
                badge_bg = (220, 252, 231, 255) if any(st in val_lower for st in ["complete", "approved", "done", "active"]) else (254, 243, 199, 255)
                badge_fg = (22, 101, 52, 255) if any(st in val_lower for st in ["complete", "approved", "done", "active"]) else (146, 64, 14, 255)
                bw = font(11, bold=True).getlength(val_display) + 14
                draw_rounded_rect(d, [cx-4, row_y+6, cx+bw, row_y+26], radius=6, fill=badge_bg)
                d.text((cx+3, row_y+8), val_display, font=font(11, bold=True), fill=badge_fg)
            else:
                d.text((cx, row_y+8), val_display, font=font(11, bold=(c_idx==0)), fill=(30, 41, 59, 255))
        
        row_y += 32

    # 3. CHART SIDEBAR CONTAINER (Right Side Chart Visual)
    chart_x = win_x + 20 + tbl_w + 15
    chart_w = win_w - tbl_w - 55
    chart_h = tbl_h

    draw_rounded_rect(d, [chart_x, main_y, chart_x+chart_w, main_y+chart_h], radius=10, fill=(255, 255, 255, 255), outline=(226, 232, 240, 255), width=1)
    d.text((chart_x+16, main_y+14), "PERFORMANCE ANALYTICS", font=font(12, bold=True), fill=(71, 85, 105, 255))

    # Bar Chart Visuals
    bars_y = main_y + 55
    bar_heights = [140, 210, 175, 240, 190, 260]
    bar_labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"]
    bw_item = (chart_w - 40) // len(bar_heights)

    max_bh = max(bar_heights)
    available_h = chart_h - 110

    for bi, bh in enumerate(bar_heights):
        bx = chart_x + 20 + bi * bw_item
        scaled_h = int((bh / max_bh) * available_h)
        by = main_y + chart_h - 35 - scaled_h
        
        # Rounded Bar
        bar_fill = theme["primary"] if bi % 2 == 0 else theme["kpi2"]
        draw_rounded_rect(d, [bx+4, by, bx+bw_item-4, main_y+chart_h-35], radius=4, fill=bar_fill)
        # Bar Label
        d.text((bx+bw_item//4, main_y+chart_h-28), bar_labels[bi], font=font(10, bold=True), fill=(100, 116, 139, 255))

    # EXCEL SHEET TABS AT BOTTOM OF WINDOW
    sheet_y = win_y + win_h - 34
    draw_rounded_rect(d, [win_x, sheet_y, win_x+win_w, win_y+win_h], radius=14, fill=(241, 245, 249, 255))
    sx = win_x + 20
    sheets_to_show = content["sheets"][:4] if content["sheets"] else ["Dashboard", "Data Entry", "Settings"]
    for s_idx, s_name in enumerate(sheets_to_show):
        sw = font(11, bold=(s_idx==0)).getlength(s_name) + 24
        if s_idx == 0:
            draw_rounded_rect(d, [sx, sheet_y+4, sx+sw, win_y+win_h], radius=4, fill=(255, 255, 255, 255))
            d.text((sx+12, sheet_y+8), s_name, font=font(11, bold=True), fill=(16, 124, 65, 255))
        else:
            d.text((sx+12, sheet_y+8), s_name, font=font(11), fill=(100, 116, 139, 255))
        sx += sw + 6

    # Save PNG & WebP
    dest_png.parent.mkdir(parents=True, exist_ok=True)
    img_rgb = img.convert("RGB")
    img_rgb.save(dest_png, "PNG", optimize=True)
    img_rgb.save(dest_webp, "WEBP", quality=90)

def main():
    print("Starting Excel Cover Generation Task (1600x1000)...")
    if not EXCEL_EXPORT_JSON.exists():
        print(f"Error: Export JSON not found at {EXCEL_EXPORT_JSON}")
        return

    items = json.loads(EXCEL_EXPORT_JSON.read_text())
    valid_items = [x for x in items if x.get("file_exists")]
    print(f"Loaded {len(items)} DB items, {len(valid_items)} have valid Excel files on disk.")

    BACKUP_DIR.mkdir(parents=True, exist_ok=True)

    processed = 0
    updated = 0
    failed = []
    report_details = []

    for idx, item in enumerate(valid_items, 1):
        slug = item["slug"]
        title = item["title"]
        subcategory = item["subcategory"] or item["category"] or "General"
        file_path = item["full_path"]

        preview_dir = PREVIEWS_DIR / slug
        cover_png = preview_dir / "cover.png"
        cover_webp = preview_dir / "cover.webp"

        # Backup existing covers
        if cover_png.exists():
            b_png = BACKUP_DIR / f"{slug}_cover.png"
            if not b_png.exists():
                shutil.copy2(cover_png, b_png)
        if cover_webp.exists():
            b_webp = BACKUP_DIR / f"{slug}_cover.webp"
            if not b_webp.exists():
                shutil.copy2(cover_webp, b_webp)

        try:
            excel_content = read_excel_content(file_path)
            generate_cover_image(item, excel_content, cover_png, cover_webp)
            
            updated += 1
            report_details.append({
                "slug": slug,
                "title": title,
                "subcategory": subcategory,
                "status": "success",
                "cover_png": str(cover_png),
                "cover_webp": str(cover_webp),
                "dimensions": "1600x1000",
                "excel_file": file_path
            })
            print(f"[{idx:03d}/{len(valid_items)}] SUCCESS: {slug}")
        except Exception as e:
            failed.append({"slug": slug, "error": str(e), "file_path": file_path})
            report_details.append({
                "slug": slug,
                "title": title,
                "subcategory": subcategory,
                "status": "failed",
                "error": str(e)
            })
            print(f"[{idx:03d}/{len(valid_items)}] FAILED: {slug} - {e}")

        processed += 1

    # Write Final Comprehensive Report JSON
    report_path = ROOT / "storage/excel_covers_generation_report.json"
    report_data = {
        "total_examined": len(items),
        "total_valid_excel_files": len(valid_items),
        "updated_covers_count": updated,
        "failed_count": len(failed),
        "dimensions": "1600x1000",
        "failed_items": failed,
        "details": report_details
    }
    report_path.write_text(json.dumps(report_data, indent=2), encoding="utf-8")
    print(f"\nCompleted! {updated}/{len(valid_items)} Excel template covers generated at 1600x1000.")
    print(f"Report saved to: {report_path}")

if __name__ == "__main__":
    main()
