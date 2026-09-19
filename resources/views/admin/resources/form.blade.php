@extends('admin.layouts.app')
@section('section', 'CATALOG / RESOURCES')
@section('heading', $resource->exists ? 'Edit Resource: ' . $resource->title : 'Add New Resource')

@section('content')
<form method="POST" enctype="multipart/form-data"
    action="{{ $resource->exists ? route('admin.resources.update', $resource) : route('admin.resources.store') }}">
    @csrf
    @if($resource->exists) @method('PUT') @endif

    <div class="grid2">
        <div style="display:flex;flex-direction:column;gap:18px">
            <section class="panel">
                <div class="panel-head"><h3>Classification & Core Information</h3></div>
                <div class="panel-body">
                    <div style="margin-bottom:12px">
                        <label>Template Title</label>
                        <input class="input" style="width:100%" name="title" value="{{ old('title', $resource->title) }}" required>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                        <div>
                            <label>Resource Type</label>
                            <select class="select" style="width:100%" name="resource_type">
                                @foreach(['website','excel','word','design','presentation','ui-kit','other'] as $t)
                                    <option value="{{ $t }}" @selected(old('resource_type', $resource->resource_type ?: 'design') === $t)>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Category</label>
                            <select class="select" style="width:100%" name="category_id" id="category_id">
                                <option value="">No category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id', $resource->category_id) == $c->id)>{{ $c->name }}{{ $c->name_ar ? ' / '.$c->name_ar : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="margin-bottom:12px">
                        <label>Subcategory</label>
                        <select class="select" style="width:100%" name="subcategory_id" id="subcategory_id">
                            <option value="">No subcategory</option>
                            @foreach($subcategories as $s)
                                <option value="{{ $s->id }}" data-category="{{ $s->category_id }}" @selected(old('subcategory_id', $resource->subcategory_id) == $s->id)>
                                    {{ $s->category?->name }} → {{ $s->name }}{{ $s->name_ar ? ' / '.$s->name_ar : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div><label>Version</label><input class="input" style="width:100%" name="version" value="{{ old('version', $resource->version ?? '1.0.0') }}"></div>
                        <div><label>License</label><input class="input" style="width:100%" name="license" value="{{ old('license', $resource->license) }}"></div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>English Content</h3></div>
                <div class="panel-body">
                    <div style="margin-bottom:12px"><label>Short Description</label><textarea class="input" style="width:100%;height:75px" name="short_description">{{ old('short_description', $resource->short_description) }}</textarea></div>
                    <div style="margin-bottom:12px"><label>Full Description</label><textarea class="input" style="width:100%;height:220px" name="description">{{ old('description', $resource->description) }}</textarea></div>
                    <div style="margin-bottom:12px"><label>Tech / Platform</label><input class="input" style="width:100%" name="tech_stack" value="{{ old('tech_stack', $resource->tech_stack) }}"></div>
                    <div><label>Features — one per line</label><textarea class="input" style="width:100%;height:120px" name="features_raw">{{ old('features_raw', is_array($resource->features) ? implode("\n", $resource->features) : '') }}</textarea></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>Arabic Content</h3></div>
                <div class="panel-body" dir="rtl">
                    <div style="margin-bottom:12px"><label>اسم القالب</label><input class="input" style="width:100%" name="title_ar" value="{{ old('title_ar', $resource->title_ar) }}"></div>
                    <div style="margin-bottom:12px"><label>الوصف المختصر</label><textarea class="input" style="width:100%;height:75px" name="short_description_ar">{{ old('short_description_ar', $resource->short_description_ar) }}</textarea></div>
                    <div style="margin-bottom:12px"><label>الوصف الكامل</label><textarea class="input" style="width:100%;height:220px" name="description_ar">{{ old('description_ar', $resource->description_ar) }}</textarea></div>
                    <div style="margin-bottom:12px"><label>المنصة / التقنية</label><input class="input" style="width:100%" name="tech_stack_ar" value="{{ old('tech_stack_ar', $resource->tech_stack_ar) }}"></div>
                    <div><label>المميزات — ميزة في كل سطر</label><textarea class="input" style="width:100%;height:120px" name="features_raw_ar">{{ old('features_raw_ar', is_array($resource->features_ar) ? implode("\n", $resource->features_ar) : '') }}</textarea></div>
                </div>
            </section>
        </div>

        <div style="display:flex;flex-direction:column;gap:18px">
            <section class="panel">
                <div class="panel-head"><h3>Canva Source</h3></div>
                <div class="panel-body">
                    <div style="margin-bottom:10px"><label>Canva Design ID</label><input class="input" style="width:100%" name="canva_design_id" value="{{ old('canva_design_id', $resource->canva_design_id) }}"></div>
                    <div style="margin-bottom:10px"><label>Canva Preview URL</label><input class="input" style="width:100%" type="url" name="canva_url" value="{{ old('canva_url', $resource->canva_url) }}"></div>
                    <div style="margin-bottom:10px"><label>Original Source URL</label><input class="input" style="width:100%" type="url" name="source_url" value="{{ old('source_url', $resource->source_url) }}"></div>
                    <div style="margin-bottom:10px"><label>External / Edit in Canva URL</label><input class="input" style="width:100%" type="url" name="external_url" value="{{ old('external_url', $resource->external_url) }}"></div>
                    <div><label>Gallery Status</label><input class="input" style="width:100%" name="gallery_status" value="{{ old('gallery_status', $resource->gallery_status ?: 'placeholder') }}"></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>SEO</h3></div>
                <div class="panel-body">
                    <div style="margin-bottom:10px"><label>SEO Title</label><input class="input" style="width:100%" name="seo_title" value="{{ old('seo_title', $resource->seo_title) }}"></div>
                    <div style="margin-bottom:10px"><label>SEO Description</label><textarea class="input" style="width:100%;height:85px" name="seo_description">{{ old('seo_description', $resource->seo_description) }}</textarea></div>
                    <div style="margin-bottom:10px" dir="rtl"><label>عنوان SEO بالعربية</label><input class="input" style="width:100%" name="seo_title_ar" value="{{ old('seo_title_ar', $resource->seo_title_ar) }}"></div>
                    <div style="margin-bottom:10px" dir="rtl"><label>وصف SEO بالعربية</label><textarea class="input" style="width:100%;height:85px" name="seo_description_ar">{{ old('seo_description_ar', $resource->seo_description_ar) }}</textarea></div>
                    <div><label>SEO Keywords — one per line</label><textarea class="input" style="width:100%;height:110px" name="seo_keywords_raw">{{ old('seo_keywords_raw', is_array($resource->seo_keywords) ? implode("\n", $resource->seo_keywords) : '') }}</textarea></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>Media Gallery</h3></div>
                <div class="panel-body">
                    <div style="margin-bottom:14px"><label>Cover / Composite Image</label><input class="input" style="width:100%;margin-bottom:6px" name="preview_image" value="{{ old('preview_image', $resource->preview_image) }}"><input class="input" style="width:100%" type="file" name="preview_image_file" accept="image/*"></div>
                    <div style="margin-bottom:14px"><label>Detail Image</label><input class="input" style="width:100%;margin-bottom:6px" name="detail_image" value="{{ old('detail_image', $resource->detail_image) }}"><input class="input" style="width:100%" type="file" name="detail_image_file" accept="image/*"></div>
                    <div style="margin-bottom:14px"><label>English Image Alt</label><input class="input" style="width:100%" name="image_alt" value="{{ old('image_alt', $resource->image_alt) }}"></div>
                    <div style="margin-bottom:14px" dir="rtl"><label>وصف الصورة بالعربية</label><input class="input" style="width:100%" name="image_alt_ar" value="{{ old('image_alt_ar', $resource->image_alt_ar) }}"></div>
                    <div><label>Screenshots — one path/URL per line</label><textarea class="input" style="width:100%;height:110px" name="screenshots_raw">{{ old('screenshots_raw', is_array($resource->screenshots) ? implode("\n", $resource->screenshots) : '') }}</textarea><input class="input" style="width:100%;margin-top:6px" type="file" name="screenshot_files[]" multiple accept="image/*"></div>
                    @if(is_array($resource->screenshots) && count($resource->screenshots))
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
                            @foreach($resource->screenshots as $shot)<img src="{{ $shot }}" style="width:95px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #cbd5e1">@endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>Publishing & Links</h3></div>
                <div class="panel-body">
                    <div style="display:flex;gap:18px;margin-bottom:12px"><label><input type="checkbox" name="is_free" value="1" @checked(old('is_free', $resource->is_free ?? true))> Free</label><label><input type="checkbox" name="featured" value="1" @checked(old('featured', $resource->featured))> Featured</label></div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px"><div><label>Price</label><input class="input" style="width:100%" type="number" step="0.01" name="price" value="{{ old('price', $resource->price ?? 0) }}"></div><div><label>Status</label><select class="select" style="width:100%" name="status">@foreach(['draft','published','archived'] as $s)<option value="{{ $s }}" @selected(old('status', $resource->status ?: 'published') === $s)>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
                    <div style="margin-bottom:12px"><label>Published At</label><input class="input" style="width:100%" type="datetime-local" name="published_at" value="{{ old('published_at', optional($resource->published_at)->format('Y-m-d\\TH:i')) }}"></div>
                    <div style="margin-bottom:12px"><label>Demo URL</label><input class="input" style="width:100%" type="url" name="demo_url" value="{{ old('demo_url', $resource->demo_url) }}"></div>
                    <div><label>Downloadable Files</label><input class="input" style="width:100%" type="file" name="files[]" multiple></div>
                </div>
            </section>

            <button class="btn" style="width:100%;padding:14px">Save Template Resource</button>
        </div>
    </div>
</form>

<script>
(function () {
    const category = document.getElementById('category_id');
    const sub = document.getElementById('subcategory_id');
    if (!category || !sub) return;
    function filterSubcategories() {
        const selected = category.value;
        [...sub.options].forEach((opt, i) => {
            if (i === 0) return;
            opt.hidden = selected && opt.dataset.category !== selected;
        });
        if (sub.selectedOptions[0]?.hidden) sub.value = '';
    }
    category.addEventListener('change', filterSubcategories);
    filterSubcategories();
})();
</script>
@endsection
