@extends('admin.layouts.app')
@section('section', 'WEBSITE CMS / SYSTEM PAGES')
@section('heading', 'System Pages & Content Manager')

@section('content')
    <style>
        .tab-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color, #e2e8f0);
            padding-bottom: 8px;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-muted, #64748b);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: var(--primary-color, #2563eb);
            color: #ffffff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .repeater-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 12px;
            position: relative;
        }

        .repeater-item .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
            cursor: pointer;
        }
    </style>

    <div class="tab-bar">
        <button type="button" class="tab-btn active" onclick="switchTab('faq')">❓ FAQ Page</button>
        <button type="button" class="tab-btn" onclick="switchTab('custom_requests')">🛠 Custom Requests</button>
        <button type="button" class="tab-btn" onclick="switchTab('support')">🎧 Technical Support</button>
        <button type="button" class="tab-btn" onclick="switchTab('contact')">📩 Contact Us</button>
        <button type="button" class="tab-btn" onclick="switchTab('privacy')">🔒 Privacy Policy</button>
        <button type="button" class="tab-btn" onclick="switchTab('terms')">📜 Terms of Service</button>
    </div>

    <form method="POST" action="{{ route('admin.cms.custom-pages.update') }}">
        @csrf

        {{-- TAB 1: FAQ --}}
        <div id="tab-faq" class="tab-content active">
            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>FAQ Page Header & Content</h3>
                </div>
                <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (EN)</label>
                        <input class="input" style="width:100%" name="page_faq[title]"
                            value="{{ $faq['title'] ?? 'Frequently Asked Questions' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (AR)</label>
                        <input class="input" style="width:100%" name="page_faq[title_ar]"
                            value="{{ $faq['title_ar'] ?? 'الأسئلة الشائعة' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (EN)</label>
                        <input class="input" style="width:100%" name="page_faq[subtitle]"
                            value="{{ $faq['subtitle'] ?? 'Find answers to common questions about EzyTemplate.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (AR)</label>
                        <input class="input" style="width:100%" name="page_faq[subtitle_ar]"
                            value="{{ $faq['subtitle_ar'] ?? 'إجابات شاملة لجميع استفساراتك حول إيزي تيمبلت.' }}">
                    </div>
                </div>
            </section>

            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>FAQ Items</h3>
                    <button type="button" class="btn secondary" onclick="addFaqItem()">+ Add Question</button>
                </div>
                <div class="panel-body" id="faq-items-container">
                    @foreach ($faq['items'] ?? [] as $idx => $item)
                        <div class="repeater-item" id="faq-item-{{ $idx }}">
                            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕
                                Remove</button>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px">
                                <div>
                                    <label style="font-size:10px;font-weight:700">Question (EN)</label>
                                    <input class="input" style="width:100%"
                                        name="page_faq[items][{{ $idx }}][question]"
                                        value="{{ $item['question'] ?? '' }}">
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700">Question (AR)</label>
                                    <input class="input" style="width:100%"
                                        name="page_faq[items][{{ $idx }}][question_ar]"
                                        value="{{ $item['question_ar'] ?? '' }}">
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700">Answer (EN)</label>
                                    <textarea class="input" style="width:100%;height:60px" name="page_faq[items][{{ $idx }}][answer]">{{ $item['answer'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700">Answer (AR)</label>
                                    <textarea class="input" style="width:100%;height:60px" name="page_faq[items][{{ $idx }}][answer_ar]">{{ $item['answer_ar'] ?? '' }}</textarea>
                                </div>
                                <div style="grid-column: 1 / -1">
                                    <label style="font-size:10px;font-weight:700">Category Tag (e.g. General, Licensing,
                                        Support)</label>
                                    <input class="input" style="width:100%"
                                        name="page_faq[items][{{ $idx }}][category]"
                                        value="{{ $item['category'] ?? 'General' }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- TAB 2: CUSTOM REQUESTS --}}
        <div id="tab-custom_requests" class="tab-content">
            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Custom Requests Page Details</h3>
                </div>
                <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (EN)</label>
                        <input class="input" style="width:100%" name="page_custom_requests[title]"
                            value="{{ $custom_requests['title'] ?? 'Custom Template Requests' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (AR)</label>
                        <input class="input" style="width:100%" name="page_custom_requests[title_ar]"
                            value="{{ $custom_requests['title_ar'] ?? 'طلبات القوالب الخاصة' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (EN)</label>
                        <input class="input" style="width:100%" name="page_custom_requests[subtitle]"
                            value="{{ $custom_requests['subtitle'] ?? 'Need a bespoke design tailored specifically for your project?' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (AR)</label>
                        <input class="input" style="width:100%" name="page_custom_requests[subtitle_ar]"
                            value="{{ $custom_requests['subtitle_ar'] ?? 'هل تحتاج لتصميم مخصص ينفذ خصيصاً لمشروعك؟' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Starting
                            Price</label>
                        <input class="input" style="width:100%" name="page_custom_requests[starting_price]"
                            value="{{ $custom_requests['starting_price'] ?? '$99' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Turnaround
                            Time</label>
                        <input class="input" style="width:100%" name="page_custom_requests[turn_around]"
                            value="{{ $custom_requests['turn_around'] ?? '3 - 5 Business Days' }}">
                    </div>
                    <div style="grid-column: 1 / -1">
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Features Offered (1
                            per line)</label>
                        <textarea class="input" style="width:100%;height:100px" name="page_custom_requests[features_raw]">{{ implode("\n", $custom_requests['features'] ?? []) }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        {{-- TAB 3: TECHNICAL SUPPORT --}}
        <div id="tab-support" class="tab-content">
            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Technical Support Settings</h3>
                </div>
                <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (EN)</label>
                        <input class="input" style="width:100%" name="page_support[title]"
                            value="{{ $support['title'] ?? 'Technical Support' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (AR)</label>
                        <input class="input" style="width:100%" name="page_support[title_ar]"
                            value="{{ $support['title_ar'] ?? 'الدعم الفني والتقني' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (EN)</label>
                        <input class="input" style="width:100%" name="page_support[subtitle]"
                            value="{{ $support['subtitle'] ?? 'Our engineering team is here to assist you with installation, setup, and troubleshooting.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (AR)</label>
                        <input class="input" style="width:100%" name="page_support[subtitle_ar]"
                            value="{{ $support['subtitle_ar'] ?? 'فريقنا المهندسين متواجد على مدار الساعة لمساعدتك في التثبيت والتعديل حل أي استفسار.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Average Response
                            Time</label>
                        <input class="input" style="width:100%" name="page_support[response_time]"
                            value="{{ $support['response_time'] ?? '< 2 Hours' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">System Status</label>
                        <input class="input" style="width:100%" name="page_support[status]"
                            value="{{ $support['status'] ?? 'All Systems Operational' }}">
                    </div>
                </div>
            </section>
        </div>

        {{-- TAB 4: CONTACT US --}}
        <div id="tab-contact" class="tab-content">
            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Contact Information & Details</h3>
                </div>
                <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (EN)</label>
                        <input class="input" style="width:100%" name="page_contact[title]"
                            value="{{ $contact['title'] ?? 'Contact Us' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (AR)</label>
                        <input class="input" style="width:100%" name="page_contact[title_ar]"
                            value="{{ $contact['title_ar'] ?? 'تواصل معنا' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (EN)</label>
                        <input class="input" style="width:100%" name="page_contact[subtitle]"
                            value="{{ $contact['subtitle'] ?? 'Have questions or want to partner up? Get in touch with us.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (AR)</label>
                        <input class="input" style="width:100%" name="page_contact[subtitle_ar]"
                            value="{{ $contact['subtitle_ar'] ?? 'نحن يسعدنا التواصل معك دائمًا والإجابة على أي استفسار.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Support Email</label>
                        <input class="input" style="width:100%" name="page_contact[email]"
                            value="{{ $contact['email'] ?? 'support@ezytemplate.com' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Phone Number</label>
                        <input class="input" style="width:100%" name="page_contact[phone]"
                            value="{{ $contact['phone'] ?? '+20 100 000 0000' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Address</label>
                        <input class="input" style="width:100%" name="page_contact[address]"
                            value="{{ $contact['address'] ?? 'Cairo, Egypt / Dubai, UAE' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Working Hours</label>
                        <input class="input" style="width:100%" name="page_contact[working_hours]"
                            value="{{ $contact['working_hours'] ?? 'Sun - Thu: 9:00 AM - 6:00 PM (GMT+2)' }}">
                    </div>
                </div>
            </section>
        </div>

        {{-- TAB 5: PRIVACY POLICY --}}
        <div id="tab-privacy" class="tab-content">
            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Privacy Policy Header</h3>
                </div>
                <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (EN)</label>
                        <input class="input" style="width:100%" name="page_privacy[title]"
                            value="{{ $privacy['title'] ?? 'Privacy Policy' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (AR)</label>
                        <input class="input" style="width:100%" name="page_privacy[title_ar]"
                            value="{{ $privacy['title_ar'] ?? 'سياسة الخصوصية' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (EN)</label>
                        <input class="input" style="width:100%" name="page_privacy[subtitle]"
                            value="{{ $privacy['subtitle'] ?? 'How we protect, process, and handle your data.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (AR)</label>
                        <input class="input" style="width:100%" name="page_privacy[subtitle_ar]"
                            value="{{ $privacy['subtitle_ar'] ?? 'كيف نحمي بياناتك ونحافظ على خصوصيتك بشكل كامل.' }}">
                    </div>
                    <div style="grid-column: 1 / -1">
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Last Updated
                            Date</label>
                        <input class="input" style="width:100%" name="page_privacy[last_updated]"
                            value="{{ $privacy['last_updated'] ?? 'January 2026' }}">
                    </div>
                </div>
            </section>

            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Privacy Policy Clauses</h3>
                    <button type="button" class="btn secondary" onclick="addPrivacySection()">+ Add Clause</button>
                </div>
                <div class="panel-body" id="privacy-sections-container">
                    @foreach ($privacy['sections'] ?? [] as $idx => $sec)
                        <div class="repeater-item">
                            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕
                                Remove</button>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <div>
                                    <label style="font-size:10px;font-weight:700">Section Title (EN)</label>
                                    <input class="input" style="width:100%;margin-bottom:6px"
                                        name="page_privacy[sections][{{ $idx }}][title]"
                                        value="{{ $sec['title'] ?? '' }}">
                                    <label style="font-size:10px;font-weight:700">Content (EN)</label>
                                    <textarea class="input" style="width:100%;height:80px" name="page_privacy[sections][{{ $idx }}][content]">{{ $sec['content'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700">Section Title (AR)</label>
                                    <input class="input" style="width:100%;margin-bottom:6px"
                                        name="page_privacy[sections][{{ $idx }}][title_ar]"
                                        value="{{ $sec['title_ar'] ?? '' }}">
                                    <label style="font-size:10px;font-weight:700">Content (AR)</label>
                                    <textarea class="input" style="width:100%;height:80px"
                                        name="page_privacy[sections][{{ $idx }}][content_ar]">{{ $sec['content_ar'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- TAB 6: TERMS OF SERVICE --}}
        <div id="tab-terms" class="tab-content">
            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Terms of Service Header</h3>
                </div>
                <div class="panel-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (EN)</label>
                        <input class="input" style="width:100%" name="page_terms[title]"
                            value="{{ $terms['title'] ?? 'Terms of Service' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Title (AR)</label>
                        <input class="input" style="width:100%" name="page_terms[title_ar]"
                            value="{{ $terms['title_ar'] ?? 'شروط الاستخدام' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (EN)</label>
                        <input class="input" style="width:100%" name="page_terms[subtitle]"
                            value="{{ $terms['subtitle'] ?? 'Terms and licensing conditions governing your use of EzyTemplate.' }}">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Subtitle (AR)</label>
                        <input class="input" style="width:100%" name="page_terms[subtitle_ar]"
                            value="{{ $terms['subtitle_ar'] ?? 'الشروط والتراخيص التي تنظم استخدامك لمنصة وقوالب إيزي تيمبلت.' }}">
                    </div>
                    <div style="grid-column: 1 / -1">
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Last Updated
                            Date</label>
                        <input class="input" style="width:100%" name="page_terms[last_updated]"
                            value="{{ $terms['last_updated'] ?? 'January 2026' }}">
                    </div>
                </div>
            </section>

            <section class="panel" style="margin-bottom:24px">
                <div class="panel-head">
                    <h3>Terms & Licensing Clauses</h3>
                    <button type="button" class="btn secondary" onclick="addTermsSection()">+ Add Clause</button>
                </div>
                <div class="panel-body" id="terms-sections-container">
                    @foreach ($terms['sections'] ?? [] as $idx => $sec)
                        <div class="repeater-item">
                            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕
                                Remove</button>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <div>
                                    <label style="font-size:10px;font-weight:700">Section Title (EN)</label>
                                    <input class="input" style="width:100%;margin-bottom:6px"
                                        name="page_terms[sections][{{ $idx }}][title]"
                                        value="{{ $sec['title'] ?? '' }}">
                                    <label style="font-size:10px;font-weight:700">Content (EN)</label>
                                    <textarea class="input" style="width:100%;height:80px" name="page_terms[sections][{{ $idx }}][content]">{{ $sec['content'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700">Section Title (AR)</label>
                                    <input class="input" style="width:100%;margin-bottom:6px"
                                        name="page_terms[sections][{{ $idx }}][title_ar]"
                                        value="{{ $sec['title_ar'] ?? '' }}">
                                    <label style="font-size:10px;font-weight:700">Content (AR)</label>
                                    <textarea class="input" style="width:100%;height:80px"
                                        name="page_terms[sections][{{ $idx }}][content_ar]">{{ $sec['content_ar'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div
            style="position:sticky;bottom:20px;background:var(--card-bg, #fff);padding:16px;border-radius:10px;box-shadow:0 -4px 12px rgba(0,0,0,0.08);display:flex;justify:space-between;align-items:center;z-index:10">
            <span style="font-size:13px;color:var(--text-muted)">Save changes across all system page tabs.</span>
            <button type="submit" class="btn primary">💾 Save Changes</button>
        </div>
    </form>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            event.target.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        }

        let faqIdx = {{ count($faq['items'] ?? []) }};

        function addFaqItem() {
            const container = document.getElementById('faq-items-container');
            const div = document.createElement('div');
            div.className = 'repeater-item';
            div.innerHTML = `
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕ Remove</button>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px">
                <div>
                    <label style="font-size:10px;font-weight:700">Question (EN)</label>
                    <input class="input" style="width:100%" name="page_faq[items][${faqIdx}][question]" placeholder="Question in English">
                </div>
                <div>
                    <label style="font-size:10px;font-weight:700">Question (AR)</label>
                    <input class="input" style="width:100%" name="page_faq[items][${faqIdx}][question_ar]" placeholder="السؤال بالعربية">
                </div>
                <div>
                    <label style="font-size:10px;font-weight:700">Answer (EN)</label>
                    <textarea class="input" style="width:100%;height:60px" name="page_faq[items][${faqIdx}][answer]" placeholder="Answer in English"></textarea>
                </div>
                <div>
                    <label style="font-size:10px;font-weight:700">Answer (AR)</label>
                    <textarea class="input" style="width:100%;height:60px" name="page_faq[items][${faqIdx}][answer_ar]" placeholder="الإجابة بالعربية"></textarea>
                </div>
                <div style="grid-column: 1 / -1">
                    <label style="font-size:10px;font-weight:700">Category Tag</label>
                    <input class="input" style="width:100%" name="page_faq[items][${faqIdx}][category]" value="General">
                </div>
            </div>
        `;
            container.appendChild(div);
            faqIdx++;
        }

        let privacyIdx = {{ count($privacy['sections'] ?? []) }};

        function addPrivacySection() {
            const container = document.getElementById('privacy-sections-container');
            const div = document.createElement('div');
            div.className = 'repeater-item';
            div.innerHTML = `
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕ Remove</button>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="font-size:10px;font-weight:700">Section Title (EN)</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="page_privacy[sections][${privacyIdx}][title]">
                    <label style="font-size:10px;font-weight:700">Content (EN)</label>
                    <textarea class="input" style="width:100%;height:80px" name="page_privacy[sections][${privacyIdx}][content]"></textarea>
                </div>
                <div>
                    <label style="font-size:10px;font-weight:700">Section Title (AR)</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="page_privacy[sections][${privacyIdx}][title_ar]">
                    <label style="font-size:10px;font-weight:700">Content (AR)</label>
                    <textarea class="input" style="width:100%;height:80px" name="page_privacy[sections][${privacyIdx}][content_ar]"></textarea>
                </div>
            </div>
        `;
            container.appendChild(div);
            privacyIdx++;
        }

        let termsIdx = {{ count($terms['sections'] ?? []) }};

        function addTermsSection() {
            const container = document.getElementById('terms-sections-container');
            const div = document.createElement('div');
            div.className = 'repeater-item';
            div.innerHTML = `
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕ Remove</button>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="font-size:10px;font-weight:700">Section Title (EN)</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="page_terms[sections][${termsIdx}][title]">
                    <label style="font-size:10px;font-weight:700">Content (EN)</label>
                    <textarea class="input" style="width:100%;height:80px" name="page_terms[sections][${termsIdx}][content]"></textarea>
                </div>
                <div>
                    <label style="font-size:10px;font-weight:700">Section Title (AR)</label>
                    <input class="input" style="width:100%;margin-bottom:6px" name="page_terms[sections][${termsIdx}][title_ar]">
                    <label style="font-size:10px;font-weight:700">Content (AR)</label>
                    <textarea class="input" style="width:100%;height:80px" name="page_terms[sections][${termsIdx}][content_ar]"></textarea>
                </div>
            </div>
        `;
            container.appendChild(div);
            termsIdx++;
        }
    </script>
@endsection
