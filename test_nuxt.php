<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$job = new App\Jobs\AutoDeployProject(new App\Models\HostingProject());
$method = new ReflectionMethod($job, 'scaffoldNuxt');
$method->setAccessible(true);
$method->invoke($job, 'test_nuxt', 'My Nuxt App');
echo "OK\n";
