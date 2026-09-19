<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'site_name' => Setting::get('site_name', 'EzyTemplate'),
            'site_name_ar' => Setting::get('site_name_ar', 'إيزي تمبليت'),
            'site_tagline' => Setting::get('site_tagline', 'Beautiful Templates for Every Project'),
            'site_tagline_ar' => Setting::get('site_tagline_ar', 'أجمل وأحدث القوالب لكافة المشاريع الرقمية'),
            'site_description' => Setting::get('site_description', ''),
            'site_description_ar' => Setting::get('site_description_ar', ''),
            'contact_email' => Setting::get('contact_email', 'support@ezytemplate.com'),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'contact_address_ar' => Setting::get('contact_address_ar', ''),
            'copyright_text' => Setting::get('copyright_text', '© 2026 EzyTemplate by Ezystore. All rights reserved.'),
            'copyright_text_ar' => Setting::get('copyright_text_ar', '© 2026 إيزي تمبليت بواسطة إيزي ستور. كافة الحقوق محفوظة.'),
            'footer_subtext' => Setting::get('footer_subtext', 'Build • Create • Share • Grow'),
            'footer_subtext_ar' => Setting::get('footer_subtext_ar', 'ابنِ • ابتكر • شارك • انطلق'),
            'social_links' => Setting::get('social_links', []),
            'header_nav_links' => Setting::get('header_nav_links', []),
            'footer_columns' => Setting::get('footer_columns', []),
            'items_per_page' => Setting::get('items_per_page', '24'),
            'analytics_retention_days' => Setting::get('analytics_retention_days', '365'),
        ]);
    }

    public function update(Request $request)
    {
        // 1. General Branding & Contact
        if ($request->has('site_name')) Setting::set('site_name', $request->input('site_name'));
        if ($request->has('site_name_ar')) Setting::set('site_name_ar', $request->input('site_name_ar'));
        if ($request->has('site_tagline')) Setting::set('site_tagline', $request->input('site_tagline'));
        if ($request->has('site_tagline_ar')) Setting::set('site_tagline_ar', $request->input('site_tagline_ar'));
        if ($request->has('site_description')) Setting::set('site_description', $request->input('site_description'));
        if ($request->has('site_description_ar')) Setting::set('site_description_ar', $request->input('site_description_ar'));
        if ($request->has('contact_email')) Setting::set('contact_email', $request->input('contact_email'));
        if ($request->has('contact_phone')) Setting::set('contact_phone', $request->input('contact_phone'));
        if ($request->has('contact_address')) Setting::set('contact_address', $request->input('contact_address'));
        if ($request->has('contact_address_ar')) Setting::set('contact_address_ar', $request->input('contact_address_ar'));
        if ($request->has('copyright_text')) Setting::set('copyright_text', $request->input('copyright_text'));
        if ($request->has('copyright_text_ar')) Setting::set('copyright_text_ar', $request->input('copyright_text_ar'));
        if ($request->has('footer_subtext')) Setting::set('footer_subtext', $request->input('footer_subtext'));
        if ($request->has('footer_subtext_ar')) Setting::set('footer_subtext_ar', $request->input('footer_subtext_ar'));
        if ($request->has('items_per_page')) Setting::set('items_per_page', $request->input('items_per_page'));
        if ($request->has('analytics_retention_days')) Setting::set('analytics_retention_days', $request->input('analytics_retention_days'));

        // Handle uploaded logo or favicon if any
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('branding', 'public');
            Setting::set('site_logo', '/storage/' . $path);
        }
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('branding', 'public');
            Setting::set('site_favicon', '/storage/' . $path);
        }

        // 2. Social Links
        if ($request->has('social_links')) {
            Setting::set('social_links', array_values($request->input('social_links', [])));
        }

        // 3. Header Navigation
        if ($request->has('header_nav_links')) {
            Setting::set('header_nav_links', array_values($request->input('header_nav_links', [])));
        }

        // 4. Footer Columns
        if ($request->has('footer_columns')) {
            $cols = $request->input('footer_columns', []);
            foreach ($cols as $i => $col) {
                if (isset($col['links_raw'])) {
                    $links = [];
                    foreach (explode("\n", $col['links_raw']) as $line) {
                        $parts = explode('|', trim($line));
                        if (count($parts) >= 2) {
                            $links[] = ['label' => trim($parts[0]), 'url' => trim($parts[1])];
                        }
                    }
                    $cols[$i]['links'] = $links;
                    unset($cols[$i]['links_raw']);
                }
            }
            Setting::set('footer_columns', array_values($cols));
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
