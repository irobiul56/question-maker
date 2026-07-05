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
        Schema::create('institute_class_institute_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('institute_subject_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['compulsory', 'optional', 'elective'])->default('compulsory');
            $table->integer('full_marks');
            $table->integer('pass_marks');
            $table->boolean('is_group_based')->default(false);
            $table->string('group_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_class_institute_subjects');
    }
};
