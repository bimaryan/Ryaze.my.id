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
        Schema::create('hosting_nosql_databases', function (Blueprint $table) {
            $table->id();
            $table->string('hashid')->nullable()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nosql_type')->default('redis');
            $table->string('db_username')->nullable(); // Redis ACL user
            $table->text('db_password'); // Encrypted
            $table->string('host')->nullable();
            $table->integer('port')->default(6379);
            $table->string('keyspace_prefix')->nullable(); // For Redis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_nosql_databases');
    }
};
