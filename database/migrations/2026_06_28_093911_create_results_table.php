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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('institute_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            
            // Result summary
            $table->integer('total_subjects');
            $table->decimal('total_marks', 8, 2);
            $table->decimal('total_point', 5, 2);
            $table->decimal('gpa', 3, 2);
            $table->string('final_grade', 2);
            $table->integer('total_absent')->default(0);
            $table->integer('position')->nullable();
            $table->integer('class_position')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
