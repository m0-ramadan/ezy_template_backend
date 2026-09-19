@extends('admin.layouts.app')
@section('section', 'WEBSITE CMS / ABOUT US')
@section('heading', 'About Us & Team Content Manager')

@section('content')
    <form method="POST" action="{{ route('admin.cms.about.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Section 1: Hero --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>1. About Hero Section (قسم البداية)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Eyebrow</label>
                    <input class="input" style="width:100%" name="hero[eyebrow]"
                        value="{{ $hero['eyebrow'] ?? 'About Us' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Main Title (Line
                        1)</label>
                    <input class="input" style="width:100%" name="hero[title]"
                        value="{{ $hero['title'] ?? 'We Empower' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Highlight Gradient Text
                        (Line 2)</label>
                    <input class="input" style="width:100%" name="hero[highlight_text]"
                        value="{{ $hero['highlight_text'] ?? 'Creators & Builders' }}">
                </div>
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Lead Paragraph</label>
                    <textarea class="input" style="width:100%;height:75px;padding:8px" name="hero[lead]">{{ $hero['lead'] ?? '' }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Hero Image URL or
                        Upload</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="hero[hero_image]"
                        value="{{ $hero['hero_image'] ?? '/assets/about-hero.png' }}">
                    <input type="file" name="hero_image" accept="image/*" class="input" style="width:100%;padding:5px">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Buttons (Text &
                        Links)</label>
                    <div style="display:flex;gap:8px;margin-bottom:8px">
                        <input class="input" style="flex:1" name="hero[btn_primary_text]" placeholder="Primary Text"
                            value="{{ $hero['btn_primary_text'] ?? 'Explore Templates →' }}">
                        <input class="input" style="flex:1" name="hero[btn_primary_url]" placeholder="Primary URL"
                            value="{{ $hero['btn_primary_url'] ?? '/templates' }}">
                    </div>
                    <div style="display:flex;gap:8px">
                        <input class="input" style="flex:1" name="hero[btn_secondary_text]" placeholder="Secondary Text"
                            value="{{ $hero['btn_secondary_text'] ?? 'Join Our Community' }}">
                        <input class="input" style="flex:1" name="hero[btn_secondary_url]" placeholder="Secondary URL"
                            value="{{ $hero['btn_secondary_url'] ?? '/signup' }}">
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 2: Stats Counters (4 Cards) --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>2. Counter Statistics (عدادات الأرقام والإحصائيات الـ 4)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px">
                @foreach ($stats as $idx => $st)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px;border-radius:10px">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Icon</label>
                        <input class="input" style="width:100%;margin-bottom:6px" name="stats[{{ $idx }}][icon]"
                            value="{{ $st['icon'] ?? 'Download' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Count / Value</label>
                        <input class="input" style="width:100%;margin-bottom:6px" name="stats[{{ $idx }}][count]"
                            value="{{ $st['count'] ?? '' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Label</label>
                        <input class="input" style="width:100%" name="stats[{{ $idx }}][label]"
                            value="{{ $st['label'] ?? '' }}">
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 3: Our Story --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>3. Our Story (قصة المنصة)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1.5fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Eyebrow</label>
                    <input class="input" style="width:100%;margin-bottom:12px" name="story[eyebrow]"
                        value="{{ $story['eyebrow'] ?? 'Our Story' }}">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Story Heading</label>
                    <input class="input" style="width:100%;margin-bottom:12px" name="story[title]"
                        value="{{ $story['title'] ?? 'A Passion for Better Web Experiences' }}">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Paragraph 1</label>
                    <textarea class="input" style="width:100%;height:70px;padding:8px;margin-bottom:12px" name="story[paragraph_1]">{{ $story['paragraph_1'] ?? '' }}</textarea>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Paragraph 2</label>
                    <textarea class="input" style="width:100%;height:70px;padding:8px" name="story[paragraph_2]">{{ $story['paragraph_2'] ?? '' }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Story Image URL or
                        Upload</label>
                    <input class="input" style="width:100%;margin-bottom:8px" name="story[image]"
                        value="{{ $story['image'] ?? '/assets/about-story.png' }}">
                    <input type="file" name="story_image" accept="image/*" class="input"
                        style="width:100%;padding:5px">
                    @if (!empty($story['image']))
                        <div style="margin-top:12px;background:#f1f5f9;border-radius:8px;padding:8px;text-align:center">
                            <img src="{{ $story['image'] }}" style="max-height:140px;border-radius:6px;max-width:100%">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Section 4: Mission, Vision & Values --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>4. Mission, Vision & Values (الرسالة، الرؤية، والقيم)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px">
                @foreach ($values as $idx => $v)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px;border-radius:10px">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Icon</label>
                        <input class="input" style="width:100%;margin-bottom:6px"
                            name="values[{{ $idx }}][icon]" value="{{ $v['icon'] ?? 'Target' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Title</label>
                        <input class="input" style="width:100%;margin-bottom:6px"
                            name="values[{{ $idx }}][title]" value="{{ $v['title'] ?? '' }}">
                        <label style="font-size:10px;font-weight:700;display:block;margin-bottom:4px">Description</label>
                        <textarea class="input" style="width:100%;height:60px;padding:6px" name="values[{{ $idx }}][description]">{{ $v['description'] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 5: Team Members (CRUD) --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>5. Team Members (فريق العمل)</h3>
                <span>Manage team cards, roles, avatars and social accounts</span>
            </div>
            <div class="panel-body">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px">
                    @foreach ($team as $idx => $m)
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:10px">
                            <div style="display:flex;gap:12px;margin-bottom:10px">
                                <img src="{{ $m['image'] ?? '/assets/article-web.png' }}"
                                    style="width:50px;height:50px;border-radius:50%;object-fit:cover;border:2px solid #2563eb">
                                <div style="flex:1">
                                    <input class="input" style="width:100%;font-weight:700;margin-bottom:4px"
                                        name="team[{{ $idx }}][name]" placeholder="Full Name"
                                        value="{{ $m['name'] ?? '' }}">
                                    <input class="input" style="width:100%" name="team[{{ $idx }}][role]"
                                        placeholder="Position / Role" value="{{ $m['role'] ?? '' }}">
                                </div>
                            </div>
                            <div style="margin-bottom:8px">
                                <label style="font-size:10px;font-weight:700;display:block;margin-bottom:3px">Short
                                    Bio</label>
                                <input class="input" style="width:100%" name="team[{{ $idx }}][bio]"
                                    value="{{ $m['bio'] ?? '' }}">
                            </div>
                            <div style="margin-bottom:8px">
                                <label style="font-size:10px;font-weight:700;display:block;margin-bottom:3px">Photo URL or
                                    Upload New</label>
                                <input class="input" style="width:100%;margin-bottom:4px"
                                    name="team[{{ $idx }}][image]" value="{{ $m['image'] ?? '' }}">
                                <input type="file" name="team_photos[{{ $idx }}]" accept="image/*"
                                    class="input" style="width:100%;padding:4px">
                            </div>
                            <div style="display:flex;gap:6px">
                                <input class="input" style="flex:1" name="team[{{ $idx }}][twitter]"
                                    placeholder="Twitter URL" value="{{ $m['twitter'] ?? '' }}">
                                <input class="input" style="flex:1" name="team[{{ $idx }}][linkedin]"
                                    placeholder="LinkedIn URL" value="{{ $m['linkedin'] ?? '' }}">
                                <input class="input" style="flex:1" name="team[{{ $idx }}][github]"
                                    placeholder="GitHub URL" value="{{ $m['github'] ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Section 6: CTA --}}
        <section class="panel" style="margin-bottom:24px">
            <div class="panel-head">
                <h3>6. Bottom CTA (بانر التذييل)</h3>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Title</label>
                    <input class="input" style="width:100%" name="cta[title]" value="{{ $cta['title'] ?? '' }}">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Button Text & URL</label>
                    <div style="display:flex;gap:8px">
                        <input class="input" style="flex:1" name="cta[button_text]"
                            value="{{ $cta['button_text'] ?? 'Get Started →' }}">
                        <input class="input" style="flex:1" name="cta[button_url]"
                            value="{{ $cta['button_url'] ?? '/signup' }}">
                    </div>
                </div>
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Description</label>
                    <input class="input" style="width:100%" name="cta[description]"
                        value="{{ $cta['description'] ?? '' }}">
                </div>
            </div>
        </section>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:40px">
            <button type="submit" class="btn" style="padding:12px 24px;font-size:13px">💾 Save About Page
                Content</button>
        </div>
    </form>
@endsection
