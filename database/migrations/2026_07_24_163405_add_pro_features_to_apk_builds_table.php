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
        Schema::table('apk_builds', function (Blueprint $table) {
            $table->boolean('enable_notifications')->default(false)->after('version_code');
            $table->string('fallback_type')->default('customtabs')->after('enable_notifications');
            $table->integer('splash_fade_duration')->default(300)->after('fallback_type');
            $table->string('navigation_color')->nullable()->after('splash_fade_duration');
            $table->string('keystore_alias')->nullable()->after('navigation_color');
            $table->string('keystore_password')->nullable()->after('keystore_alias');
            $table->string('key_password')->nullable()->after('keystore_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apk_builds', function (Blueprint $table) {
            $table->dropColumn([
                'enable_notifications',
                'fallback_type',
                'splash_fade_duration',
                'navigation_color',
                'keystore_alias',
                'keystore_password',
                'key_password',
            ]);
        });
    }
};
