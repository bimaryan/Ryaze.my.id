<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_ssh_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_project_id')->constrained('hosting_projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Nama folder klien di /mnt/storage/hosting_clients
            $table->string('slug', 100)->unique();
            $table->string('ssh_user', 100)->unique();
            // sftp = chroot + internal-sftp, shell = shell di dalam container
            $table->enum('package', ['sftp', 'shell'])->default('sftp');
            $table->string('docker_ip', 20)->unique();
            $table->unsignedInteger('host_port')->unique();
            // pending | active | failed | revoked
            $table->string('status', 20)->default('pending');
            // Kunci privat dienkripsi dengan APP_KEY
            $table->text('private_key')->nullable();
            $table->text('last_output')->nullable();
            $table->timestamp('provisioned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_ssh_accesses');
    }
};
