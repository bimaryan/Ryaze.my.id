<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/')
);

$project = new \App\Models\HostingProject();
$project->hashid = 'GWpmbk8XdzJn';
$project->project_name = 'Test Project';
$project->ryaze_domain = 'test.ryaze.my.id';
$project->status = 'active';
$project->dev_port = 8080;
$project->dev_mode = false;
$project->repo_source = 'upload:test.zip';
$project->branch = 'main';
$project->framework = 'html';
$project->nginx_error = '';
$project->nginx_custom = '';

$html = view('pages.hosting.user.show', [
    'project' => $project,
    'fwIcon' => 'fa-brands fa-laravel',
    'displayUrl' => 'test.ryaze.my.id',
    'statusClass' => 'bg-green-500',
    'statusIcon' => 'fa-check',
    'unpaidPayment' => null,
    'nginxBadge' => ['bg-blue-500', 'fa-check', 'Active'],
    'nginxStatus' => 'active',
    'd' => 'test.ryaze.my.id',
    'defaultNginxConf' => 'server {}',
    'diskUsage' => '10 MB',
    'visitorsCount' => 10
])->render();

file_put_contents('rendered.html', $html);
echo "OK\n";
