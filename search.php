<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->getExtension() == 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match_all('/route\([^\)]+id[^\)]*\)/', $content, $matches)) {
            echo $file->getPathname() . "\n";
            foreach ($matches[0] as $match) {
                echo "  " . $match . "\n";
            }
        }
    }
}
