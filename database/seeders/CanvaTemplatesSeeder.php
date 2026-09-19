<?php

namespace Database\Seeders;

use App\Models\{MainCategory, Category, Subcategory, Resource, Tag};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class CanvaTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/canva_templates_188.json');
        if (!is_file($file)) {
            throw new RuntimeException('Missing data file: ' . $file);
        }

        $items = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        if (count($items) !== 188) {
            throw new RuntimeException('Expected exactly 188 Canva templates, found ' . count($items));
        }

        DB::transaction(function () use ($items) {
            $mainCategoryId = null;
            if (Schema::hasTable('main_categories')) {
                $main = MainCategory::updateOrCreate(
                    ['slug' => 'design-templates'],
                    [
                        'name' => 'Design Templates',
                        'name_ar' => 'قوالب التصميم',
                        'icon' => 'palette',
                        'color' => '#7C3AED',
                        'description' => 'Editable visual design templates and creative resources.',
                        'description_ar' => 'قوالب تصميم وموارد إبداعية قابلة للتخصيص.',
                        'sort_order' => 3,
                        'is_active' => true,
                    ]
                );
                $mainCategoryId = $main->id;
            }

            $categoryData = [
                'name' => 'Canva Templates',
                'name_ar' => 'قوالب Canva',
                'slug' => 'canva-templates',
                'description' => 'Editable Canva templates organized by industry and use case.',
                'description_ar' => 'قوالب Canva قابلة للتعديل ومقسمة حسب المجال والاستخدام.',
                'icon' => 'palette',
                'color' => '#7C3AED',
                'sort_order' => 0,
                'is_active' => true,
            ];
            if ($mainCategoryId && Schema::hasColumn('categories', 'main_category_id')) {
                $categoryData['main_category_id'] = $mainCategoryId;
            }

            $canvaCategory = Category::updateOrCreate(['slug' => 'canva-templates'], $categoryData);

            $subMap = [];
            $subCategories = collect($items)
                ->map(fn($x) => [$x['category'], $x['category_ar'], $x['category_slug']])
                ->unique(fn($x) => $x[2])
                ->values();

            foreach ($subCategories as $index => [$name, $nameAr, $slug]) {
                $sub = Subcategory::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $canvaCategory->id,
                        'name' => $name,
                        'name_ar' => $nameAr,
                        'description' => "Editable Canva templates for {$name}.",
                        'description_ar' => "قوالب Canva قابلة للتعديل ضمن قسم {$nameAr}.",
                        'sort_order' => $index,
                        'is_active' => true,
                    ]
                );
                $subMap[$slug] = $sub->id;
            }

            $featuredSubcategories = [];

            foreach ($items as $index => $item) {
                $resourceData = [
                    'category_id' => $canvaCategory->id,
                    'title' => $item['title'],
                    'title_ar' => $item['title_ar'],
                    'resource_type' => 'design',
                    'tech_stack' => 'Canva',
                    'tech_stack_ar' => 'Canva',
                    'description' => $item['description'],
                    'description_ar' => $item['description_ar'],
                    'features' => $item['features'],
                    'features_ar' => $item['features_ar'],
                    'short_description' => $item['short_description'],
                    'short_description_ar' => $item['short_description_ar'],
                    'preview_image' => $item['preview_image'],
                    'detail_image' => $item['detail_image'],
                    'screenshots' => $item['screenshots'],
                    'featured' => !isset($featuredSubcategories[$item['category_slug']]),
                    'is_free' => true,
                    'price' => 0,
                    'version' => $item['version'] ?? '1.0.0',
                    'license' => $item['license'],
                    'downloads_count' => 0,
                    'rating' => 0,
                    'reviews_count' => 0,
                    'published_at' => now(),
                    'status' => 'published',
                    'demo_url' => $item['canva_preview_url'],
                    'seo_title' => $item['seo_title'],
                    'seo_description' => $item['seo_description'],
                    'seo_title_ar' => $item['seo_title_ar'],
                    'seo_description_ar' => $item['seo_description_ar'],
                    'seo_keywords' => $item['seo_keywords'],
                    'image_alt' => $item['image_alt'],
                    'image_alt_ar' => $item['image_alt_ar'],
                    'canva_design_id' => $item['canva_design_id'],
                    'canva_url' => $item['canva_preview_url'],
                    'source_url' => $item['source_url'],
                    'external_url' => $item['external_url'],
                    'gallery_status' => $item['gallery_status'] ?? 'placeholder',
                ];

                if (Schema::hasColumn('resources', 'media_sync_status')) {
                    $resourceData['media_sync_status'] = 'pending';
                    $resourceData['media_width'] = 1200;
                    $resourceData['media_height'] = 900;
                    $resourceData['media_sync_message'] = 'Run php artisan templates:finalize-media to replace generated media with source-specific Canva previews.';
                }

                if (Schema::hasColumn('resources', 'subcategory_id')) {
                    $resourceData['subcategory_id'] = $subMap[$item['category_slug']] ?? null;
                }
                if (Schema::hasColumn('resources', 'main_category_id')) {
                    $resourceData['main_category_id'] = $mainCategoryId;
                }

                $resource = Resource::updateOrCreate(
                    ['slug' => $item['slug']],
                    $resourceData
                );

                $featuredSubcategories[$item['category_slug']] = true;

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

        $this->command?->info('Seeded 188 Canva templates into EzyTemplate.');
    }
}
