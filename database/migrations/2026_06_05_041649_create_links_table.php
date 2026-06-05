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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->_notNull();
            $table->foreignId('link_status_id')->constrained();
            $table->text('original_url');
            $table->string('short_code')->unique();
            $table->unsignedInteger('click_count')->default(0);
            $table->timestamps();

            $table->index('short_code', 'links_short_code_unique');
            $table->index('user_id');
            $table->index('link_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
