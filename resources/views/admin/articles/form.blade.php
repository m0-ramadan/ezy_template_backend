@extends('admin.layouts.app')
@section('section', 'CONTENT / ARTICLES')
@section('heading', $article->exists ? 'Edit Article: ' . $article->title : 'New Article')

@section('content')
    <form method="POST" enctype="multipart/form-data"
        action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <div class="grid2">
            <!-- Left Column: English & Arabic Content -->
            <div style="display:flex;flex-direction:column;gap:18px">
                <!-- English Article Content -->
                <section class="panel" style="border-left:4px solid #3b82f6">
                    <div class="panel-head">
                        <h3 style="display:flex;align-items:center;gap:8px">
                            <span>🇬🇧</span>
                            <span>English Content (المحتوى بالإنجليزية)</span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Article Title (English) *</label>
                            <input class="input" style="width:100%" name="title" value="{{ old('title', $article->title) }}"
                                placeholder="e.g. 10 Web Design Trends to Watch in 2026" required>
                        </div>

                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Excerpt / Intro (English)</label>
                            <textarea class="input" style="width:100%;height:65px;padding:8px" name="excerpt"
                                placeholder="Short summary for cards and search results in English">{{ old('excerpt', $article->excerpt) }}</textarea>
                        </div>

                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Full Content (English - Rich Text Editor) *</label>
                            <textarea class="rich-editor" style="width:100%;height:280px;display:none" name="content" required
                                placeholder="Write full article content here in English...">{{ old('content', $article->content) }}</textarea>
                        </div>
                    </div>
                </section>

                <!-- Arabic Article Content -->
                <section class="panel" style="border-left:4px solid #10b981">
                    <div class="panel-head">
                        <h3 style="display:flex;align-items:center;gap:8px">
                            <span>🇸🇦</span>
                            <span>Arabic Content (المحتوى باللغة العربية)</span>
                        </h3>
                    </div>
                    <div class="panel-body" style="direction:rtl;text-align:right">
                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">عنوان المقال بالعربية (Arabic Title)</label>
                            <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="title_ar" dir="rtl"
                                value="{{ old('title_ar', $article->title_ar) }}"
                                placeholder="مثال: أبرز 10 اتجاهات في تصميم وتطوير المواقع لعام 2026">
                        </div>

                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">مقدمة وملخص المقال بالعربية (Excerpt AR)</label>
                            <textarea class="input" style="width:100%;height:65px;padding:8px;font-family:'Cairo',sans-serif;text-align:right" name="excerpt_ar" dir="rtl"
                                placeholder="ملخص سريع يظهر في كارت المقال بالمدونة بالعربية">{{ old('excerpt_ar', $article->excerpt_ar) }}</textarea>
                        </div>

                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">محتوى المقال الكامل بالعربية (Full Content AR - محرر منسق)</label>
                            <textarea class="rich-editor" style="width:100%;height:280px;display:none" name="content_ar" dir="rtl"
                                placeholder="اكتب محتوى المقال الكامل والمنسق باللغة العربية...">{{ old('content_ar', $article->content_ar) }}</textarea>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column: Meta, Categories, Cover, SEO -->
            <div style="display:flex;flex-direction:column;gap:18px">
                <section class="panel">
                    <div class="panel-head">
                        <h3>Publishing & Metadata (بيانات النشر والتصنيف)</h3>
                    </div>
                    <div class="panel-body">
                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Category Tag</label>
                            <input class="input" style="width:100%" name="category"
                                value="{{ old('category', $article->category ?? 'Web Design') }}"
                                placeholder="e.g. Next.js, Design Trends, Tutorials">
                        </div>

                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Author Name</label>
                            <input class="input" style="width:100%" name="author_name"
                                value="{{ old('author_name', $article->author_name ?? 'EzyTemplate Team') }}"
                                placeholder="Author name">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Status</label>
                                <select class="select" style="width:100%" name="status">
                                    <option value="draft" @selected(old('status', $article->status) === 'draft')>Draft</option>
                                    <option value="published" @selected(old('status', $article->status ?? 'published') === 'published')>Published</option>
                                    <option value="archived" @selected(old('status', $article->status) === 'archived')>Archived</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Reading Time (Min)</label>
                                <input class="input" style="width:100%" type="number" name="reading_time"
                                    value="{{ old('reading_time', $article->reading_time ?? 8) }}">
                            </div>
                        </div>

                        <div style="margin-bottom:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">Cover Image (صورة الغلاف)</label>
                            <input class="input" style="width:100%;margin-bottom:6px" name="cover_image"
                                value="{{ old('cover_image', $article->cover_image) }}"
                                placeholder="/assets/... or https://...">
                            <input type="file" name="cover_image_file" accept="image/*" class="input"
                                style="width:100%;padding:4px">
                            @if ($article->cover_image)
                                <div style="margin-top:10px">
                                    <img src="{{ $article->cover_image }}"
                                        style="height:70px;border-radius:6px;border:1px solid #e2e8f0">
                                </div>
                            @endif
                        </div>

                        <div style="margin-bottom:14px;border-top:1px solid #eef2f7;padding-top:14px">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">SEO Meta Title</label>
                            <input class="input" style="width:100%;margin-bottom:8px" name="meta_title"
                                value="{{ old('meta_title', $article->meta_title) }}" placeholder="Custom meta title">
                            <label style="display:block;font-size:11px;font-weight:700;margin-bottom:6px">SEO Meta Description</label>
                            <textarea class="input" style="width:100%;height:60px;padding:6px" name="meta_description"
                                placeholder="Custom meta description">{{ old('meta_description', $article->meta_description) }}</textarea>
                        </div>

                        <button class="btn" style="width:100%;padding:12px;font-size:13px">Save Article (حفظ المقال)</button>
                    </div>
                </section>
            </div>
        </div>
    </form>
@endsection
