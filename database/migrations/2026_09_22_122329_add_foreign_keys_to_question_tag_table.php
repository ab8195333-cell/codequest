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
        Schema::table('question_tag', function (Blueprint $table) {
            $table->foreign(['question_id'])->references(['question_id'])->on('questions')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['tag_id'])->references(['tag_id'])->on('tags')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_tag', function (Blueprint $table) {
            $table->dropForeign('question_tag_question_id_foreign');
            $table->dropForeign('question_tag_tag_id_foreign');
        });
    }
};
