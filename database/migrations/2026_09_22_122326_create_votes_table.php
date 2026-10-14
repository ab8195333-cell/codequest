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
        Schema::create('votes', function (Blueprint $table) {
            $table->bigIncrements('vote_id');
            $table->unsignedBigInteger('user_id')->index('votes_user_id_foreign');
            $table->string('votable_type');
            $table->unsignedBigInteger('votable_id');
            $table->string('vote_type');
            $table->timestamps();

            $table->index(['votable_type', 'votable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
