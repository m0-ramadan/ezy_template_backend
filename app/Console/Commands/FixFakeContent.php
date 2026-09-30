<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Resource;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Removes fabricated content that earlier seeders created in production
 * databases: invented statistics, placeholder social links, navigation links
 * to non-existent routes, and sample blog posts attributed to fake authors.
 *
 * Runs as a dry-run unless --apply is passed, and never touches anything that
 * is backed by real data.
 */
class FixFakeContent extends Command
{
    protected $signature = 'ezytemplate:fix-fake-content
        {--apply : Write the corrected settings (default is a dry run)}
        {--remove-sample-articles : Also unpublish the known sample blog posts}
        {--reset-ratings : Zero the resource ratings that have no backing review}';

    protected $description = 'Report and remove fabricated stats/links/sample content from the CMS settings';

    /** Slugs of the demo posts created by the old CmsInitialSeeder. */
    private const SAMPLE_ARTICLE_SLUGS = [
        '10-modern-web-design-trends-for-2026',
        'complete-guide-to-next-js-for-beginners',
        'build-a-complete-laravel-ecommerce-project',
        'ui-ux-best-practices-for-higher-conversions',
        'how-to-create-a-high-converting-product-page',
        'top-10-free-wordpress-themes-for-2026',
    ];

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $this->info($apply ? 'Applying corrections...' : 'DRY RUN - no changes will be written.');

        // ---- Settings -------------------------------------------------------
        $this->line('');
        $this->info('Settings');

        $hero = Setting::get('home_hero', []);
        if (is_array($hero) && isset($hero['stat_badge_1_val']) && $hero['stat_badge_1_val'] === '1000+') {
            $hero['stat_badge_1_val'] = 'Curated';
            if (($hero['stat_badge_1_label'] ?? null) === 'Free Templates') {
                $hero['stat_badge_1_label'] = 'Free & Premium';
            }
            $this->warn('  home_hero stat badge "1000+" -> "Curated"');
            if ($apply) {
                Setting::set('home_hero', $hero);
            }
        } else {
            $this->line('  home_hero: nothing to change');
        }

        $benefits = Setting::get('home_benefits', []);
        if (is_array($benefits)) {
            $changed = false;
            foreach ($benefits as &$benefit) {
                if (($benefit['title'] ?? null) === 'Trusted by Developers') {
                    $benefit['title'] = 'Multi-Format Library';
                    $benefit['description'] = 'Website, Excel, Word, presentation, and design files in one place.';
                    $changed = true;
                }
            }
            unset($benefit);
            if ($changed) {
                $this->warn('  home_benefits: replaced "Trusted by Developers" claim');
                if ($apply) {
                    Setting::set('home_benefits', $benefits);
                }
            }
        }

        $socials = Setting::get('social_links', []);
        if (is_array($socials) && $this->hasPlaceholderSocials($socials)) {
            $this->warn('  social_links: ' . count($socials) . ' placeholder links -> []');
            if ($apply) {
                Setting::set('social_links', []);
            }
        }

        foreach (['header_nav_links', 'footer_columns'] as $key) {
            $value = Setting::get($key, []);
            if (is_string($value)) {
                $value = json_decode($value, true) ?: [];
            }
            if (is_array($value) && $this->containsCategoriesRoute($value)) {
                $this->warn("  {$key}: contains /categories (404) - run the updated CmsInitialSeeder or edit in admin");
            }
        }

        $aboutStats = Setting::get('about_stats', []);
        if (is_array($aboutStats) && $aboutStats !== []) {
            $this->warn('  about_stats: ' . count($aboutStats) . ' hard-coded placeholder stats -> [] (About page uses live DB counts)');
            if ($apply) {
                Setting::set('about_stats', []);
            }
        }

        // ---- Ratings & downloads -------------------------------------------
        $this->line('');
        $this->info('Ratings & downloads');

        $approvedReviews = (int) Review::where('status', 'approved')->count();
        $ratedResources = (int) Resource::where('rating', '>', 0)->count();
        $this->line("  approved reviews: {$approvedReviews}");
        $this->line("  resources with rating > 0: {$ratedResources}");

        if ($approvedReviews === 0 && $ratedResources > 0) {
            $this->warn('  These ratings have no backing reviews and are fabricated social proof.');
            if ($this->option('reset-ratings')) {
                if ($apply) {
                    Resource::where('rating', '>', 0)->update(['rating' => 0]);
                    $this->info('  reset those ratings to 0');
                } else {
                    $this->line('  would reset those ratings to 0 (--apply)');
                }
            } else {
                $this->line('  pass --reset-ratings to zero them out');
            }
        }

        $seededDownloads = (int) Resource::sum('downloads_count');
        $realDownloads = (int) DB::table('downloads')->count();
        $this->line("  summed resources.downloads_count: {$seededDownloads}");
        $this->line("  real rows in downloads table: {$realDownloads}");
        if ($seededDownloads > max($realDownloads * 5, 100)) {
            $this->warn('  downloads_count looks inflated relative to real download events. Recompute it from the downloads table before displaying it.');
        }

        // ---- Sample articles ------------------------------------------------
        $this->line('');
        $this->info('Sample articles');
        $sampleArticles = Article::whereIn('slug', self::SAMPLE_ARTICLE_SLUGS)->get();
        if ($sampleArticles->isEmpty()) {
            $this->line('  none found');
        } else {
            foreach ($sampleArticles as $article) {
                $this->warn("  [{$article->id}] {$article->slug} - author: {$article->author_name}");
            }
            if ($this->option('remove-sample-articles')) {
                if ($apply) {
                    Article::whereIn('slug', self::SAMPLE_ARTICLE_SLUGS)->update([
                        'status' => 'draft',
                        'published_at' => null,
                    ]);
                    $this->info('  unpublished the sample articles');
                } else {
                    $this->line('  would unpublish the sample articles (--apply)');
                }
            } else {
                $this->line('  pass --remove-sample-articles to unpublish them');
            }
        }

        $this->line('');
        $this->info($apply ? 'Done.' : 'Dry run complete. Re-run with --apply to write changes.');

        return self::SUCCESS;
    }

    private function hasPlaceholderSocials(array $socials): bool
    {
        foreach ($socials as $social) {
            $url = is_array($social) ? ($social['url'] ?? '') : '';
            if (preg_match('#^https?://(www\.)?(twitter|x|github|linkedin|youtube|instagram|facebook)\.com/?$#i', $url)) {
                return true;
            }
        }

        return false;
    }

    private function containsCategoriesRoute(array $value): bool
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE) ?: '';
        return str_contains($json, '"/categories"') || str_contains($json, '/categories');
    }
}
