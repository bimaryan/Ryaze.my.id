<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$job = new App\Jobs\AutoDeployProject(new App\Models\HostingProject());
$method = new ReflectionMethod($job, 'scaffoldTailwind');
$method->setAccessible(true);
$method->invoke($job, 'test_tailwind', 'My Tailwind App');
echo "OK\n";
