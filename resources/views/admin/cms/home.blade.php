@extends('admin.layouts.app')
@section('section', 'WEBSITE CMS / HOMEPAGE')
@section('heading', 'Homepage Content Manager (إدارة محتوى الصفحة الرئيسية)')

@section('content')
    <form method="POST" action="{{ route('admin.cms.home.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Section 1: Hero Section --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>1. Hero Section (قسم البداية والشعار الرئيسي)</h3>
                <span>Controls the top hero on the homepage in English & Arabic</span>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Eyebrow Badge (English)</label>
                    <input class="input" style="width:100%" name="hero[eyebrow]" value="{{ $hero['eyebrow'] ?? '' }}" placeholder="🚀 Over 10,000+ templates">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">نص الشارة العلوية بالعربية (Arabic Eyebrow)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[eyebrow_ar]" dir="rtl" value="{{ $hero['eyebrow_ar'] ?? '' }}" placeholder="🚀 أكثر من 10,000+ قالب ومورد جاهز">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Main Title (English)</label>
                    <input class="input" style="width:100%" name="hero[title]" value="{{ $hero['title'] ?? '' }}" placeholder="Download High Quality">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">العنوان الرئيسي بالعربية (Arabic Title)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[title_ar]" dir="rtl" value="{{ $hero['title_ar'] ?? '' }}" placeholder="حمل أفضل وأحدث">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Highlight Gradient Text (English)</label>
                    <input class="input" style="width:100%" name="hero[highlight_text]" value="{{ $hero['highlight_text'] ?? '' }}" placeholder="Templates & Digital Assets">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">النص الملون المميز بالعربية (Arabic Highlight)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[highlight_text_ar]" dir="rtl" value="{{ $hero['highlight_text_ar'] ?? '' }}" placeholder="القوالب والملفات الرقمية الاحترافية">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Lead Paragraph (English)</label>
                    <textarea class="input" style="width:100%;height:75px;padding:8px" name="hero[lead]">{{ $hero['lead'] ?? '' }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">الوصف التعريفي بالعربية (Arabic Lead)</label>
                    <textarea class="input" style="width:100%;height:75px;padding:8px;font-family:'Cairo',sans-serif;text-align:right" name="hero[lead_ar]" dir="rtl">{{ $hero['lead_ar'] ?? '' }}</textarea>
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Hero Artwork Image URL</label>
                    <input class="input" style="width:100%" name="hero[hero_image]" value="{{ $hero['hero_image'] ?? '' }}">
                    <div style="margin-top:8px">
                        <input type="file" name="hero_image" accept="image/*" class="input" style="width:100%;padding:5px">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Hero Sticky Note (EN & AR)</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[note_title]" placeholder="Note line 1 (EN)" value="{{ $hero['note_title'] ?? '' }}">
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[note_title_ar]" dir="rtl" placeholder="ملاحظة الهيرو بالعربية" value="{{ $hero['note_title_ar'] ?? '' }}">
                </div>
            </div>
        </section>

        {{-- Section 2: Floating Stat Badges --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>2. Floating Badges (الشارات الثلاثة العائمة على صورة الهيرو)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                    <b style="display:block;margin-bottom:8px;font-size:11px">Badge 1 (Top Left)</b>
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[stat_badge_1_val]" placeholder="e.g. 1000+" value="{{ $hero['stat_badge_1_val'] ?? '' }}">
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[stat_badge_1_label]" placeholder="EN: Free Templates" value="{{ $hero['stat_badge_1_label'] ?? '' }}">
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[stat_badge_1_label_ar]" dir="rtl" placeholder="AR: قوالب مجانية" value="{{ $hero['stat_badge_1_label_ar'] ?? '' }}">
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                    <b style="display:block;margin-bottom:8px;font-size:11px">Badge 2 (Top Right)</b>
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[stat_badge_2_val]" placeholder="e.g. Modern" value="{{ $hero['stat_badge_2_val'] ?? '' }}">
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[stat_badge_2_label]" placeholder="EN: & Responsive" value="{{ $hero['stat_badge_2_label'] ?? '' }}">
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[stat_badge_2_label_ar]" dir="rtl" placeholder="AR: وتصميم متجاوب" value="{{ $hero['stat_badge_2_label_ar'] ?? '' }}">
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                    <b style="display:block;margin-bottom:8px;font-size:11px">Badge 3 (Bottom Left)</b>
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[stat_badge_3_val]" placeholder="e.g. ⚡ Easy to" value="{{ $hero['stat_badge_3_val'] ?? '' }}">
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[stat_badge_3_label]" placeholder="EN: Customize" value="{{ $hero['stat_badge_3_label'] ?? '' }}">
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="hero[stat_badge_3_label_ar]" dir="rtl" placeholder="AR: سهلة التخصيص" value="{{ $hero['stat_badge_3_label_ar'] ?? '' }}">
                </div>
            </div>
        </section>

        {{-- Section 3: Popular Search Tags --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>3. Popular Search Tags (الكلمات الدلالية السريعة أسفل البحث)</h3>
            </div>
            <div class="panel-body">
                <input class="input" style="width:100%" name="popular_tags_raw" value="{{ implode(', ', $popular_tags ?? []) }}">
                <small style="color:#71809a;display:block;margin-top:6px">Enter tags separated by commas, e.g.: Laravel, Next.js, Admin Dashboard, eCommerce, Portfolio, Blog, قوالب إكسيل, وورد</small>
            </div>
        </section>

        {{-- Section 4: Featured Section Header --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>4. Trending & Featured Section (قسم القوالب المميزة)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Section Title (English)</label>
                    <input class="input" style="width:100%" name="featured[title]" value="{{ $featured['title'] ?? 'Trending Templates' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">عنوان القسم بالعربية (Arabic Title)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="featured[title_ar]" dir="rtl" value="{{ $featured['title_ar'] ?? 'القوالب الأكثر طلباً ومميزة' }}">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Description (English)</label>
                    <input class="input" style="width:100%" name="featured[description]" value="{{ $featured['description'] ?? 'Hand-picked templates to help you build faster.' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">الوصف بالعربية (Arabic Description)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="featured[description_ar]" dir="rtl" value="{{ $featured['description_ar'] ?? 'مجموعة مختارة بعناية من القوالب لمساعدتك على الإنجاز بسرعة.' }}">
                </div>
            </div>
        </section>

        {{-- Section 5: Benefits / Why Us --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>5. Platform Benefits (بطاقات مميزات المنصة)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                @foreach ($benefits as $idx => $b)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px;border-radius:10px">
                        <div style="display:flex;gap:10px;margin-bottom:8px">
                            <div style="width:100px">
                                <label style="font-size:10px;font-weight:700">Icon</label>
                                <input class="input" style="width:100%" name="benefits[{{ $idx }}][icon]" value="{{ $b['icon'] ?? 'Zap' }}">
                            </div>
                            <div style="flex:1">
                                <label style="font-size:10px;font-weight:700">Title (EN)</label>
                                <input class="input" style="width:100%" name="benefits[{{ $idx }}][title]" value="{{ $b['title'] ?? '' }}">
                            </div>
                        </div>
                        <div style="margin-bottom:8px">
                            <label style="font-size:10px;font-weight:700">العنوان بالعربية (Title AR)</label>
                            <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="benefits[{{ $idx }}][title_ar]" dir="rtl" value="{{ $b['title_ar'] ?? '' }}">
                        </div>
                        <div style="margin-bottom:8px">
                            <label style="font-size:10px;font-weight:700">Description (EN)</label>
                            <textarea class="input" style="width:100%;height:45px;padding:6px" name="benefits[{{ $idx }}][description]">{{ $b['description'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label style="font-size:10px;font-weight:700">الوصف بالعربية (Description AR)</label>
                            <textarea class="input" style="width:100%;height:45px;padding:6px;font-family:'Cairo',sans-serif;text-align:right" name="benefits[{{ $idx }}][description_ar]" dir="rtl">{{ $b['description_ar'] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 6: CTA Section --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>6. Call to Action Banner (بانر الدعوة للبدء)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Banner Title (English)</label>
                    <input class="input" style="width:100%" name="cta[title]" value="{{ $cta['title'] ?? '' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">عنوان البانر بالعربية (Arabic Title)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="cta[title_ar]" dir="rtl" value="{{ $cta['title_ar'] ?? '' }}">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Banner Description (English)</label>
                    <input class="input" style="width:100%" name="cta[description]" value="{{ $cta['description'] ?? '' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">وصف البانر بالعربية (Arabic Description)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="cta[description_ar]" dir="rtl" value="{{ $cta['description_ar'] ?? '' }}">
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Button Text (English) & URL</label>
                    <div style="display:flex;gap:8px">
                        <input class="input" style="flex:1" name="cta[button_text]" placeholder="Button Text (EN)" value="{{ $cta['button_text'] ?? '' }}">
                        <input class="input" style="flex:1" name="cta[button_url]" placeholder="URL (e.g. /templates)" value="{{ $cta['button_url'] ?? '' }}">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">نص الزر بالعربية (Arabic Button Text)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="cta[button_text_ar]" dir="rtl" placeholder="مثال: تصفح القوالب الآن" value="{{ $cta['button_text_ar'] ?? '' }}">
                </div>
            </div>
        </section>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:40px">
            <button type="submit" class="btn" style="padding:12px 24px;font-size:13px">💾 Save Homepage Content (حفظ محتوى الهوم)</button>
        </div>
    </form>
@endsection
