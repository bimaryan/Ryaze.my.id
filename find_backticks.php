<?php
$file = 'resources/views/pages/hosting/user/show.blade.php';
$lines = file($file);
$found = 0;
foreach ($lines as $i => $line) {
    // Find lines with \` (backslash+backtick) OR \${ (backslash+dollar+brace) outside of data-* attributes
    if (strpos($line, chr(92) . chr(96)) !== false || strpos($line, chr(92) . '${') !== false) {
        echo ($i + 1) . ': ' . trim($line) . "\n";
        $found++;
    }
}
echo "\nTotal found: $found\n";
