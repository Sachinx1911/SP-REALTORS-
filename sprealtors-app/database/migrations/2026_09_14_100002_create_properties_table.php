<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();

            // residential | commercial
            $table->string('property_type', 32)->default('residential');
            // buy | rent
            $table->string('purpose', 16)->default('buy');
            // 1bhk | 2bhk | 3bhk | 4bhk+ | plot | office | shop
            $table->string('configuration', 32)->nullable();
            // ready-to-move | under-construction | new-launch | sold | rented
            $table->string('status', 32)->default('ready-to-move');
            // furnished | semi-furnished | unfurnished
            $table->string('furnishing', 32)->nullable();

            $table->decimal('price', 14, 2)->nullable();
            $table->boolean('price_negotiable')->default(false);
            // for rent listings: per month
            $table->boolean('is_monthly')->default(false);

            $table->unsignedInteger('area')->nullable();
            $table->string('area_unit', 16)->default('Sq.ft.');
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('car_parking')->nullable();

            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();
            $table->json('amenities')->nullable();

            $table->string('rera_number')->nullable();
            $table->string('possession')->nullable();

            $table->string('main_image')->nullable();
            $table->string('map_url', 1000)->nullable();

            $table->string('contact_phone', 32)->nullable();
            $table->string('contact_whatsapp', 32)->nullable();

            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'is_featured']);
            $table->index(['purpose', 'property_type']);
            $table->index('price');
            $table->index('configuration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
