@extends('admin.layouts.app')
@section('section', 'WEBSITE CMS / SERVICES')
@section('heading', 'Services & FAQ Content Manager')

@section('content')
    <form method="POST" action="{{ route('admin.cms.services.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Section 1: Hero --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>1. Services Hero Section (قسم البداية)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Eyebrow</label>
                    <input class="input" style="width:100%" name="hero[eyebrow]"
                        value="{{ $hero['eyebrow'] ?? 'Our Services' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Main Title (Line
                        1)</label>
                    <input class="input" style="width:100%" name="hero[title]"
                        value="{{ $hero['title'] ?? 'More Than Templates' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Highlight Gradient Text
                        (Line 2)</label>
                    <input class="input" style="width:100%" name="hero[highlight_text]"
                        value="{{ $hero['highlight_text'] ?? 'We Help You Build' }}">
                </div>
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Lead Paragraph</label>
                    <textarea class="input" style="width:100%;height:75px;padding:8px" name="hero[lead]">{{ $hero['lead'] ?? '' }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Hero Image URL or
                        Upload</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[hero_image]"
                        value="{{ $hero['hero_image'] ?? '/assets/services-hero.png' }}">
                    <input type="file" name="hero_image" accept="image/*" class="input" style="width:100%;padding:5px">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Buttons</label>
                    <div style="display:flex;gap:8px;margin-bottom:8px">
                        <input class="input" style="flex:1" name="hero[btn_primary_text]"
                            value="{{ $hero['btn_primary_text'] ?? 'Get Started →' }}">
                        <input class="input" style="flex:1" name="hero[btn_primary_url]"
                            value="{{ $hero['btn_primary_url'] ?? '#request-service' }}">
                    </div>
                    <div style="display:flex;gap:8px">
                        <input class="input" style="flex:1" name="hero[btn_secondary_text]"
                            value="{{ $hero['btn_secondary_text'] ?? 'Explore FAQs' }}">
                        <input class="input" style="flex:1" name="hero[btn_secondary_url]"
                            value="{{ $hero['btn_secondary_url'] ?? '#faq' }}">
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 2: Trust Badges (3 Badges) --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>2. Trust Badges (شارات الثقة الـ 3 أسفل الهيرو)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px">
                @foreach ($trust_badges as $idx => $b)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Icon</label>
                        <input class="input" style="width:100%;margin-bottom:6px"
                            name="trust_badges[{{ $idx }}][icon]" value="{{ $b['icon'] ?? 'ShieldCheck' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Title</label>
                        <input class="input" style="width:100%;margin-bottom:6px"
                            name="trust_badges[{{ $idx }}][title]" value="{{ $b['title'] ?? '' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Subtitle</label>
                        <input class="input" style="width:100%" name="trust_badges[{{ $idx }}][subtitle]"
                            value="{{ $b['subtitle'] ?? '' }}">
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 3: Services Catalog (6 Cards) --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>3. Services Packages (باقات وقائمة الخدمات)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                @foreach ($services as $idx => $s)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px;border-radius:10px">
                        <div style="display:flex;gap:10px;margin-bottom:8px">
                            <div style="width:120px">
                                <label style="font-size:10px;font-weight:700">Icon</label>
                                <input class="input" style="width:100%" name="services[{{ $idx }}][icon]"
                                    value="{{ $s['icon'] ?? 'Settings' }}">
                            </div>
                            <div style="flex:1">
                                <label style="font-size:10px;font-weight:700">Service Title</label>
                                <input class="input" style="width:100%" name="services[{{ $idx }}][title]"
                                    value="{{ $s['title'] ?? '' }}">
                            </div>
                        </div>
                        <div>
                            <label style="font-size:10px;font-weight:700">Description</label>
                            <textarea class="input" style="width:100%;height:55px;padding:6px"
                                name="services[{{ $idx }}][description]">{{ $s['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 4: Why Us Section --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>4. Why Choose Us (لماذا تختارنا)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1.5fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Eyebrow</label>
                    <input class="input" style="width:100%;margin-bottom:12px" name="why_us[eyebrow]"
                        value="{{ $why_us['eyebrow'] ?? 'Why Choose Us' }}">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Section Title</label>
                    <input class="input" style="width:100%;margin-bottom:12px" name="why_us[title]"
                        value="{{ $why_us['title'] ?? 'Your Success is Our Priority' }}">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Lead Paragraph</label>
                    <textarea class="input" style="width:100%;height:65px;padding:8px;margin-bottom:12px" name="why_us[lead]">{{ $why_us['lead'] ?? '' }}</textarea>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Bullet Points (One per
                        line)</label>
                    <textarea class="input" style="width:100%;height:100px;padding:8px" name="why_us[bullets_raw]">{{ implode("\n", $why_us['bullets'] ?? []) }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Why Us Artwork</label>
                    <input class="input" style="width:100%;margin-bottom:8px" name="why_us[image]"
                        value="{{ $why_us['image'] ?? '/assets/services-why.png' }}">
                    <input type="file" name="why_us_image" accept="image/*" class="input"
                        style="width:100%;padding:5px">
                    @if (!empty($why_us['image']))
                        <div style="margin-top:12px;background:#f1f5f9;border-radius:8px;padding:8px;text-align:center">
                            <img src="{{ $why_us['image'] }}" style="max-height:140px;border-radius:6px;max-width:100%">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Section 5: Process Steps (4 Steps) --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>5. Working Process (مراحل العمل الـ 4)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px">
                @foreach ($process as $idx => $p)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                        <b style="display:block;margin-bottom:6px;font-size:11px">Step {{ $p['step'] ?? $idx + 1 }}</b>
                        <input type="hidden" name="process[{{ $idx }}][step]"
                            value="{{ $p['step'] ?? $idx + 1 }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:3px">Step Title</label>
                        <input class="input" style="width:100%;margin-bottom:6px"
                            name="process[{{ $idx }}][title]" value="{{ $p['title'] ?? '' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:3px">Description</label>
                        <textarea class="input" style="width:100%;height:60px;padding:6px"
                            name="process[{{ $idx }}][description]">{{ $p['description'] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 6: FAQs (CRUD) --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>6. Frequently Asked Questions (الأسئلة الشائعة)</h3>
            </div>
            <div class="panel-body">
                @foreach ($faqs as $idx => $f)
                    <div
                        style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px;border-radius:10px;margin-bottom:14px">
                        <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px">Question
                            #{{ $idx + 1 }}</label>
                        <input class="input" style="width:100%;margin-bottom:8px"
                            name="faqs[{{ $idx }}][question]" value="{{ $f['question'] ?? '' }}">
                        <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px">Answer</label>
                        <textarea class="input" style="width:100%;height:50px;padding:6px" name="faqs[{{ $idx }}][answer]">{{ $f['answer'] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 7: Help Box --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>7. Help & Inquiries Card (صندوق المساعدة)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Card Title</label>
                    <input class="input" style="width:100%" name="help_box[title]"
                        value="{{ $help_box['title'] ?? 'Still Have Questions?' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Button Text &
                        Link</label>
                    <div style="display:flex;gap:8px">
                        <input class="input" style="flex:1" name="help_box[button_text]"
                            value="{{ $help_box['button_text'] ?? 'Contact Us →' }}">
                        <input class="input" style="flex:1" name="help_box[button_url]"
                            value="{{ $help_box['button_url'] ?? '#request-service' }}">
                    </div>
                </div>
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Description</label>
                    <input class="input" style="width:100%" name="help_box[description]"
                        value="{{ $help_box['description'] ?? '' }}">
                </div>
            </div>
        </section>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:40px">
            <button type="submit" class="btn" style="padding:12px 24px;font-size:13px">💾 Save Services Page
                Content</button>
        </div>
    </form>
@endsection
