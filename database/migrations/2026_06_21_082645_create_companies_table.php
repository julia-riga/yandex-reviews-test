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
    Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('url')->unique();
        $table->string('yandex_id')->nullable();
        $table->string('name');
        $table->decimal('rating', 2, 1)->nullable();
        $table->integer('rating_count')->default(0);
        $table->integer('review_count')->default(0);
        $table->timestamp('last_parsed_at')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
