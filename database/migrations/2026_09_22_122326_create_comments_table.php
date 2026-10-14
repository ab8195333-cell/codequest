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
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('comment_id');
            $table->text('content');
            $table->unsignedBigInteger('user_id')->nullable()->index('comments_user_id_foreign');
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['commentable_type', 'commentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
