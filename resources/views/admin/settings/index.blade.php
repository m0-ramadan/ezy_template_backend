@extends('admin.layouts.app')
@section('section', 'WEBSITE CMS / BRANDING & SETTINGS')
@section('heading', 'Platform Branding & Navigation')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Tab 1: General Brand & Identity --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>1. Brand Identity & Meta (هوية الموقع والشعار)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Site Name (اسم
                        المنصة)</label>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Site Name (English)</label>
                    <input class="input" style="width:100%" name="site_name" value="{{ $site_name }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Tagline / Slogan (الشعار
                        اللفظي)</label>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">اسم المنصة بالعربية
                        (Arabic Name)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right"
                        name="site_name_ar" dir="rtl" value="{{ $site_name_ar ?? '' }}">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Tagline / Slogan
                        (English)</label>
                    <input class="input" style="width:100%" name="site_tagline" value="{{ $site_tagline }}">
                </div>
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Site Meta Description
                        (الوصف التعريفي للـ SEO)</label>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">الشعار اللفظي بالعربية
                            (Arabic Tagline)</label>
                        <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right"
                            name="site_tagline_ar" dir="rtl" value="{{ $site_tagline_ar ?? '' }}">
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Site Meta Description
                            (English)</label>
                        <textarea class="input" style="width:100%;height:60px;padding:8px" name="site_description">{{ $site_description }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Copyright Text (حقوق
                            الملكية في الفوتر)</label>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">الوصف التعريفي للـ SEO
                            بالعربية (Arabic Description)</label>
                        <textarea class="input" style="width:100%;height:60px;padding:8px;font-family:'Cairo',sans-serif;text-align:right"
                            name="site_description_ar" dir="rtl">{{ $site_description_ar ?? '' }}</textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Copyright Text
                            (English)</label>
                        <input class="input" style="width:100%" name="copyright_text" value="{{ $copyright_text }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Footer Subtext /
                            Mission</label>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">حقوق الملكية بالعربية
                            (Arabic Copyright)</label>
                        <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right"
                            name="copyright_text_ar" dir="rtl" value="{{ $copyright_text_ar ?? '' }}">
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Footer Subtext
                            (English)</label>
                        <input class="input" style="width:100%" name="footer_subtext" value="{{ $footer_subtext }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">نص التذييل الإضافي
                            بالعربية (Arabic Subtext)</label>
                        <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right"
                            name="footer_subtext_ar" dir="rtl" value="{{ $footer_subtext_ar ?? '' }}">
                    </div>
                </div>
        </section>

        {{-- Tab 2: Contact Information --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>2. Contact Details (بيانات التواصل والدعم)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Support Email</label>
                    <input class="input" style="width:100%" type="email" name="contact_email"
                        value="{{ $contact_email }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Phone / WhatsApp</label>
                    <input class="input" style="width:100%" name="contact_phone" value="{{ $contact_phone }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Office Address /
                        Location</label>
                    <input class="input" style="width:100%" name="contact_address" value="{{ $contact_address }}">
                </div>
            </div>
        </section>

        {{-- Tab 3: Social Media Links --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>3. Social Media Links (روابط حسابات التواصل)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
                @foreach ($social_links as $idx => $s)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                        <b style="display:block;font-size:11px;margin-bottom:6px">{{ $s['platform'] ?? 'Platform' }}</b>
                        <input type="hidden" name="social_links[{{ $idx }}][platform]"
                            value="{{ $s['platform'] ?? '' }}">
                        <input type="hidden" name="social_links[{{ $idx }}][icon]"
                            value="{{ $s['icon'] ?? '' }}">
                        <input class="input" style="width:100%;margin-bottom:6px"
                            name="social_links[{{ $idx }}][url]" placeholder="https://..."
                            value="{{ $s['url'] ?? '' }}">
                        <label style="font-size:10px;display:flex;align-items:center;gap:6px">
                            <input type="checkbox" name="social_links[{{ $idx }}][is_active]" value="1"
                                {{ !empty($s['is_active']) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Tab 4: Header Navigation Menu --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>4. Header Navigation Links (روابط القائمة العلوية للهيدر)</h3>
            </div>
            <div class="panel-body">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
                    @foreach ($header_nav_links as $idx => $link)
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                            <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Link
                                Label</label>
                            <input class="input" style="width:100%;margin-bottom:6px"
                                name="header_nav_links[{{ $idx }}][label]" value="{{ $link['label'] ?? '' }}">
                            <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">URL Path</label>
                            <input class="input" style="width:100%;margin-bottom:6px"
                                name="header_nav_links[{{ $idx }}][url]" value="{{ $link['url'] ?? '' }}">
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <label style="font-size:10px;display:flex;align-items:center;gap:6px">
                                    <input type="checkbox" name="header_nav_links[{{ $idx }}][is_active]"
                                        value="1" {{ !empty($link['is_active']) ? 'checked' : '' }}> Active
                                </label>
                                <input class="input" style="width:50px;height:28px;padding:2px 6px;font-size:10px"
                                    name="header_nav_links[{{ $idx }}][sort_order]"
                                    value="{{ $link['sort_order'] ?? $idx + 1 }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Tab 5: Footer Columns --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>5. Footer Columns & Links (أعمدة وروابط التذييل الفوتر)</h3>
                <span>Format each line as: Label | URL</span>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px">
                @foreach ($footer_columns as $idx => $col)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px;border-radius:10px">
                        <label style="font-size:11px;font-weight:700;display:block;margin-bottom:6px">Column
                            #{{ $idx + 1 }} Title</label>
                        <input class="input" style="width:100%;margin-bottom:10px"
                            name="footer_columns[{{ $idx }}][title]" value="{{ $col['title'] ?? '' }}">
                        <label style="font-size:11px;font-weight:700;display:block;margin-bottom:6px">Links (One per line:
                            Label | URL)</label>
                        @php
                            $lines = [];
                            foreach ($col['links'] ?? [] as $l) {
                                $lines[] = ($l['label'] ?? '') . ' | ' . ($l['url'] ?? '');
                            }
                        @endphp
                        <textarea class="input" style="width:100%;height:130px;padding:8px;font-family:monospace;font-size:11px"
                            name="footer_columns[{{ $idx }}][links_raw]">{{ implode("\n", $lines) }}</textarea>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Tab 6: System Settings --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>6. Platform & Performance (إعدادات النظام العامة)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Default Items per Page
                        (القوالب في الصفحة)</label>
                    <input class="input" style="width:100%" name="items_per_page" value="{{ $items_per_page }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Analytics Retention
                        (Days)</label>
                    <input class="input" style="width:100%" name="analytics_retention_days"
                        value="{{ $analytics_retention_days }}">
                </div>
            </div>
        </section>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:40px">
            <button type="submit" class="btn" style="padding:12px 24px;font-size:13px">💾 Save Branding &
                Settings</button>
        </div>
    </form>
@endsection
