<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('editor')->after('password');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('name');
            $table->json('slug');
            $table->string('icon')->nullable();
            $table->string('pin_color')->default('#5B3A7A');
            $table->string('cover_url')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('name');
            $table->string('icon')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->json('short_text')->nullable();
            $table->json('highlights')->nullable();
            $table->json('brands')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->default('7800');
            $table->string('city')->default('Ath');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->date('member_since')->nullable();
            $table->boolean('holiday_closed')->default(false);
            $table->boolean('open_sundays')->default(false);
            $table->text('internal_notes')->nullable();
            $table->text('search_text')->nullable();
            $table->string('cover_url')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('category_merchant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->unique(['category_id', 'merchant_id']);
        });

        Schema::create('merchant_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unique(['merchant_id', 'service_id']);
        });

        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->time('opens_at');
            $table->time('closes_at');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->json('message')->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('network');
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('status')->default('pending');
            $table->date('paid_on')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('change_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('sender_role')->default('visitor');
            $table->json('payload');
            $table->json('original')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamps();
        });

        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('color')->default('#5B3A7A');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('title');
            $table->string('system_key')->nullable()->unique();
            $table->json('recipients')->nullable();
            $table->json('success_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->boolean('ack_enabled')->default(true);
            $table->json('ack_subject')->nullable();
            $table->json('ack_body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->json('label');
            $table->json('help')->nullable();
            $table->string('name')->nullable();
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->string('width')->default('full');
            $table->string('accept')->nullable();
            $table->unsignedInteger('max_size')->nullable();
            $table->unsignedInteger('max_files')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('recurrence_rule')->nullable();
            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('price')->nullable();
            $table->string('external_url')->nullable();
            $table->foreignId('form_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('capacity')->nullable();
            $table->timestamp('registration_deadline')->nullable();
            $table->boolean('waitlist')->default(false);
            $table->string('status')->default('draft');
            $table->string('cover_url')->nullable();
            $table->unsignedInteger('proposed_by_submission_id')->nullable();
            $table->timestamps();
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->json('description')->nullable();
            $table->json('conditions')->nullable();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status')->default('draft');
            $table->string('cover_url')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->string('category')->default('association');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_press_release')->default(false);
            $table->string('cover_url')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug');
            $table->json('blocks')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 5)->default('fr');
            $table->json('data');
            $table->string('status')->default('new');
            $table->text('internal_notes')->nullable();
            $table->string('ip_hash')->nullable();
            $table->json('status_history')->nullable();
            $table->timestamps();
        });

        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name');
            $table->string('vat_number')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('legacy_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_redirects');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('membership_applications');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('events');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('event_types');
        Schema::dropIfExists('change_suggestions');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('closures');
        Schema::dropIfExists('opening_hours');
        Schema::dropIfExists('merchant_service');
        Schema::dropIfExists('category_merchant');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('services');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'last_login_at']);
        });
    }
};
