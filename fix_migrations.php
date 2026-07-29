<?php
$files = [
    'd:\Ryaze.my.id\database\migrations\2026_07_29_184045_modify_plan_enum_in_hosting_billings_table.php',
    'd:\Ryaze.my.id\database\migrations\2026_07_10_212000_update_framework_enum_add_vue_in_hosting_projects.php',
    'd:\Ryaze.my.id\database\migrations\2026_07_01_124127_update_framework_enum_in_hosting_projects.php',
    'd:\Ryaze.my.id\database\migrations\2026_06_24_062257_update_status_enum_in_hosting_projects.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Wrap DB::statement(...) in if (DB::getDriverName() !== 'sqlite') { ... }
        // Ensure we don't wrap it twice
        if (strpos($content, "if (DB::getDriverName() !== 'sqlite')") === false) {
            $content = preg_replace('/(DB::statement\(.*?\);)/', "if (DB::getDriverName() !== 'sqlite') {\n            $1\n        }", $content);
            file_put_contents($file, $content);
            echo "Fixed $file\n";
        } else {
            echo "Already fixed $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
