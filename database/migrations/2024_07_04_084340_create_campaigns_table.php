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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('site_ids')->nullable();
            $table->string('frequency')->nullable();
            $table->boolean('status');
            $table->json('template_ids');
            $table->string('day')->nullable();
            $table->date('last_exec')->nullable();
            $table->boolean('active')->default(false);
            $table->string('originator');
            $table->boolean('sending_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
