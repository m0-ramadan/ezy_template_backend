<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, MainCategory, Category, Subcategory, Resource, ResourceFile, Article, Setting};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@ezytemplate.local')],
            ['name' => 'EzyTemplate Super Admin', 'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!')), 'role' => 'admin', 'status' => 'active', 'email_verified_at' => now()]
        );

        $mainCategoriesData = [
            [
                'name' => 'Website Templates',
                'name_ar' => 'قوالب المواقع الإلكترونية',
                'slug' => 'website-templates',
                'icon' => 'globe',
                'color' => '#2563EB',
                'categories' => [
                    ['HTML & CSS', 'html-css', ['Corporate', 'Portfolio', 'Landing Pages', 'E-commerce']],
                    ['WordPress', 'wordpress', ['Business', 'Blog', 'Shop', 'Agency']],
                    ['React & Next.js', 'react-nextjs', ['SaaS', 'Dashboards', 'Portfolios']],
                    ['Tailwind CSS', 'tailwind-css', ['Admin', 'Components', 'Marketing']]
                ]
            ],
            [
                'name' => 'Excel Templates',
                'name_ar' => 'قوالب إكسيل',
                'slug' => 'excel-templates',
                'icon' => 'table',
                'color' => '#10B981',
                'categories' => [
                    ['Calendars', 'calendars', ['Yearly Calendar', 'Monthly Calendar', 'Weekly Calendar']],
                    ['Planners & Schedules', 'planners-schedules', ['Work Schedules', 'Employee Schedules', 'Personal Planners', 'Daily Planners']],
                    ['Budgets & Personal Finance', 'budgets', ['Personal Budget', 'Family Budget', 'Business Budget', 'Expense Tracking']],
                    ['Invoice Templates', 'invoices', ['Quotes', 'Estimates', 'Purchase Orders', 'Receipts']],
                    ['Timesheets & HR', 'timesheets-hr', ['Timesheets', 'Attendance', 'Payroll', 'Leave Management']],
                    ['Inventory', 'inventory', ['Stock Management', 'Warehouse Inventory', 'Reorder Tracker']],
                    ['Education', 'education', ['Gradebook', 'Class Schedule', 'Study Planner']]
                ]
            ],
            [
                'name' => 'Word Templates',
                'name_ar' => 'قوالب وورد',
                'slug' => 'word-templates',
                'icon' => 'file-text',
                'color' => '#3B82F6',
                'categories' => [
                    ['Resumes & CVs', 'resumes-cvs', ['Modern CV', 'Executive Resume', 'Cover Letters']],
                    ['Business Letters', 'business-letters', ['Formal Letters', 'Proposals', 'Contracts']],
                    ['Reports & Invoices', 'reports-invoices', ['Monthly Report', 'Invoice Template']]
                ]
            ],
            [
                'name' => 'Design Templates',
                'name_ar' => 'قوالب التصميم',
                'slug' => 'design-templates',
                'icon' => 'palette',
                'color' => '#EC4899',
                'categories' => [
                    ['Social Media', 'social-media', ['Instagram Posts', 'Facebook Banners', 'LinkedIn Covers']],
                    ['Branding & Logos', 'branding-logos', ['Logo Vectors', 'Brand Guidelines']],
                    ['Print Templates', 'print-templates', ['Flyers', 'Brochures', 'Business Cards']]
                ]
            ],
            [
                'name' => 'Presentation Templates',
                'name_ar' => 'قوالب العروض التقديمية',
                'slug' => 'presentation-templates',
                'icon' => 'presentation',
                'color' => '#8B5CF6',
                'categories' => [
                    ['PowerPoint', 'powerpoint', ['Pitch Decks', 'Business Presentations', 'Education']],
                    ['Google Slides', 'google-slides', ['Startup Deck', 'Marketing Deck']],
                    ['Keynote', 'keynote', ['Creative Pitch', 'Minimalist Slide Deck']]
                ]
            ]
        ];

        foreach ($mainCategoriesData as $mIndex => $mCatData) {
            $mainCat = MainCategory::updateOrCreate(
                ['slug' => $mCatData['slug']],
                [
                    'name' => $mCatData['name'],
                    'name_ar' => $mCatData['name_ar'],
                    'icon' => $mCatData['icon'],
                    'color' => $mCatData['color'],
                    'sort_order' => $mIndex,
                    'is_active' => true
                ]
            );

            foreach ($mCatData['categories'] as $cIndex => $catInfo) {
                $cat = Category::updateOrCreate(
                    ['slug' => $catInfo[1]],
                    [
                        'main_category_id' => $mainCat->id,
                        'name' => $catInfo[0],
                        'name_ar' => $catInfo[0],
                        'description' => 'Curated ' . $catInfo[0] . ' templates.',
                        'icon' => $mCatData['icon'],
                        'color' => $mCatData['color'],
                        'sort_order' => $cIndex,
                        'is_active' => true
                    ]
                );

                foreach ($catInfo[2] as $sIndex => $subName) {
                    Subcategory::updateOrCreate(
                        ['slug' => Str::slug($subName)],
                        [
                            'category_id' => $cat->id,
                            'name' => $subName,
                            'name_ar' => $subName,
                            'sort_order' => $sIndex,
                            'is_active' => true
                        ]
                    );
                }
            }
        }

        // Seed comprehensive Excel Templates Taxonomy
        $excelCatMap = [
            'calendars' => [
                'Excel Calendar Template',
                'Yearly Calendar Template',
                'Monthly Calendar',
                'Weekly Calendar Template'
            ],
            'planners-schedules' => [
                'Work Schedule',
                'Weekly Schedule',
                'Shift Schedule',
                'Employee Schedule Template',
                'Rota Template',
                'Personal Planner',
                'Daily Planner',
                'Weekly Planner',
                'Monthly Planner Template',
                'Bullet Journaling Templates'
            ],
            'budgets' => [
                'Income and Expense Worksheet',
                'Simple Budget Template',
                'Money Management Template',
                'Money Tracker for Mobile Excel',
                'Personal Budget',
                'Personal Monthly Budget',
                'Family Budget Planner',
                'Household Budget Worksheet',
                'Checkbook',
                'Account Register',
                'Expense Tracker',
                'Event Budget',
                'Business Budget Template'
            ],
            'invoices' => [
                'Free Invoice Template',
                'Billing Invoice Template',
                'Invoice Tracking Template',
                'Simple Invoice',
                'Consulting Invoice',
                'Quote Template',
                'Estimate Template',
                'Work Order Template',
                'Purchase Order',
                'Billing Statement',
                'Receipt Templates'
            ],
            'timesheets-hr' => [
                'Timesheet Template',
                'Free Time Card Calculator',
                'Timesheet with Breaks',
                'Timecard',
                'Time Log Template',
                'Expense Report',
                'Reimbursement Form',
                'Vacation Tracking',
                'Employee Leave Tracker',
                'Organization Chart',
                'Fax Cover Sheet Template',
                'Job Application Template',
                'Employee Payroll Template',
                'Payslip Template'
            ],
            'inventory' => [
                'Inventory Control Template',
                'Home Inventory',
                'Stock Management Sheet',
                'Warehouse Inventory Tracker'
            ],
            'education' => [
                'Gradebook Tracker',
                'Class Schedule Planner',
                'Student Assignment Tracker'
            ]
        ];

        foreach ($excelCatMap as $catSlug => $items) {
            $catModel = Category::where('slug', $catSlug)->first();
            if (!$catModel) continue;

            foreach ($items as $idx => $title) {
                $res = Resource::updateOrCreate(
                    ['slug' => Str::slug($title)],
                    [
                        'category_id' => $catModel->id,
                        'title' => $title,
                        'resource_type' => 'excel',
                        'description' => $title . ' — Fully customizable Excel spreadsheet template with automated formulas, clean layout, and print-ready formatting.',
                        'short_description' => 'Professional ' . $title . ' for Excel & Google Sheets',
                        'featured' => $idx < 2,
                        'is_free' => true,
                        'price' => 0,
                        'version' => '1.0.0',
                        'license' => 'Free for Personal & Commercial Use',
                        'status' => 'published',
                        'published_at' => now()->subDays($idx)
                    ]
                );

                ResourceFile::updateOrCreate(
                    ['resource_id' => $res->id, 'quality' => 'Standard'],
                    [
                        'label' => 'Excel XLSX Format',
                        'path' => 'files/' . Str::slug($title) . '.xlsx',
                        'original_name' => Str::slug($title) . '.xlsx',
                        'extension' => 'xlsx',
                        'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'size_bytes' => 148576,
                        'quality' => 'Standard',
                        'format' => 'xlsx',
                        'is_primary' => true
                    ]
                );
            }
        }

        // Seed Website Templates
        $webCat = Category::where('slug', 'html-css')->first();
        if ($webCat) {
            foreach ([['Travelix', 'Travel & Tourism Landing'], ['SaaSPro', 'SaaS Product Template'], ['ShopMart', 'eCommerce Store Template']] as $i => $wItem) {
                $res = Resource::updateOrCreate(
                    ['slug' => Str::slug($wItem[0])],
                    [
                        'category_id' => $webCat->id,
                        'title' => $wItem[0],
                        'resource_type' => 'website',
                        'description' => $wItem[1] . ' — HTML5, Bootstrap 5 & Tailwind CSS responsive theme.',
                        'short_description' => $wItem[1],
                        'featured' => true,
                        'is_free' => true,
                        'price' => 0,
                        'version' => '1.0.0',
                        'status' => 'published',
                        'published_at' => now()->subDays($i)
                    ]
                );

                ResourceFile::updateOrCreate(
                    ['resource_id' => $res->id, 'quality' => 'Standard'],
                    [
                        'label' => 'HTML5 Source Zip',
                        'path' => 'files/' . Str::slug($wItem[0]) . '.zip',
                        'original_name' => Str::slug($wItem[0]) . '.zip',
                        'extension' => 'zip',
                        'mime_type' => 'application/zip',
                        'size_bytes' => 1245876,
                        'quality' => 'Standard',
                        'format' => 'zip',
                        'is_primary' => true
                    ]
                );
            }
        }

        foreach (['site_name' => 'EzyTemplate', 'site_tagline' => 'Beautiful templates for every project', 'admin_email' => env('ADMIN_EMAIL', 'admin@ezytemplate.local'), 'items_per_page' => '24', 'analytics_retention_days' => '365'] as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v, 'type' => 'string']);
        }

        // Seed the complete 188-item Canva catalog.
        $this->call(CanvaTemplatesSeeder::class);

        // Seed the uploaded Excel/Word/PowerPoint template library.
        $this->call(OfficeTemplateLibrarySeeder::class);
    }
}
