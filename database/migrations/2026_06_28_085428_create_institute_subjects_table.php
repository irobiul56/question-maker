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
        Schema::create('institute_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('bn_name')->nullable();
            $table->string('code')->unique();
            $table->enum('type', ['compulsory', 'optional', 'elective', 'additional'])->default('compulsory');
            $table->integer('full_marks');
            $table->integer('pass_marks');
            $table->integer('theory_marks')->nullable();
            $table->integer('practical_marks')->nullable();
            $table->integer('mcq_marks')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('has_practical')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_subjects');
    }
};
