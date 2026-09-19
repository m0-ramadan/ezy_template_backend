<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Resource, Category, Subcategory, ResourceFile};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index(Request $r)
    {
        $q = Resource::with(['category', 'subcategory'])->latest();
        if ($r->filled('search')) $q->where('title', 'like', '%' . $r->search . '%');
        if ($r->filled('type')) $q->where('resource_type', $r->type);
        return view('admin.resources.index', [
            'resources' => $q->paginate(15),
            'filters' => $r->only(['search', 'type'])
        ]);
    }

    public function create()
    {
        return view('admin.resources.form', [
            'resource' => new Resource,
            'categories' => Category::with('subcategories')->orderBy('name')->get(),
            'subcategories' => Subcategory::with('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $d = $this->validateData($r);
        $d['slug'] = Str::slug($d['title']) . '-' . Str::lower(Str::random(5));
        $d = $this->processUploadsAndStructuredFields($r, $d);

        $resource = Resource::create($d);
        $this->files($r, $resource);

        return redirect()->route('admin.resources.edit', $resource)->with('success', 'Template resource created successfully.');
    }

    public function edit(Resource $resource)
    {
        $resource->load('files');
        return view('admin.resources.form', [
            'resource' => $resource,
            'categories' => Category::with('subcategories')->orderBy('name')->get(),
            'subcategories' => Subcategory::with('category')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $r, Resource $resource)
    {
        $d = $this->validateData($r);
        $d['slug'] = Str::slug($d['title']);
        if (Resource::where('slug', $d['slug'])->where('id', '<>', $resource->id)->exists()) {
            $d['slug'] .= '-' . Str::lower(Str::random(4));
        }
        $d = $this->processUploadsAndStructuredFields($r, $d, $resource);

        $resource->update($d);
        $this->files($r, $resource);

        return back()->with('success', 'Template resource updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        foreach ($resource->files as $f) {
            Storage::disk('public')->delete($f->path);
        }
        $resource->delete();
        return back()->with('success', 'Resource deleted.');
    }

    private function validateData(Request $r): array
    {
        return $r->validate([
            'title' => 'required|string|max:180',
            'title_ar' => 'nullable|string|max:180',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'resource_type' => 'required|in:website,excel,word,design,presentation,ui-kit,other',
            'short_description' => 'nullable|string|max:500',
            'short_description_ar' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'preview_image' => 'nullable|string|max:500',
            'detail_image' => 'nullable|string|max:500',
            'tech_stack' => 'nullable|string|max:255',
            'tech_stack_ar' => 'nullable|string|max:255',
            'featured' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0',
            'version' => 'nullable|string|max:30',
            'license' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'demo_url' => 'nullable|url|max:500',
            'seo_title' => 'nullable|string|max:180',
            'seo_description' => 'nullable|string|max:500',
            'seo_title_ar' => 'nullable|string|max:180',
            'seo_description_ar' => 'nullable|string|max:500',
            'image_alt' => 'nullable|string|max:255',
            'image_alt_ar' => 'nullable|string|max:255',
            'canva_design_id' => 'nullable|string|max:100',
            'canva_url' => 'nullable|url|max:2000',
            'source_url' => 'nullable|url|max:2000',
            'external_url' => 'nullable|url|max:2000',
            'gallery_status' => 'nullable|string|max:30',
        ]);
    }

    private function processUploadsAndStructuredFields(Request $r, array $d, ?Resource $resource = null): array
    {
        $d['featured'] = $r->boolean('featured');
        $d['is_free'] = $r->boolean('is_free');

        if ($r->hasFile('preview_image_file')) {
            $path = $r->file('preview_image_file')->store('resources/previews', 'public');
            $d['preview_image'] = '/storage/' . $path;
        }
        if ($r->hasFile('detail_image_file')) {
            $path = $r->file('detail_image_file')->store('resources/details', 'public');
            $d['detail_image'] = '/storage/' . $path;
        }

        $d['features'] = $this->parseLines($r->input('features_raw'));
        $d['features_ar'] = $this->parseLines($r->input('features_raw_ar'));
        $d['seo_keywords'] = $this->parseLines($r->input('seo_keywords_raw'));

        $screenshots = $this->parseLines($r->input('screenshots_raw'));
        if ($r->hasFile('screenshot_files')) {
            foreach ($r->file('screenshot_files') as $file) {
                $path = $file->store('resources/screenshots', 'public');
                $screenshots[] = '/storage/' . $path;
            }
        }
        $d['screenshots'] = !empty($screenshots) ? array_values(array_unique($screenshots)) : null;

        return $d;
    }

    private function parseLines(?string $input): array
    {
        if (empty($input)) return [];
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $input))));
    }

    private function files(Request $r, Resource $resource): void
    {
        if (!$r->hasFile('files')) return;
        foreach ($r->file('files') as $i => $file) {
            $path = $file->store('resources/' . $resource->id, 'public');
            ResourceFile::create([
                'resource_id' => $resource->id,
                'label' => $file->getClientOriginalName(),
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'quality' => $r->input('quality', 'Standard'),
                'format' => strtoupper($file->getClientOriginalExtension()),
                'is_primary' => $i === 0 && !$resource->files()->where('is_primary', true)->exists(),
                'sort_order' => $i
            ]);
        }
    }
}
