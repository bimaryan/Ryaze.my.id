<?php

namespace Tests\Feature\Hosting;

use App\Models\HostingProject;
use App\Models\User;
use App\Services\NodeServerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NextjsDeployTest extends TestCase
{
    use RefreshDatabase;

    public function test_node_server_resolves_nextjs_commands()
    {
        $srv = app(NodeServerService::class);
        $tempDir = sys_get_temp_dir() . '/test_nextjs_' . uniqid();
        @mkdir($tempDir, 0777, true);

        // Case 1: package.json with scripts.start = "next start"
        file_put_contents($tempDir . '/package.json', json_encode([
            'name' => 'my-nextjs-app',
            'scripts' => [
                'start' => 'next start',
            ],
        ]));

        $log = [];
        $cmdReflect = new \ReflectionClass($srv);
        $method = $cmdReflect->getMethod('resolveStartCommand');
        $method->setAccessible(true);

        $cmd = $method->invokeArgs($srv, [$tempDir, null, &$log]);
        $this->assertEquals(['npm', 'start'], $cmd);

        // Case 2: .next directory exists but no scripts.start
        @unlink($tempDir . '/package.json');
        @mkdir($tempDir . '/.next', 0777, true);
        $log = [];
        $cmd = $method->invokeArgs($srv, [$tempDir, null, &$log]);
        $this->assertEquals(['npx', 'next', 'start'], $cmd);

        // Case 3: standalone nextjs
        @mkdir($tempDir . '/.next/standalone', 0777, true);
        file_put_contents($tempDir . '/.next/standalone/server.js', '// standalone');
        $log = [];
        $cmd = $method->invokeArgs($srv, [$tempDir, null, &$log]);
        $this->assertEquals(['node', '.next/standalone/server.js'], $cmd);

        // Cleanup
        @unlink($tempDir . '/.next/standalone/server.js');
        @rmdir($tempDir . '/.next/standalone');
        @rmdir($tempDir . '/.next');
        @rmdir($tempDir);
    }

    public function test_fallback_html_is_written_for_node_ssr()
    {
        $project = new HostingProject([
            'framework' => 'nextjs',
            'ryaze_domain' => 'inovainvite.ryz.my.id',
            'status' => 'active',
        ]);

        $job = new \App\Jobs\AutoDeployProject($project);
        $jobReflect = new \ReflectionClass($job);
        $method = $jobReflect->getMethod('writeNodeFallbackHtml');
        $method->setAccessible(true);

        $tempFile = sys_get_temp_dir() . '/test_fallback_' . uniqid() . '.html';
        $method->invokeArgs($job, [$tempFile]);

        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertStringContainsString('Ryaze Cloud Hosting', $content);
        $this->assertStringContainsString('Aplikasi Sedang Memulai', $content);
        $this->assertStringContainsString('window.location.reload()', $content);

        @unlink($tempFile);
    }
}
