<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 32);
            $table->string('email')->nullable();
            $table->text('message')->nullable();

            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();

            // contact | property | project | home-search
            $table->string('source', 32)->default('contact');
            // new | contacted | qualified | closed
            $table->string('status', 24)->default('new');
            $table->text('admin_notes')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
