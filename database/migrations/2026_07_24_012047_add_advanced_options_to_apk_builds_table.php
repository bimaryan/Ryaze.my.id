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
            $table->string('theme_color')->default('#FFFFFF')->after('icon_path');
            $table->string('background_color')->default('#FFFFFF')->after('theme_color');
            $table->string('display_mode')->default('standalone')->after('background_color'); // standalone, fullscreen, minimal-ui
            $table->string('orientation')->default('default')->after('display_mode'); // default, portrait, landscape
            $table->string('version_name')->default('1.0.0')->after('orientation');
            $table->integer('version_code')->default(1)->after('version_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apk_builds', function (Blueprint $table) {
            $table->dropColumn([
                'theme_color',
                'background_color',
                'display_mode',
                'orientation',
                'version_name',
                'version_code'
            ]);
        });
    }
};
