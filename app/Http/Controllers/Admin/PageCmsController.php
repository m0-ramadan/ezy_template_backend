<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageCmsController extends Controller
{
    // === HOME PAGE CMS ===
    public function home()
    {
        return view('admin.cms.home', [
            'hero' => Setting::get('home_hero', []),
            'popular_tags' => Setting::get('home_popular_tags', []),
            'featured' => Setting::get('home_featured_section', []),
            'benefits' => Setting::get('home_benefits', []),
            'cta' => Setting::get('home_cta', []),
        ]);
    }

    public function updateHome(Request $request)
    {
        // 1. Hero
        $hero = Setting::get('home_hero', []);
        $heroData = $request->input('hero', []);
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('cms', 'public');
            $heroData['hero_image'] = '/storage/' . $path;
        } elseif (!empty($heroData['hero_image'])) {
            // Keep existing or text input
        } else {
            $heroData['hero_image'] = $hero['hero_image'] ?? '/assets/hero-main.png';
        }
        Setting::set('home_hero', array_merge($hero, $heroData));

        // 2. Popular tags
        if ($request->filled('popular_tags_raw')) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $request->input('popular_tags_raw')))));
            Setting::set('home_popular_tags', $tags);
        }

        // 3. Featured section
        if ($request->has('featured')) {
            Setting::set('home_featured_section', $request->input('featured'));
        }

        // 4. Benefits
        if ($request->has('benefits')) {
            $benefits = array_values($request->input('benefits', []));
            Setting::set('home_benefits', $benefits);
        }

        // 5. CTA
        if ($request->has('cta')) {
            Setting::set('home_cta', $request->input('cta'));
        }

        return back()->with('success', 'Homepage content updated successfully.');
    }

    // === ABOUT PAGE CMS ===
    public function about()
    {
        return view('admin.cms.about', [
            'hero' => Setting::get('about_hero', []),
            'stats' => Setting::get('about_stats', []),
            'story' => Setting::get('about_story', []),
            'values' => Setting::get('about_values', []),
            'team' => Setting::get('about_team', []),
            'cta' => Setting::get('about_cta', []),
        ]);
    }

    public function updateAbout(Request $request)
    {
        // 1. Hero
        $hero = Setting::get('about_hero', []);
        $heroData = $request->input('hero', []);
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('cms', 'public');
            $heroData['hero_image'] = '/storage/' . $path;
        }
        Setting::set('about_hero', array_merge($hero, $heroData));

        // 2. Stats
        if ($request->has('stats')) {
            Setting::set('about_stats', array_values($request->input('stats')));
        }

        // 3. Story
        $story = Setting::get('about_story', []);
        $storyData = $request->input('story', []);
        if ($request->hasFile('story_image')) {
            $path = $request->file('story_image')->store('cms', 'public');
            $storyData['image'] = '/storage/' . $path;
        }
        Setting::set('about_story', array_merge($story, $storyData));

        // 4. Values
        if ($request->has('values')) {
            Setting::set('about_values', array_values($request->input('values')));
        }

        // 5. Team
        if ($request->has('team')) {
            $team = $request->input('team', []);
            // Handle uploaded team member photos if any
            if ($request->hasFile('team_photos')) {
                foreach ($request->file('team_photos') as $idx => $photo) {
                    if ($photo && isset($team[$idx])) {
                        $path = $photo->store('cms/team', 'public');
                        $team[$idx]['image'] = '/storage/' . $path;
                    }
                }
            }
            Setting::set('about_team', array_values($team));
        }

        // 6. CTA
        if ($request->has('cta')) {
            Setting::set('about_cta', $request->input('cta'));
        }

        return back()->with('success', 'About Us page content updated successfully.');
    }

    // === SERVICES PAGE CMS ===
    public function services()
    {
        return view('admin.cms.services', [
            'hero' => Setting::get('services_hero', []),
            'trust_badges' => Setting::get('services_trust_badges', []),
            'services' => Setting::get('services_list', []),
            'why_us' => Setting::get('services_why_us', []),
            'process' => Setting::get('services_process', []),
            'faqs' => Setting::get('services_faqs', []),
            'help_box' => Setting::get('services_help_box', []),
        ]);
    }

    public function updateServices(Request $request)
    {
        // 1. Hero
        $hero = Setting::get('services_hero', []);
        $heroData = $request->input('hero', []);
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('cms', 'public');
            $heroData['hero_image'] = '/storage/' . $path;
        }
        Setting::set('services_hero', array_merge($hero, $heroData));

        // 2. Trust badges
        if ($request->has('trust_badges')) {
            Setting::set('services_trust_badges', array_values($request->input('trust_badges')));
        }

        // 3. Services list
        if ($request->has('services')) {
            Setting::set('services_list', array_values($request->input('services')));
        }

        // 4. Why us
        if ($request->has('why_us')) {
            $whyUs = $request->input('why_us');
            if (!empty($whyUs['bullets_raw'])) {
                $whyUs['bullets'] = array_values(array_filter(array_map('trim', explode("\n", $whyUs['bullets_raw']))));
            }
            if ($request->hasFile('why_us_image')) {
                $path = $request->file('why_us_image')->store('cms', 'public');
                $whyUs['image'] = '/storage/' . $path;
            }
            Setting::set('services_why_us', $whyUs);
        }

        // 5. Process
        if ($request->has('process')) {
            Setting::set('services_process', array_values($request->input('process')));
        }

        // 6. FAQs
        if ($request->has('faqs')) {
            Setting::set('services_faqs', array_values($request->input('faqs')));
        }

        // 7. Help Box
        if ($request->has('help_box')) {
            Setting::set('services_help_box', $request->input('help_box'));
        }

        return back()->with('success', 'Services page content updated successfully.');
    }

    // === CUSTOM & SYSTEM PAGES CMS ===
    public function customPages()
    {
        return view('admin.cms.custom_pages', [
            'faq' => Setting::get('page_faq', []),
            'custom_requests' => Setting::get('page_custom_requests', []),
            'support' => Setting::get('page_support', []),
            'contact' => Setting::get('page_contact', []),
            'privacy' => Setting::get('page_privacy', []),
            'terms' => Setting::get('page_terms', []),
        ]);
    }

    public function updateCustomPages(Request $request)
    {
        if ($request->has('page_faq')) {
            $faq = $request->input('page_faq');
            if (isset($faq['items'])) {
                $faq['items'] = array_values($faq['items']);
            }
            Setting::set('page_faq', $faq);
        }

        if ($request->has('page_custom_requests')) {
            $custom = $request->input('page_custom_requests');
            if (!empty($custom['features_raw'])) {
                $custom['features'] = array_values(array_filter(array_map('trim', explode("\n", $custom['features_raw']))));
            }
            Setting::set('page_custom_requests', $custom);
        }

        if ($request->has('page_support')) {
            $support = $request->input('page_support');
            if (isset($support['channels'])) {
                $support['channels'] = array_values($support['channels']);
            }
            Setting::set('page_support', $support);
        }

        if ($request->has('page_contact')) {
            Setting::set('page_contact', $request->input('page_contact'));
        }

        if ($request->has('page_privacy')) {
            $privacy = $request->input('page_privacy');
            if (isset($privacy['sections'])) {
                $privacy['sections'] = array_values($privacy['sections']);
            }
            Setting::set('page_privacy', $privacy);
        }

        if ($request->has('page_terms')) {
            $terms = $request->input('page_terms');
            if (isset($terms['sections'])) {
                $terms['sections'] = array_values($terms['sections']);
            }
            Setting::set('page_terms', $terms);
        }

        return back()->with('success', 'System pages content updated successfully.');
    }
}
