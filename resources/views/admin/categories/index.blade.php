@extends('admin.layouts.app')
@section('section', 'CATALOG / CATEGORIES')
@section('heading', 'Categories (التصنيفات والأقسام)')

@section('content')
<div class="grid2">
    <!-- Category List Table -->
    <section class="panel">
        <div class="panel-head">
            <h3>Categories List (قائمة التصنيفات)</h3>
            <span class="badge">{{ $categories->count() }} Categories</span>
        </div>
        <div class="responsive-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Arabic Name (الاسم بالعربي)</th>
                        <th>Resources</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $c)
                    <tr>
                        <td>
                            <b>{{ $c->name }}</b>
                            <br><small style="color:#64748b">{{ $c->slug }}</small>
                        </td>
                        <td>
                            <span style="font-family:'Cairo',sans-serif;font-weight:600">{{ $c->name_ar ?? '—' }}</span>
                        </td>
                        <td><span class="badge">{{ $c->resources_count }} templates</span></td>
                        <td>
                            <span class="badge" style="background:{{ $c->is_active ? '#dcfce7;color:#15803d' : '#f1f5f9;color:#64748b' }}">
                                {{ $c->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- Add Category Form -->
    <section class="panel">
        <div class="panel-head">
            <h3>Add New Category (إضافة تصنيف جديد)</h3>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Category Name (English) *</label>
                    <input class="input" style="width:100%" name="name" placeholder="e.g. Web Development, Dashboard, Mobile" required>
                </div>

                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">اسم التصنيف بالعربية (Arabic Name)</label>
                    <input class="input" style="width:100%;font-family:'Cairo',sans-serif;text-align:right" name="name_ar" dir="rtl" placeholder="مثال: تطوير المواقع، لوحات التحكم، تطبيقات الموبايل">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Icon / Key</label>
                        <input class="input" style="width:100%" name="icon" placeholder="Layout, Globe, FileSpreadsheet">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Badge Color</label>
                        <input class="input" style="width:100%" name="color" value="#2563EB" placeholder="#2563EB">
                    </div>
                </div>

                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">Description (English)</label>
                    <textarea class="input" style="width:100%;height:70px;padding:8px" name="description" placeholder="Brief category description in English..."></textarea>
                </div>

                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:11px;font-weight:700;margin-bottom:4px">الوصف بالعربية (Arabic Description)</label>
                    <textarea class="input" style="width:100%;height:70px;padding:8px;font-family:'Cairo',sans-serif;text-align:right" name="description_ar" dir="rtl" placeholder="نبذة مختصرة عن التصنيف باللغة العربية..."></textarea>
                </div>

                <button class="btn" style="width:100%;padding:10px">Create Category (حفظ التصنيف)</button>
            </form>
        </div>
    </section>
</div>
@endsection
