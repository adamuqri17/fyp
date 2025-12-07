<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('graves', function (Blueprint $table) {
            $table->id('grave_id');

            // Foreign keys
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('section_id');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('available');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('admin_id')->references('admin_id')->on('administrators')->onDelete('cascade');
            $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graves');
    }
};
