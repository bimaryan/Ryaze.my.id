<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$reflection = new ReflectionClass(\App\Jobs\AutoDeployProject::class);
$job = $reflection->newInstanceWithoutConstructor();

$templates = [
    'tailwind_portfolio' => 'scaffoldTailwindPortfolio',
    'tailwind_landing' => 'scaffoldTailwindLanding',
    'tailwind_blog' => 'scaffoldTailwindBlog',
    'tailwind_ecommerce' => 'scaffoldTailwindEcommerce',
    'tailwind_admin' => 'scaffoldTailwindAdmin',
    'tailwind_linkinbio' => 'scaffoldTailwindLinkinbio',
];

foreach ($templates as $key => $method) {
    $m = $reflection->getMethod($method);
    $m->setAccessible(true);
    
    $tmpDir = __DIR__ . '/storage/app/tmp_' . $key;
    @mkdir($tmpDir, 0755, true);
    
    $m->invokeArgs($job, [$tmpDir, 'Preview ' . ucwords(str_replace('_', ' ', $key))]);
    
    $html = file_get_contents($tmpDir . '/index.html');
    file_put_contents(__DIR__ . '/resources/views/previews/' . $key . '.blade.php', $html);
    
    @unlink($tmpDir . '/index.html');
    @rmdir($tmpDir);
}
echo "Done\n";
