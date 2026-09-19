<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('tool_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('short_description')->nullable();
            $table->text('short_description_ar')->nullable();
            $table->string('icon')->nullable();
            $table->string('component_key')->nullable();
            $table->string('tool_class')->default('browser'); // 'browser', 'light_server', 'file_processing', 'heavy_conversion'
            $table->string('required_dependency')->nullable(); // 'ghostscript', 'libreoffice', 'poppler', 'imagemagick', 'phpspreadsheet'
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_new')->default(false);
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('seo_title_ar')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_description_ar')->nullable();
            $table->json('keywords')->nullable();
            $table->json('features')->nullable();
            $table->json('usage_steps')->nullable();
            $table->json('faqs')->nullable();
            $table->json('related_resource_categories')->nullable(); // e.g. ['excel', 'word'] for cross-linking
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
        });

        Schema::create('tool_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->enum('event_type', [
                'page_view',
                'tool_started',
                'upload_started',
                'processing_success',
                'processing_failed',
                'result_downloaded',
                'related_template_clicked'
            ])->default('page_view');
            $table->string('status')->default('success'); // 'success', 'failed'
            $table->unsignedInteger('processing_time_ms')->nullable();
            $table->unsignedBigInteger('input_size_bytes')->nullable();
            $table->unsignedBigInteger('output_size_bytes')->nullable();
            $table->string('user_ip_hash', 64)->nullable(); // Anonymized SHA-256 hash of IP
            $table->string('country', 10)->nullable();
            $table->string('device', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('tool_id');
            $table->index('event_type');
            $table->index('status');
            $table->index('created_at');
            $table->index('country');
        });

        Schema::create('tool_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->string('status')->default('queued'); // queued, processing, completed, failed
            $table->integer('progress_percent')->default(0);
            $table->string('status_message')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('output_filename')->nullable();
            $table->string('storage_path')->nullable();
            $table->string('download_token')->unique()->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_jobs');
        Schema::dropIfExists('tool_analytics');
        Schema::dropIfExists('tools');
        Schema::dropIfExists('tool_categories');
    }
};
