<?php

namespace Database\Seeders;

use App\Models\{MainCategory, Category, Subcategory, Resource, ResourceFile, Tag};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class OfficeTemplateLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/office_templates_117.json');
        if (!is_file($file)) {
            throw new RuntimeException('Missing data file: ' . $file);
        }

        $items = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        if (count($items) !== 117) {
            throw new RuntimeException('Expected exactly 117 office templates, found ' . count($items));
        }

        DB::transaction(function () use ($items) {
            $mainMap = [];
            $categoryMap = [];
            $subcategoryMap = [];

            foreach ($items as $index => $item) {
                $mainSlug = $item['main_category_slug'];
                if (!isset($mainMap[$mainSlug])) {
                    $main = MainCategory::updateOrCreate(
                        ['slug' => $mainSlug],
                        [
                            'name' => $item['main_category_name'],
                            'name_ar' => $item['main_category_name_ar'],
                            'icon' => $item['icon'],
                            'color' => $item['color'],
                            'description' => 'Editable office templates and source files for practical business use.',
                            'description_ar' => 'قوالب مكتبية وملفات مصدر قابلة للتعديل للاستخدام العملي في الأعمال.',
                            'sort_order' => match ($mainSlug) {
                                'excel-templates' => 1,
                                'word-templates' => 2,
                                'presentation-templates' => 4,
                                default => 10,
                            },
                            'is_active' => true,
                        ]
                    );
                    $mainMap[$mainSlug] = $main->id;
                }

                $categorySlug = $item['category_slug'];
                if (!isset($categoryMap[$categorySlug])) {
                    $category = Category::updateOrCreate(
                        ['slug' => $categorySlug],
                        [
                            'main_category_id' => $mainMap[$mainSlug],
                            'name' => $item['category_name'],
                            'name_ar' => $item['category_name_ar'],
                            'description' => 'Curated editable templates with downloadable source files, SEO metadata and preview galleries.',
                            'description_ar' => 'قوالب قابلة للتعديل مع ملفات مصدر للتنزيل وبيانات SEO وصور معاينة للمعرض.',
                            'icon' => $item['icon'],
                            'color' => $item['color'],
                            'sort_order' => 90,
                            'is_active' => true,
                        ]
                    );
                    $categoryMap[$categorySlug] = $category->id;
                }

                $subcategorySlug = $item['subcategory_slug'];
                if (!isset($subcategoryMap[$subcategorySlug])) {
                    $subcategory = Subcategory::updateOrCreate(
                        ['slug' => $subcategorySlug],
                        [
                            'category_id' => $categoryMap[$categorySlug],
                            'name' => $item['subcategory_name'],
                            'name_ar' => $item['subcategory_name_ar'],
                            'description' => 'Editable ' . $item['subcategory_name'] . ' templates.',
                            'description_ar' => 'قوالب قابلة للتعديل ضمن قسم ' . $item['subcategory_name_ar'] . '.',
                            'sort_order' => count($subcategoryMap),
                            'is_active' => true,
                        ]
                    );
                    $subcategoryMap[$subcategorySlug] = $subcategory->id;
                }
            }

            $featuredBySubcategory = [];

            foreach ($items as $index => $item) {
                $resourceData = [
                    'category_id' => $categoryMap[$item['category_slug']],
                    'main_category_id' => $mainMap[$item['main_category_slug']],
                    'subcategory_id' => $subcategoryMap[$item['subcategory_slug']],
                    'title' => $item['title'],
                    'title_ar' => $item['title_ar'],
                    'resource_type' => $item['resource_type'],
                    'tech_stack' => $item['tech_stack'],
                    'tech_stack_ar' => $item['tech_stack_ar'],
                    'description' => $item['description'],
                    'description_ar' => $item['description_ar'],
                    'features' => $item['features'],
                    'features_ar' => $item['features_ar'],
                    'short_description' => $item['short_description'],
                    'short_description_ar' => $item['short_description_ar'],
                    'preview_image' => $item['preview_image'],
                    'detail_image' => $item['detail_image'],
                    'screenshots' => $item['screenshots'],
                    'featured' => !isset($featuredBySubcategory[$item['subcategory_slug']]),
                    'is_free' => true,
                    'price' => 0,
                    'version' => $item['version'] ?? '1.0.0',
                    'license' => $item['license'],
                    'downloads_count' => 0,
                    'rating' => 0,
                    'reviews_count' => 0,
                    'published_at' => now()->subMinutes(117 - $index),
                    'status' => 'published',
                    'demo_url' => null,
                    'seo_title' => $item['seo_title'],
                    'seo_description' => $item['seo_description'],
                    'seo_title_ar' => $item['seo_title_ar'],
                    'seo_description_ar' => $item['seo_description_ar'],
                    'seo_keywords' => $item['seo_keywords'],
                    'image_alt' => $item['image_alt'],
                    'image_alt_ar' => $item['image_alt_ar'],
                    'canva_design_id' => null,
                    'canva_url' => null,
                    'source_url' => null,
                    'external_url' => null,
                    'gallery_status' => $item['gallery_status'],
                ];

                if (Schema::hasColumn('resources', 'media_sync_status')) {
                    $resourceData['media_sync_status'] = 'synced';
                    $resourceData['media_synced_at'] = now();
                    $resourceData['media_width'] = 1200;
                    $resourceData['media_height'] = 900;
                    $resourceData['media_sync_message'] = null;
                }

                $resource = Resource::updateOrCreate(
                    ['slug' => $item['slug']],
                    $resourceData
                );

                $featuredBySubcategory[$item['subcategory_slug']] = true;

                ResourceFile::updateOrCreate(
                    [
                        'resource_id' => $resource->id,
                        'path' => $item['download_path'],
                    ],
                    [
                        'label' => $item['download_label'],
                        'original_name' => $item['source_original_name'],
                        'extension' => $item['source_ext'],
                        'mime_type' => $item['mime_type'],
                        'size_bytes' => $item['source_size_bytes'],
                        'quality' => $item['file_quality'],
                        'format' => $item['file_format'],
                        'is_primary' => true,
                        'sort_order' => 0,
                    ]
                );

                $tagIds = [];
                foreach ($item['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => Str::headline($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
                $resource->tags()->sync($tagIds);
            }
        });

        $this->command?->info('Seeded 117 uploaded office templates: 111 Excel, 5 Word, 1 PowerPoint.');
    }
}
