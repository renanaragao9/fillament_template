<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('logo_path')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('document')->nullable();
            $table->string('status')->default('active');
            $table->json('settings')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('domain');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
