<?php

namespace App\Helpers;

class AppVersion
{
    /**
     * Get the app version based on git commit count.
     * Formula: commits => v{major}.{minor}.{patch}
     * - major: commits / 1000 (integer)
     * - minor: (commits % 1000) / 100 (integer)
     * - patch: (commits % 100)
     * Example: 930 commits => v1.9.30
     */
    public static function get(): string
    {
        static $version = null;

        if ($version !== null) {
            return $version;
        }

        try {
            $output = @shell_exec('git rev-list --count HEAD');
            $count = (int) trim((string) $output);
            if ($count <= 0) {
                $version = 'v1.0.0';
                return $version;
            }
        } catch (\Throwable) {
            $version = 'v1.0.0';
            return $version;
        }

        $major = intdiv($count, 1000) + 1;
        $minor = intdiv($count % 1000, 100);
        $patch = $count % 100;

        $version = "v{$major}.{$minor}.{$patch}";

        return $version;
    }
}
