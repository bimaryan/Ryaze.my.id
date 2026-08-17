<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/')
);
// Bootstrapped.

config(['database.connections.mysql.host' => '127.0.0.1']);

$user = \App\Models\User::first();
Auth::login($user);

$project = \App\Models\HostingProject::first();

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
