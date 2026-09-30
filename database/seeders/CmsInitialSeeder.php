<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Setting, Category, Resource, ResourceFile, Article};
use Illuminate\Support\Str;

class CmsInitialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. General Site Identity & Social Links
        Setting::set('site_name', 'EzyTemplate');
        Setting::set('site_tagline', 'Beautiful Templates for Every Project');
        Setting::set('site_description', 'Browse free and premium website templates, UI kits, Excel sheets, and design resources.');
        Setting::set('contact_email', 'support@ezytemplate.com');
        Setting::set('contact_phone', '');
        Setting::set('contact_address', 'Cairo, Egypt');
        Setting::set('copyright_text', '© 2026 EzyTemplate by Ezystore. All rights reserved.');
        Setting::set('footer_subtext', 'Build • Create • Share • Grow');

        // No real social profiles are configured yet. An empty list is honest;
        // placeholder links to twitter.com/github.com home pages are not.
        Setting::set('social_links', []);

        // 2. Navigation Menus (only routes that exist)
        Setting::set('header_nav_links', [
            ['label' => 'Home', 'label_ar' => 'الرئيسية', 'url' => '/', 'is_active' => true, 'sort_order' => 1],
            ['label' => 'Templates', 'label_ar' => 'القوالب', 'url' => '/templates', 'is_active' => true, 'sort_order' => 2],
            ['label' => 'Blog', 'label_ar' => 'المدونة', 'url' => '/blog', 'is_active' => true, 'sort_order' => 3],
            ['label' => 'Services', 'label_ar' => 'خدماتنا', 'url' => '/services', 'is_active' => true, 'sort_order' => 4],
            ['label' => 'About', 'label_ar' => 'من نحن', 'url' => '/about', 'is_active' => true, 'sort_order' => 5],
        ]);

        Setting::set('footer_columns', [
            [
                'title' => 'Quick Links',
                'title_ar' => 'روابط سريعة',
                'links' => [
                    ['label' => 'Home', 'label_ar' => 'الرئيسية', 'url' => '/'],
                    ['label' => 'Templates', 'label_ar' => 'القوالب والتصاميم', 'url' => '/templates'],
                    ['label' => 'Resources', 'label_ar' => 'الموارد', 'url' => '/resources'],
                    ['label' => 'Blog', 'label_ar' => 'المدونة والمقالات', 'url' => '/blog'],
                    ['label' => 'Services', 'label_ar' => 'الخدمات والحلول', 'url' => '/services'],
                    ['label' => 'About Us', 'label_ar' => 'من نحن', 'url' => '/about'],
                ]
            ],
            [
                'title' => 'Resources',
                'title_ar' => 'الموارد والقوالب',
                'links' => [
                    ['label' => 'Excel Templates', 'label_ar' => 'قوالب إكسيل', 'url' => '/excel-templates'],
                    ['label' => 'Word Templates', 'label_ar' => 'قوالب وورد', 'url' => '/word-templates'],
                    ['label' => 'Design Resources', 'label_ar' => 'تصاميم وجرافيك', 'url' => '/design-templates'],
                    ['label' => 'Website Templates', 'label_ar' => 'قوالب مواقع', 'url' => '/website-templates'],
                ]
            ],
            [
                'title' => 'Help & Support',
                'title_ar' => 'المساعدة والدعم',
                'links' => [
                    ['label' => 'FAQ', 'label_ar' => 'الأسئلة الشائعة', 'url' => '/faq'],
                    ['label' => 'Contact Us', 'label_ar' => 'تواصل معنا', 'url' => '/contact'],
                    ['label' => 'Privacy Policy', 'label_ar' => 'سياسة الخصوصية', 'url' => '/privacy'],
                    ['label' => 'Terms of Service', 'label_ar' => 'شروط الاستخدام', 'url' => '/terms'],
                ]
            ]
        ]);

        // 3. Homepage Content (CMS Home)
        Setting::set('home_hero', [
            'eyebrow' => 'Free & Premium Website Templates',
            'title' => 'Beautiful Templates',
            'highlight_text' => 'for Every Project',
            'lead' => 'Browse free and premium website templates, UI kits, and design resources. Download, customize, and build your next project faster.',
            'hero_image' => '/assets/hero-main.png',
            'note_title' => 'Your Next Website',
            'note_subtitle' => 'Starts Here',
            'stat_badge_1_val' => 'Curated',
            'stat_badge_1_label' => 'Free & Premium',
            'stat_badge_2_val' => 'Modern',
            'stat_badge_2_label' => '& Responsive',
            'stat_badge_3_val' => '⚡ Easy to',
            'stat_badge_3_label' => 'Customize',
        ]);

        Setting::set('home_popular_tags', [
            'Laravel',
            'Next.js',
            'Admin Dashboard',
            'eCommerce',
            'Portfolio',
            'Blog',
            'React',
            'Tailwind'
        ]);

        Setting::set('home_featured_section', [
            'eyebrow' => 'Featured Templates',
            'title' => 'Trending Templates',
            'description' => 'Hand-picked templates to help you build faster.',
            'button_text' => 'View All Templates →',
            'button_url' => '/templates',
        ]);

        Setting::set('home_benefits', [
            ['icon' => 'Zap', 'title' => '100% Free Options', 'description' => 'High-quality templates completely free to download and kickstart your work.'],
            ['icon' => 'MonitorCog', 'title' => 'Responsive Design', 'description' => 'Works perfectly on all modern devices, tablets, and desktop screen sizes.'],
            ['icon' => 'Settings2', 'title' => 'Easy to Customize', 'description' => 'Clean, structured, and well-documented code designed for rapid editing.'],
            ['icon' => 'Users', 'title' => 'Multi-Format Library', 'description' => 'Website, Excel, Word, presentation, and design files in one place.'],
        ]);

        Setting::set('home_cta', [
            'title' => 'Ready to Build Something Amazing?',
            'description' => 'Join our community and get access to the latest templates, updates, and resources.',
            'button_text' => 'Get Started for Free →',
            'button_url' => '/templates',
        ]);

        // 4. About Us Page Content (CMS About)
        Setting::set('about_hero', [
            'eyebrow' => 'About Us',
            'title' => 'We Empower',
            'highlight_text' => 'Creators & Builders',
            'lead' => 'At EzyTemplate, we believe everyone can build something amazing. We provide high-quality, free and premium website templates, UI kits, and resources to help developers, designers, and businesses bring their ideas to life faster.',
            'hero_image' => '/assets/about-hero.png',
            'btn_primary_text' => 'Explore Templates →',
            'btn_primary_url' => '/templates',
            'btn_secondary_text' => 'Join Our Community',
            'btn_secondary_url' => '/signup',
        ]);

        // The About page computes real, database-backed counters in
        // CmsController::about(). No hard-coded placeholder statistics belong
        // here: inventing downloads, users, ratings or countries is fabricated
        // social proof and a policy risk.
        Setting::set('about_stats', []);

        Setting::set('about_story', [
            'eyebrow' => 'Our Story',
            'title' => 'A Passion for Better Web Experiences',
            'image' => '/assets/about-story.png',
            'paragraph_1' => 'EzyTemplate started as a small idea between a group of developers and designers who wanted to make high-quality templates accessible to everyone. We noticed that many amazing resources were either too expensive or hard to find, so we decided to create a platform that brings everything together in one place.',
            'paragraph_2' => 'Today, EzyTemplate is a growing community of creators, learners, and businesses who trust us for modern, clean, and easy-to-use templates across various technologies and formats.'
        ]);

        Setting::set('about_values', [
            ['icon' => 'Target', 'title' => 'Our Mission', 'description' => 'To simplify website development and design for everyone everywhere.'],
            ['icon' => 'Eye', 'title' => 'Our Vision', 'description' => 'A world where high-quality web design and code is accessible to all creators.'],
            ['icon' => 'Heart', 'title' => 'Our Values', 'description' => 'Quality, community support, creativity, transparency, and continuous improvement.'],
        ]);

        Setting::set('about_team', [
            [
                'name' => 'Mohamed Ramadan',
                'name_ar' => 'محمد رمضان',
                'role' => 'Software Engineer',
                'role_ar' => 'مهندس برمجيات',
                'bio' => 'Software Engineer specialized in full-stack web development, building high-performance scalable systems and modern digital platforms.',
                'bio_ar' => 'مهندس برمجيات متخصص في تطوير الويب الشامل والحلول البرمجية عالية الأداء وبناء المنصات الرقمية الحديثة.',
                'image' => '/assets/mohamed-ramadan.png',
                'linkedin' => 'http://linkedin.com/in/mohamed-ramadan-zaki/',
                'facebook' => 'https://www.facebook.com/profile.php?id=61589459066799'
            ],
        ]);

        Setting::set('about_cta', [
            'title' => 'Join Our Growing Community',
            'description' => 'Be part of thousands of developers and designers. Get updates, free resources, and inspiration directly in your inbox.',
            'button_text' => 'Get Started →',
            'button_url' => '/signup',
        ]);

        // 5. Services Page Content (CMS Services)
        Setting::set('services_hero', [
            'eyebrow' => 'Our Services',
            'title' => 'More Than Templates',
            'highlight_text' => 'We Help You Build',
            'lead' => 'From customization to full website development, EzyTemplate offers professional services to help you launch, customize, and grow your online projects.',
            'hero_image' => '/assets/services-hero.png',
            'btn_primary_text' => 'Get Started →',
            'btn_primary_url' => '#request-service',
            'btn_secondary_text' => 'Explore FAQs',
            'btn_secondary_url' => '#faq',
        ]);

        Setting::set('services_trust_badges', [
            ['icon' => 'ShieldCheck', 'title' => 'Trusted & Reliable', 'subtitle' => 'Quality work delivered on time'],
            ['icon' => 'Zap', 'title' => 'Fast Delivery', 'subtitle' => 'Get your project done quickly'],
            ['icon' => 'Award', 'title' => 'Satisfaction Guarantee', 'subtitle' => "We're not happy until you are"],
        ]);

        Setting::set('services_list', [
            ['icon' => 'Settings', 'title' => 'Template Customization', 'description' => 'Need changes to a template? We customize colors, structure, and features to match your brand and exact requirements.'],
            ['icon' => 'Code2', 'title' => 'Full Website Development', 'description' => 'Get a complete bespoke website built using our templates or from scratch with full backend and database integration.'],
            ['icon' => 'Smartphone', 'title' => 'Website Conversion', 'description' => 'Convert Figma or static designs to production-ready Laravel, Next.js, React or HTML/Tailwind.'],
            ['icon' => 'Paintbrush', 'title' => 'UI/UX Design', 'description' => 'Get modern, conversion-focused design systems for your website, SaaS dashboard, or mobile web application.'],
            ['icon' => 'LifeBuoy', 'title' => 'Installation & Setup', 'description' => "We will deploy, install and configure your template or full-stack application on your server or cloud host."],
            ['icon' => 'Users', 'title' => 'Ongoing Maintenance & Support', 'description' => 'Continuous updates, bug fixing, performance enhancements, and technical support whenever you need it.'],
        ]);

        Setting::set('services_why_us', [
            'eyebrow' => 'Why Choose Us',
            'title' => 'Your Success is Our Priority',
            'lead' => 'We combine technical expertise, creative design, and reliable support to deliver the best solutions for your web projects.',
            'image' => '/assets/services-why.png',
            'bullets' => [
                'Experienced Senior Developers & Designers',
                'Pixel-Perfect and High-Quality Code',
                'Strict On-Time Delivery Deadlines',
                'Clear and Transparent Communication',
                'Competitive and Affordable Pricing'
            ]
        ]);

        Setting::set('services_process', [
            ['step' => 1, 'title' => 'Tell Us What You Need', 'description' => 'Fill out our quick service request form with your project specifications and requirements.'],
            ['step' => 2, 'title' => 'Get a Custom Quote', 'description' => 'We review your needs and provide a tailored plan with accurate timeline and cost.'],
            ['step' => 3, 'title' => 'We Get To Work', 'description' => 'Our developers and designers craft your solution with regular updates along the way.'],
            ['step' => 4, 'title' => 'Delivery & Support', 'description' => 'We deploy your project and provide warranty support to ensure everything runs smoothly.'],
        ]);

        Setting::set('services_faqs', [
            ['question' => 'How much does a custom website or template modification cost?', 'answer' => 'Pricing depends on project scope, complexity, and timeline. Minor template customizations start at very affordable rates, while full-stack applications are quoted based on requirements.'],
            ['question' => 'How long does it take to complete a project?', 'answer' => 'Small customizations take 1-3 business days. Full custom websites usually take between 1 to 3 weeks depending on the number of pages and integrations.'],
            ['question' => 'Do you provide support and revisions after delivery?', 'answer' => 'Yes, every service package includes post-delivery revision rounds and a warranty period to fix any issues.'],
            ['question' => 'Can you work with my existing design files or code?', 'answer' => 'Absolutely! We work with Figma, Adobe XD, Sketch, Photoshop, or any existing repository you provide.'],
            ['question' => 'What technologies and frameworks do you specialize in?', 'answer' => 'We specialize in Laravel, PHP, Next.js, React, Tailwind CSS, Bootstrap, TypeScript, Node.js, and WordPress.'],
        ]);

        Setting::set('services_help_box', [
            'title' => 'Still Have Questions?',
            'description' => "We're here to help. Send us your project details and our team will get back to you within 24 hours.",
            'button_text' => 'Request a Quote →',
            'button_url' => '#request-service',
        ]);

        // 6. Articles (Blog)
        // No articles are seeded on purpose. The blog only shows content
        // that a real author published; seeding sample posts attributed to
        // invented authors would be fabricated content.
        $articles = [];
        foreach ($articles as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 7. Update Resource Images & Metadata
        $resourceImages = [
            'travelix' => '/assets/travelix-card.png',
            'saaspro' => '/assets/saaspro-card.png',
            'shopmart' => '/assets/shopmart-card.png',
            'dashforge' => '/assets/dashforge-card.png',
            'finance-planner' => '/assets/shopmart-card.png',
            'invoice-pro' => '/assets/dashforge-card.png',
            'business-letter-pack' => '/assets/portfoliox-card.png',
            'social-media-kit' => '/assets/saaspro-card.png',
            'pitch-deck-pro' => '/assets/travelix-card.png',
        ];

        foreach ($resourceImages as $slug => $img) {
            Resource::where('slug', $slug)->update(['preview_image' => $img]);
        }

        // Add extra templates if missing. Ratings and download counters start
        // at zero and only grow from real user activity - they are never seeded
        // with invented numbers.
        $extraResources = [
            ['title' => 'Homezy', 'slug' => 'homezy', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Elegant real estate website template.', 'img' => '/assets/homezy-card.png', 'rating' => 0, 'downloads' => 0],
            ['title' => 'PortfolioX', 'slug' => 'portfoliox', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Minimal portfolio for designers and developers.', 'img' => '/assets/portfoliox-card.png', 'rating' => 0, 'downloads' => 0],
            ['title' => 'BlogMart', 'slug' => 'blogmart', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Editorial blog and magazine template.', 'img' => '/assets/blogmart-card.png', 'rating' => 0, 'downloads' => 0],
            ['title' => 'Resto', 'slug' => 'resto', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Premium restaurant and food website template.', 'img' => '/assets/resto-card.png', 'rating' => 0, 'downloads' => 0],
        ];

        foreach ($extraResources as $ex) {
            $cat = Category::where('name', $ex['cat'])->first();
            Resource::updateOrCreate(
                ['slug' => $ex['slug']],
                [
                    'category_id' => $cat?->id,
                    'title' => $ex['title'],
                    'resource_type' => $ex['type'],
                    'description' => $ex['desc'] . ' Ready to customize and deploy.',
                    'short_description' => $ex['desc'],
                    'preview_image' => $ex['img'],
                    'featured' => false,
                    'is_free' => true,
                    'price' => 0,
                    'version' => '1.0.0',
                    'license' => 'Free for Personal & Commercial Use',
                    'rating' => $ex['rating'],
                    'downloads_count' => $ex['downloads'],
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );
        }
    }
}
