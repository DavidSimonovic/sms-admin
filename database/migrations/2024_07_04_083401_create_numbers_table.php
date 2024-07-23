<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('numbers', function (Blueprint $table) {
            $table->id();
            $table->string('ad_title')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('number')->nullable();
            $table->foreignId('site_id');
            $table->boolean('active')->default(true);
            $table->boolean('bounced')->default(false);
            $table->foreignId('url_id');
            $table->boolean('script_started')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbers');
    }
};
