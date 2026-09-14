<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('developer')->nullable();

            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();

            $table->decimal('starting_price', 14, 2)->nullable();
            // e.g. "2, 3 & 4 BHK Apartments"
            $table->string('configurations')->nullable();
            $table->string('possession')->nullable();
            $table->string('rera_number')->nullable();

            $table->string('property_type', 32)->default('residential');
            // pre-launch | under-construction | ready-to-move | completed
            $table->string('status', 32)->default('under-construction');

            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();
            $table->json('amenities')->nullable();
            $table->json('nearby_places')->nullable();
            $table->json('configuration_details')->nullable();

            $table->string('hero_image')->nullable();
            $table->string('brochure')->nullable();
            $table->string('map_url', 1000)->nullable();

            $table->string('contact_phone', 32)->nullable();
            $table->string('contact_whatsapp', 32)->nullable();

            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'is_featured']);
            $table->index('starting_price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
