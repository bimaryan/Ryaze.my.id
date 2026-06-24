<?php
$traitUse = "    use \App\Traits\HasHashid;\n";

foreach(glob(__DIR__ . '/app/Models/*.php') as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'getHashidAttribute') !== false) {
        $content = preg_replace('/public function getHashidAttribute\(\)\s*\{\s*return (\\\\Vinkla\\\\Hashids\\\\Facades\\\\)?Hashids::encode\(\$this->id\);\s*\}/s', '', $content);
        $content = str_replace('use Vinkla\Hashids\Facades\Hashids;', '', $content);
    }
    if (strpos($content, 'HasHashid') === false) {
        $content = preg_replace('/class [a-zA-Z0-9_]+ extends [a-zA-Z0-9_]+\s*\{/', "$0\n$traitUse", $content);
        $content = preg_replace('/class User extends Authenticatable\s*\{/', "$0\n$traitUse", $content);
    }
    file_put_contents($file, $content);
}
echo 'Models refactored';
