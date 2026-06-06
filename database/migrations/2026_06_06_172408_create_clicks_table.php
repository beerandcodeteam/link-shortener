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
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('device_type_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('browser_id')->nullable()->constrained()->nullOnDelete();
            $table->text('referrer')->nullable();
            $table->string('ip_hash')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index('link_id');
            $table->index('clicked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};
