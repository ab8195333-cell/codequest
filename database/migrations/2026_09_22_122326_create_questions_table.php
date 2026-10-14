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
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('question_id');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('user_id')->nullable()->index('questions_user_id_foreign');
            $table->unsignedBigInteger('category_id')->index('questions_category_id_foreign');
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_faq')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
