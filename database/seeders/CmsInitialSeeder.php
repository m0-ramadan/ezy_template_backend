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
        Setting::set('site_description', 'Discover thousands of free and premium website templates, UI kits, Excel sheets, and design resources.');
        Setting::set('contact_email', 'support@ezytemplate.com');
        Setting::set('contact_phone', '+20 100 000 0000');
        Setting::set('contact_address', 'Cairo, Egypt');
        Setting::set('copyright_text', '© 2026 EzyTemplate by Ezystore. All rights reserved.');
        Setting::set('footer_subtext', 'Build • Create • Share • Grow');

        Setting::set('social_links', [
            ['platform' => 'Twitter/X', 'url' => 'https://twitter.com', 'icon' => 'twitter', 'is_active' => true],
            ['platform' => 'GitHub', 'url' => 'https://github.com', 'icon' => 'github', 'is_active' => true],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com', 'icon' => 'linkedin', 'is_active' => true],
            ['platform' => 'YouTube', 'url' => 'https://youtube.com', 'icon' => 'youtube', 'is_active' => true],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com', 'icon' => 'instagram', 'is_active' => true],
        ]);

        // 2. Navigation Menus
        Setting::set('header_nav_links', [
            ['label' => 'Home', 'label_ar' => 'الرئيسية', 'url' => '/', 'is_active' => true, 'sort_order' => 1],
            ['label' => 'Templates', 'label_ar' => 'القوالب', 'url' => '/templates', 'is_active' => true, 'sort_order' => 2],
            ['label' => 'Categories', 'label_ar' => 'الأقسام والتصنيفات', 'url' => '/categories', 'is_active' => true, 'sort_order' => 3],
            ['label' => 'Blog', 'label_ar' => 'المدونة', 'url' => '/blog', 'is_active' => true, 'sort_order' => 4],
            ['label' => 'Services', 'label_ar' => 'خدماتنا', 'url' => '/services', 'is_active' => true, 'sort_order' => 5],
            ['label' => 'About', 'label_ar' => 'من نحن', 'url' => '/about', 'is_active' => true, 'sort_order' => 6],
        ]);

        Setting::set('footer_columns', [
            [
                'title' => 'Quick Links',
                'title_ar' => 'روابط سريعة',
                'links' => [
                    ['label' => 'Home', 'label_ar' => 'الرئيسية', 'url' => '/'],
                    ['label' => 'Templates', 'label_ar' => 'القوالب والتصاميم', 'url' => '/templates'],
                    ['label' => 'Categories', 'label_ar' => 'الأقسام والتصنيفات', 'url' => '/categories'],
                    ['label' => 'Blog', 'label_ar' => 'المدونة والمقالات', 'url' => '/blog'],
                    ['label' => 'Services', 'label_ar' => 'الخدمات والحلول', 'url' => '/services'],
                    ['label' => 'About Us', 'label_ar' => 'من نحن', 'url' => '/about'],
                ]
            ],
            [
                'title' => 'Resources',
                'title_ar' => 'الموارد والقوالب',
                'links' => [
                    ['label' => 'Freebies', 'label_ar' => 'قوالب مجانية', 'url' => '/templates?price=Free'],
                    ['label' => 'UI Kits', 'label_ar' => 'حزم واجهات المستخدم', 'url' => '/templates?cat=UI+Kit'],
                    ['label' => 'Excel Templates', 'label_ar' => 'قوالب إكسيل', 'url' => '/templates?type=Excel'],
                    ['label' => 'Word Templates', 'label_ar' => 'قوالب وورد', 'url' => '/templates?type=Word'],
                    ['label' => 'Design Resources', 'label_ar' => 'تصاميم وجرافيك', 'url' => '/templates?type=Design'],
                    ['label' => 'Documentation', 'label_ar' => 'الشروحات والمقالات', 'url' => '/blog'],
                ]
            ],
            [
                'title' => 'Help & Support',
                'title_ar' => 'المساعدة والدعم',
                'links' => [
                    ['label' => 'FAQ', 'label_ar' => 'الأسئلة الشائعة', 'url' => '/services'],
                    ['label' => 'Custom Requests', 'label_ar' => 'طلب تخصيص قالب', 'url' => '/services'],
                    ['label' => 'Technical Support', 'label_ar' => 'الدعم الفني', 'url' => '/services'],
                    ['label' => 'Contact Us', 'label_ar' => 'تواصل معنا', 'url' => '/about'],
                    ['label' => 'Privacy Policy', 'label_ar' => 'سياسة الخصوصية', 'url' => '/about'],
                    ['label' => 'Terms of Service', 'label_ar' => 'شروط الاستخدام', 'url' => '/about'],
                ]
            ]
        ]);

        // 3. Homepage Content (CMS Home)
        Setting::set('home_hero', [
            'eyebrow' => 'Free & Premium Website Templates',
            'title' => 'Beautiful Templates',
            'highlight_text' => 'for Every Project',
            'lead' => 'Discover thousands of free and premium website templates, UI kits, and design resources. Download, customize, and build your next amazing project faster.',
            'hero_image' => '/assets/hero-main.png',
            'note_title' => 'Your Next Website',
            'note_subtitle' => 'Starts Here',
            'stat_badge_1_val' => '1000+',
            'stat_badge_1_label' => 'Free Templates',
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
            ['icon' => 'Users', 'title' => 'Trusted by Developers', 'description' => 'Join thousands of developers and creators worldwide building with EzyTemplate.'],
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

        Setting::set('about_stats', [
            ['icon' => 'Download', 'count' => '1000+', 'label' => 'Templates Available'],
            ['icon' => 'Users', 'count' => '50K+', 'label' => 'Happy Users'],
            ['icon' => 'Star', 'count' => '4.9/5', 'label' => 'Average Rating'],
            ['icon' => 'Globe', 'count' => '150+', 'label' => 'Countries'],
        ]);

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

        // 6. Seed Articles (Blog)
        $articles = [
            [
                'title' => '10 Modern Web Design Trends for 2026',
                'slug' => '10-modern-web-design-trends-for-2026',
                'excerpt' => 'Explore the latest web design trends that will dominate in 2026, from AI-powered interfaces to immersive user experiences.',
                'content' => "## The Evolution of Web Design in 2026\n\nWeb design continues to evolve at breakneck speed. In 2026, web developers and UI designers are embracing new paradigms that blend high performance with stunning aesthetic appeal.\n\n### 1. AI-Assisted Personalization\nWebsites are adapting to user preferences in real time, from micro-interactions to content recommendations.\n\n### 2. Glassmorphism and Depth\nLayered semi-transparent cards with frosted backdrops and realistic physics create tangible depth without sacrificing performance.\n\n### 3. Micro-Animations and Micro-Interactions\nSubtle hover states and page transitions guide users naturally through digital workflows.\n\n### Conclusion\nStaying ahead of design trends ensures your products stand out and convert visitors effectively.",
                'cover_image' => '/assets/blog-web-card.png',
                'category' => 'Web Design',
                'author_name' => 'Ahmed Ali',
                'reading_time' => 8,
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Complete Guide to Next.js 15 for Beginners',
                'slug' => 'complete-guide-to-next-js-for-beginners',
                'excerpt' => 'Learn Next.js from scratch with practical examples, project structure, Turbopack, and deployment tips.',
                'content' => "## Getting Started with Next.js\n\nNext.js has become the premier framework for building full-stack React applications. With the App Router, server components, and Turbopack, building fast web applications has never been easier.\n\n### Why Next.js?\n- **Server Components:** Render heavy UI on the server to keep bundle sizes tiny.\n- **Built-in Optimizations:** Automatic image optimization, font caching, and script handling.\n- **Route Handlers:** Simple RESTful API endpoints right inside your Next.js directory.",
                'cover_image' => '/assets/blog-next-card.png',
                'category' => 'Next.js',
                'author_name' => 'Sara Mohamed',
                'reading_time' => 12,
                'status' => 'published',
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Build a Complete Laravel Ecommerce Project',
                'slug' => 'build-a-complete-laravel-ecommerce-project',
                'excerpt' => 'Step-by-step tutorial to build a full ecommerce website with Laravel 11, including payment integration.',
                'content' => "## Crafting Scalable Ecommerce with Laravel\n\nLaravel provides an elegant, expressive syntax combined with powerful features like Eloquent ORM, queues, and robust authentication.\n\nIn this comprehensive guide, we cover designing product schemas, managing carts with sessions, and processing webhooks seamlessly.",
                'cover_image' => '/assets/blog-laravel-card.png',
                'category' => 'Laravel',
                'author_name' => 'Omar Khaled',
                'reading_time' => 10,
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'UI/UX Best Practices for Higher Conversions',
                'slug' => 'ui-ux-best-practices-for-higher-conversions',
                'excerpt' => 'Learn proven UI/UX techniques to improve user engagement, reduce bounce rates, and boost conversion.',
                'content' => "## Designing for Impact and Conversions\n\nGreat design is not just how it looks—it is how effortlessly it works. Reducing cognitive load, establishing clear visual hierarchies, and placing focal calls-to-action can dramatically increase user satisfaction.",
                'cover_image' => '/assets/blog-ui-card.png',
                'category' => 'UI/UX',
                'author_name' => 'Nourhan Tarek',
                'reading_time' => 8,
                'status' => 'published',
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'How to Create a High-Converting Product Page',
                'slug' => 'how-to-create-a-high-converting-product-page',
                'excerpt' => 'Tips and real-world examples to design product pages that increase sales and build customer trust.',
                'content' => "## The Anatomy of an Effective Product Page\n\nA high-converting product page must quickly communicate value, answer questions, provide social proof, and present a clear call to action.",
                'cover_image' => '/assets/blog-ecom-card.png',
                'category' => 'eCommerce',
                'author_name' => 'Mostafa Ezz',
                'reading_time' => 9,
                'status' => 'published',
                'published_at' => now()->subDays(18),
            ],
            [
                'title' => 'Top 10 Free WordPress Themes for 2026',
                'slug' => 'top-10-free-wordpress-themes-for-2026',
                'excerpt' => 'A curated list of the best free WordPress themes for blogs, business portfolios, and modern stores.',
                'content' => "## The Best Free WordPress Themes\n\nLooking for lightweight, block-based, and SEO-friendly WordPress themes? We tested dozens of options to bring you the top performers for 2026.",
                'cover_image' => '/assets/blog-wp-card.png',
                'category' => 'WordPress',
                'author_name' => 'Layla Hassan',
                'reading_time' => 7,
                'status' => 'published',
                'published_at' => now()->subDays(22),
            ],
        ];

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

        // Add extra templates from front/data/templates.ts if missing
        $extraResources = [
            ['title' => 'Homezy', 'slug' => 'homezy', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Elegant real estate website template.', 'img' => '/assets/homezy-card.png', 'rating' => 4.6, 'downloads' => 9300],
            ['title' => 'PortfolioX', 'slug' => 'portfoliox', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Minimal portfolio for designers and developers.', 'img' => '/assets/portfoliox-card.png', 'rating' => 4.8, 'downloads' => 6700],
            ['title' => 'BlogMart', 'slug' => 'blogmart', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Editorial blog and magazine template.', 'img' => '/assets/blogmart-card.png', 'rating' => 4.5, 'downloads' => 4900],
            ['title' => 'Resto', 'slug' => 'resto', 'type' => 'website', 'cat' => 'Website Templates', 'desc' => 'Premium restaurant and food website template.', 'img' => '/assets/resto-card.png', 'rating' => 4.7, 'downloads' => 7100],
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
