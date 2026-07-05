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
        Schema::create('institute_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->foreignId('institute_class_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('bn_name')->nullable();
            $table->integer('numeric_value'); // 6 for Class 6, 9 for Class 9 etc.
            $table->enum('group', ['science', 'commerce', 'arts', 'general'])->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('has_elective')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_classes');
    }
};
