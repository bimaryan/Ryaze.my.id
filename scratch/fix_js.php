<?php
$dir = new RecursiveDirectoryIterator('d:/Ryaze.my.id/resources/views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        // Match document.querySelectorAll(something).forEach(
        $newContent = preg_replace('/document\.querySelectorAll\((.*?)\)\.forEach\(/', '[].forEach.call(document.querySelectorAll($1), ', $content);
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo 'Fixed: ' . $path . PHP_EOL;
        }
    }
}
echo 'Done';
