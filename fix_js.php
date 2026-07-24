<?php

function processDir($dir) {
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_dir($file)) {
            processDir($file);
        } else {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($file);
                
                // Replace [].forEach.call(document.querySelectorAll(X), Y) -> document.querySelectorAll(X).forEach(Y)
                $newContent = preg_replace(
                    '/\[\]\.forEach\.call\(\s*document\.querySelectorAll\((.*?)\)\s*,\s*(.*?)\)/s',
                    'document.querySelectorAll($1).forEach($2)',
                    $content
                );
                
                if ($newContent !== $content) {
                    file_put_contents($file, $newContent);
                    echo "Updated: $file\n";
                }
            }
        }
    }
}

processDir(__DIR__ . '/resources/views');
echo "Done!\n";
