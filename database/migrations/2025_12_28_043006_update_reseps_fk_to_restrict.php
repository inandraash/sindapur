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
        Schema::table('reseps', function (Blueprint $table) {
            // Drop existing FK constraint
            $table->dropForeign(['bahan_baku_id']);
            
            // Recreate with RESTRICT to prevent deletion of bahan_baku if used in recipes
            $table->foreign('bahan_baku_id')
                  ->references('id')
                  ->on('bahan_bakus')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            // Revert back to cascade
            $table->dropForeign(['bahan_baku_id']);
            
            $table->foreign('bahan_baku_id')
                  ->references('id')
                  ->on('bahan_bakus')
                  ->onDelete('cascade');
        });
    }
};
