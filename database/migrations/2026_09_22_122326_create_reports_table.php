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
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('report_id');
            $table->unsignedBigInteger('user_id')->index('reports_user_id_foreign');
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
