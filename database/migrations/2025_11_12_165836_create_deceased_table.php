<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deceased', function (Blueprint $table) {
            $table->id('deceased_id');
            $table->unsignedBigInteger('grave_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('full_name');
            $table->string('ic_number')->unique();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_death');
            $table->time('time_of_death')->nullable();
            $table->date('burial_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('grave_id')->references('grave_id')->on('graves')->onDelete('cascade');
            $table->foreign('admin_id')->references('admin_id')->on('administrators')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deceased');
    }
};
