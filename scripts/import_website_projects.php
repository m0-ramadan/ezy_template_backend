<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Resource;
use App\Models\ResourceFile;
use App\Models\Subcategory;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$sourceRoot = rtrim($argv[1] ?? '/home/mohamed/Videos/dashboard/بسم الله/tempplate_website', '/');
if (!is_dir($sourceRoot)) {
    fwrite(STDERR, "Source folder does not exist: {$sourceRoot}\n");
    exit(1);
}

$projects = [
    't1' => ['BeFaster Cycling Event', 'قالب فعالية BeFaster للدراجات', 'html-css', 'landing-pages', 'HTML, CSS, JavaScript'],
    't2' => ['Bitco Cryptocurrency', 'قالب Bitco للعملات الرقمية', 'html-css', 'corporate', 'Adobe Muse'],
    't3' => ['Blare Landing Page', 'قالب صفحة هبوط Blare', 'html-css', 'landing-pages', 'Unbounce, PSD'],
    't4' => ['Chameleon App Promo', 'قالب Chameleon لتسويق التطبيقات', 'html-css', 'landing-pages', 'Adobe Muse'],
    't5' => ['BookSeller', 'قالب BookSeller لبيع الكتب الإلكترونية', 'html-css', 'e-commerce', 'HTML, CSS, JavaScript'],
    't6' => ['Cinnamon Grid Theme', 'قالب Cinnamon للمدونات', 'html-css', 'portfolio', 'Tumblr, HTML, CSS'],
    't8' => ['Coxe Corporate', 'قالب Coxe للشركات', 'html-css', 'corporate', 'Adobe Muse'],
    't10' => ['Constructo Construction', 'قالب Constructo لشركات المقاولات', 'html-css', 'corporate', 'Joomla'],
    't11' => ['Clima OpenCart Store', 'قالب متجر Clima', 'html-css', 'e-commerce', 'OpenCart'],
    't12' => ['Creative Agency', 'قالب وكالة إبداعية', 'html-css', 'corporate', 'Adobe Muse'],
    't14' => ['Bonesa Portfolio', 'قالب معرض أعمال Bonesa', 'html-css', 'portfolio', 'Adobe Muse'],
    't16' => ['Century Multipurpose', 'قالب Century متعدد الأغراض', 'html-css', 'corporate', 'Adobe Muse'],
    't19' => ['Brix Mobile App', 'قالب Brix لتطبيقات الموبايل', 'html-css', 'landing-pages', 'Adobe Muse'],
    't20' => ['EzyAdmin Dashboard', 'لوحة تحكم EzyAdmin', 'react-nextjs', 'dashboards', 'Next.js, React, TypeScript, Tailwind CSS'],
    't21' => ['EzyAdmin Bootstrap', 'لوحة تحكم EzyAdmin Bootstrap', 'html-css', 'corporate', 'Bootstrap'],
    't22' => ['OmniMart Store', 'قالب متجر OmniMart', 'html-css', 'e-commerce', 'HTML, CSS, JavaScript'],
    'tw7' => ['Credit Card Experience', 'قالب تجربة بطاقة الائتمان', 'wordpress', 'business', 'WordPress'],
    'tw9' => ['Consto Industrial', 'قالب Consto للصناعة والمقاولات', 'wordpress', 'business', 'WordPress'],
    'tw13' => ['JobScout', 'قالب JobScout للوظائف', 'wordpress', 'business', 'WordPress'],
    'tw15' => ['Bubox Online Store', 'قالب متجر Bubox', 'wordpress', 'shop', 'WordPress, WooCommerce'],
    'tw17' => ['Carlax Automotive', 'قالب Carlax للسيارات', 'wordpress', 'business', 'WordPress'],
    'tw18' => ['Casamia Real Estate', 'قالب Casamia للعقارات', 'wordpress', 'agency', 'WordPress'],
];

$fallbackZipImages = [
    't3' => 'Main File/Images/screenshots/banner-bg.jpg',
    't6' => 'Theme Cinnamon/Documentation/images/prev.png',
];

// Archives that contain a browser-ready exported site. CMS/source-only
// packages fall back to a clean, full-size visual preview below.
$livePreviewRoots = [
    't1' => 'HTML Preview/',
    't2' => 'Bitco - Bitcoin Crypto Muse theme/html/',
    't4' => 'Preview/',
    't5' => 'HTML Demo/',
    't8' => 'Coxe - Corporate Multipurpose/html/',
    't10' => 'Construct_Package/Html/',
    't12' => 'Exported HTML/CA-Multipage/',
    't14' => 'main_bonesa_v.1.0/html_bones_v.1.0/',
    't16' => 'main_century_onepage_v.1.0/html_century_onepage_v.1.0/',
    't19' => 'BRIX - Landing App Onepage/html/v1/',
    't21' => 'ezyadmin-bootstrap/',
    't22' => 'omnimart-complete/',
];

function firstFile(string $dir, array $extensions): ?string
{
    $files = array_values(array_filter(scandir($dir) ?: [], function (string $name) use ($dir, $extensions): bool {
        return is_file($dir . '/' . $name) && in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), $extensions, true);
    }));
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    return $files ? $dir . '/' . $files[0] : null;
}

function firstZip(string $dir): ?string
{
    $files = array_values(array_filter(scandir($dir) ?: [], fn(string $name): bool =>
        is_file($dir . '/' . $name) && str_contains(strtolower($name), '.zip')
    ));
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    return $files ? $dir . '/' . $files[0] : null;
}

function imageBytes(string $folder, string $key, array $fallbackZipImages, string $zip): string
{
    $image = firstFile($folder, ['png', 'jpg', 'jpeg', 'webp']);
    if ($image !== null) {
        $bytes = file_get_contents($image);
        if ($bytes !== false) return $bytes;
    }
    if (!isset($fallbackZipImages[$key])) throw new RuntimeException("No image found for {$key}");
    $archive = new ZipArchive();
    if ($archive->open($zip) !== true) throw new RuntimeException("Cannot open {$zip}");
    $bytes = $archive->getFromName($fallbackZipImages[$key]);
    $archive->close();
    if ($bytes === false) throw new RuntimeException("Fallback image missing for {$key}");
    return $bytes;
}

function saveJpeg(string $bytes, string $destination, int $width, int $height, bool $cover): void
{
    $source = imagecreatefromstring($bytes);
    if ($source === false) throw new RuntimeException('Unsupported source image');
    $sw = imagesx($source); $sh = imagesy($source);
    $canvas = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    $scale = $cover ? max($width / $sw, $height / $sh) : min($width / $sw, $height / $sh);
    $dw = (int) round($sw * $scale); $dh = (int) round($sh * $scale);
    $dx = (int) floor(($width - $dw) / 2); $dy = (int) floor(($height - $dh) / 2);
    imagecopyresampled($canvas, $source, $dx, $dy, 0, 0, $dw, $dh, $sw, $sh);
    if (!imagejpeg($canvas, $destination, 88)) throw new RuntimeException("Cannot write {$destination}");
    imagedestroy($source); imagedestroy($canvas);
}

function extractPreviewRoot(string $zipPath, string $prefix, string $destination): bool
{
    $archive = new ZipArchive();
    if ($archive->open($zipPath) !== true) return false;
    $foundIndex = false;
    for ($i = 0; $i < $archive->numFiles; $i++) {
        $entry = $archive->getNameIndex($i);
        if ($entry === false || !str_starts_with($entry, $prefix)) continue;
        $relative = substr($entry, strlen($prefix));
        if ($relative === '' || str_ends_with($relative, '/')) continue;
        $parts = array_values(array_filter(explode('/', str_replace('\\', '/', $relative)), fn($part) => $part !== '' && $part !== '.' && $part !== '..'));
        if (!$parts) continue;
        $target = $destination . '/' . implode('/', $parts);
        $targetDir = dirname($target);
        if (!is_dir($targetDir)) mkdir($targetDir, 0775, true);
        $stream = $archive->getStream($entry);
        if ($stream === false) continue;
        $output = fopen($target, 'wb');
        if ($output !== false) { stream_copy_to_stream($stream, $output); fclose($output); }
        fclose($stream);
        if (strtolower(implode('/', $parts)) === 'index.html') $foundIndex = true;
    }
    $archive->close();
    return $foundIndex;
}

function createVisualPreview(string $title, string $previewImage, string $destination): void
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $html = <<<HTML
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$safeTitle} Preview</title><style>*{box-sizing:border-box}html,body{margin:0;min-height:100%;background:#eef2f7;font-family:Arial,sans-serif}.bar{position:sticky;top:0;z-index:2;padding:12px 20px;background:#111827;color:#fff;font-weight:700}.stage{min-height:calc(100vh - 45px);display:grid;place-items:center;padding:24px}.stage img{display:block;max-width:100%;max-height:calc(100vh - 93px);width:auto;height:auto;box-shadow:0 18px 60px #0f172a30;border-radius:10px}</style></head>
<body><div class="bar">{$safeTitle}</div><main class="stage"><img src="{$previewImage}" alt="{$safeTitle} website preview"></main></body></html>
HTML;
    file_put_contents($destination . '/index.html', $html);
}

$main = MainCategory::where('slug', 'website-templates')->firstOrFail();
$storageRoot = storage_path('app/public/website-templates');
$demoRoot = storage_path('app/public/website-template-demos');
if (!is_dir($storageRoot) && !mkdir($storageRoot, 0775, true) && !is_dir($storageRoot)) {
    throw new RuntimeException("Cannot create {$storageRoot}");
}
if (!is_dir($demoRoot) && !mkdir($demoRoot, 0775, true) && !is_dir($demoRoot)) throw new RuntimeException("Cannot create {$demoRoot}");

$created = 0; $updated = 0;
foreach ($projects as $key => [$title, $titleAr, $categorySlug, $subcategorySlug, $stack]) {
    $folder = $sourceRoot . '/' . $key;
    if (!is_dir($folder)) throw new RuntimeException("Project folder missing: {$folder}");
    $zip = firstZip($folder);
    if ($zip === null) throw new RuntimeException("ZIP missing for {$key}");

    $category = Category::where('main_category_id', $main->id)->where('slug', $categorySlug)->firstOrFail();
    $subcategory = Subcategory::where('category_id', $category->id)->where('slug', $subcategorySlug)->firstOrFail();
    $slug = strtolower($key . '-' . preg_replace('/[^a-z0-9]+/i', '-', $title));
    $slug = trim($slug, '-');
    $assetDir = $storageRoot . '/' . $slug;
    if (!is_dir($assetDir) && !mkdir($assetDir, 0775, true) && !is_dir($assetDir)) throw new RuntimeException("Cannot create {$assetDir}");

    $bytes = imageBytes($folder, $key, $fallbackZipImages, $zip);
    saveJpeg($bytes, $assetDir . '/preview-1200x900.jpg', 1200, 900, true);
    saveJpeg($bytes, $assetDir . '/detail-1600x1200.jpg', 1600, 1200, false);
    copy($zip, $assetDir . '/download.zip');

    $demoDir = $demoRoot . '/' . $slug;
    if (!is_dir($demoDir) && !mkdir($demoDir, 0775, true) && !is_dir($demoDir)) throw new RuntimeException("Cannot create {$demoDir}");
    $hasLivePreview = isset($livePreviewRoots[$key]) && extractPreviewRoot($zip, $livePreviewRoots[$key], $demoDir);
    if (!$hasLivePreview) {
        createVisualPreview($title, "../../website-templates/{$slug}/detail-1600x1200.jpg", $demoDir);
    }

    DB::transaction(function () use (&$created, &$updated, $key, $title, $titleAr, $category, $subcategory, $main, $slug, $stack, $zip): void {
        $resource = Resource::where('slug', $slug)->first();
        $resource ? $updated++ : $created++;
        $description = "A responsive {$title} website template supplied as a ready-to-customize ZIP package.";
        $descriptionAr = "{$titleAr} متجاوب، متوفر كحزمة ZIP جاهزة للتخصيص والاستخدام.";
        $resource = Resource::updateOrCreate(['slug' => $slug], [
            'category_id' => $category->id, 'main_category_id' => $main->id, 'subcategory_id' => $subcategory->id,
            'title' => $title, 'title_ar' => $titleAr, 'resource_type' => 'website',
            'tech_stack' => $stack, 'tech_stack_ar' => $stack,
            'short_description' => $description, 'short_description_ar' => $descriptionAr,
            'description' => $description, 'description_ar' => $descriptionAr,
            'features' => ['Responsive layout', 'Editable source files', 'Ready-to-use ZIP package'],
            'features_ar' => ['تصميم متجاوب', 'ملفات مصدر قابلة للتعديل', 'حزمة ZIP جاهزة للاستخدام'],
            'preview_image' => "/storage/website-templates/{$slug}/preview-1200x900.jpg",
            'detail_image' => "/storage/website-templates/{$slug}/detail-1600x1200.jpg",
            'screenshots' => ["/storage/website-templates/{$slug}/detail-1600x1200.jpg"],
            'featured' => false, 'is_free' => true, 'price' => 0, 'version' => '1.0.0',
            'license' => 'Free', 'status' => 'published', 'published_at' => now(),
            'demo_url' => "/storage/website-template-demos/{$slug}/index.html",
            'rating' => 5.0, 'reviews_count' => 0, 'downloads_count' => 0,
            'seo_title' => "{$title} Website Template", 'seo_title_ar' => $titleAr,
            'seo_description' => $description, 'seo_description_ar' => $descriptionAr,
            'image_alt' => "{$title} website template preview", 'image_alt_ar' => "معاينة {$titleAr}",
            'media_width' => 1200, 'media_height' => 900, 'gallery_status' => 'ready', 'media_sync_status' => 'ready',
        ]);
        $downloadPath = "website-templates/{$slug}/download.zip";
        ResourceFile::updateOrCreate(['resource_id' => $resource->id, 'is_primary' => true], [
            'label' => "Download {$title}", 'path' => $downloadPath, 'original_name' => basename($zip),
            'extension' => 'zip', 'mime_type' => 'application/zip', 'size_bytes' => filesize(storage_path('app/public/' . $downloadPath)),
            'quality' => 'Original', 'format' => 'ZIP', 'sort_order' => 0,
        ]);
    });
    echo "Imported {$key}: {$title}\n";
}

echo "Done. Created {$created}, updated {$updated}, total " . count($projects) . ".\n";
