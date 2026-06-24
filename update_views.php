<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->getExtension() == 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        // Regex to match route('name', $model->id) or route('name', ['id' => $model->id])
        // We use a callback to replace ->id with ->hashid inside the route() call
        $content = preg_replace_callback('/route\([^)]+\)/', function($matches) {
            return str_replace('->id', '->hashid', $matches[0]);
        }, $content);
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated routes in " . $file->getPathname() . "\n";
        }
    }
}
