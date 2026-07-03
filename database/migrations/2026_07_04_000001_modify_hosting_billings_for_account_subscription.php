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
        Schema::table('hosting_billings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            // Drop existing foreign key and make hosting_project_id nullable
            $table->dropForeign(['hosting_project_id']);
            $table->unsignedBigInteger('hosting_project_id')->nullable()->change();
            
            // Re-add foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hosting_project_id')->references('id')->on('hosting_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosting_billings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            $table->dropForeign(['hosting_project_id']);
            $table->unsignedBigInteger('hosting_project_id')->nullable(false)->change();
            $table->foreign('hosting_project_id')->references('id')->on('hosting_projects')->onDelete('cascade');
        });
    }
};
