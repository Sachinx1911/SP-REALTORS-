<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('path');
            $table->string('alt')->nullable();
            // gallery | floor_plan
            $table->string('type', 24)->default('gallery');
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
