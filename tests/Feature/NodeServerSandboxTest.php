<?php

namespace Tests\Feature;

use App\Models\HostingBilling;
use App\Models\HostingProject;
use App\Models\User;
use App\Services\NodeServerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Verifikasi siklus hidup server Node klien: start (port acak, detached,
 * tulis .port/.proxy-host), status, lalu stop (bersih-bersih legacy files).
 */
class NodeServerSandboxTest extends TestCase
{
    use RefreshDatabase;

    private string $sandboxDir;

    private HostingProject $project;

    private ?int $childPid = null;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create(['role' => 'user_hosting']);
        HostingBilling::create([
            'user_id' => $owner->id,
            'plan_name' => 'Starter',
            'plan' => 'starter',
            'amount' => 50000,
            'billing_cycle' => 'monthly',
            'next_due_date' => now()->addDays(30),
            'status' => 'active',
        ]);

        $this->project = HostingProject::create([
            'user_id' => $owner->id,
            'project_name' => 'nodesrv-'.uniqid(),
            'framework' => 'node',
            'branch' => 'main',
            'ryaze_domain' => 'nodesrv-'.uniqid().'.ryaze.my.id',
            'status' => 'active',
        ]);

        $this->sandboxDir = storage_path('app/testing/nodesrv-'.uniqid());
        File::makeDirectory($this->sandboxDir, 0755, true, true);

        // Server Node tanpa dependency: echo PORT, bind 0.0.0.0.
        // Pakai .cjs — node resolve package.json terdekat yang bisa berisi
        // "type": "module" (repo platform ini pun) → .js dianggap ESM.
        File::put($this->sandboxDir.'/server.cjs', <<<'JS'
            require('http')
                .createServer((req, res) => { res.end('pong:' + process.env.PORT); })
                .listen(process.env.PORT, '0.0.0.0', () => console.log('listening'));
            JS);
    }

    protected function tearDown(): void
    {
        // Matikan sisa proses node (Windows tidak punya /proc → stop service
        // tidak menemukan pid; kill manual dari pid yang kita catat).
        if ($this->childPid) {
            if (PHP_OS_FAMILY === 'Windows') {
                exec('taskkill /F /PID '.$this->childPid.' 2>nul');
            } else {
                @posix_kill($this->childPid, 9);
            }
        }
        File::deleteDirectory($this->sandboxDir);

        parent::tearDown();
    }

    private function service(): NodeServerService
    {
        return app(NodeServerService::class);
    }

    public function test_start_writes_port_files_and_server_listens(): void
    {
        $port = $this->service()->pickFreePort();
        $this->assertNotNull($port);

        $log = [];
        $result = null;
        $started = $this->service()->start($this->project, $this->sandboxDir, 'server.cjs', $port, $log, $result);

        $this->assertTrue($started, 'start harus sukses: '.implode("\n", $log));
        $this->assertSame($port, $result['port']);
        $this->childPid = (int) $result['pid'];
        $this->assertTrue($this->service()->isPortListening('127.0.0.1', $port));

        $this->assertSame((string) $port, trim((string) file_get_contents($this->sandboxDir.'/'.NodeServerService::PORT_FILE)));
        $this->assertFileExists($this->sandboxDir.'/'.NodeServerService::PROXY_HOST_FILE);
        $this->assertFileExists($this->sandboxDir.'/'.NodeServerService::LOG_FILE);

        $this->project->refresh();
        $this->assertSame($port, $this->project->dev_port);
        $this->assertTrue((bool) $this->project->dev_mode);

        // Status harus mendeteksi server hidup.
        $statusLog = [];
        $statusResult = null;
        $this->assertTrue($this->service()->status($this->project, $this->sandboxDir, $statusLog, $statusResult));
        $this->assertTrue($statusResult['running']);
        $this->assertSame($port, $statusResult['port']);
    }

    public function test_stop_cleans_port_and_pid_files(): void
    {
        $port = $this->service()->pickFreePort();
        $log = [];
        $result = null;
        $this->assertTrue($this->service()->start($this->project, $this->sandboxDir, 'server.cjs', $port, $log, $result));
        $this->childPid = (int) $result['pid'];

        $stopLog = [];
        $stopResult = null;
        $stopped = $this->service()->stop($this->project, $this->sandboxDir, $stopLog, $stopResult);

        $this->assertFileDoesNotExist($this->sandboxDir.'/'.NodeServerService::PORT_FILE);
        $this->assertFileDoesNotExist($this->sandboxDir.'/'.NodeServerService::PID_FILE);

        $this->project->refresh();
        $this->assertNull($this->project->dev_port);
        $this->assertFalse((bool) $this->project->dev_mode);

        if (PHP_OS_FAMILY !== 'Windows') {
            $this->assertTrue($stopped, 'stop harus true di Linux (ada proses termatikan): '.implode("\n", $stopLog));
        }
    }

    public function test_start_rejects_when_no_entry_found(): void
    {
        // Directory kosong (tanpa server.js, tanpa package.json).
        $emptyDir = storage_path('app/testing/nodesrv-empty-'.uniqid());
        File::makeDirectory($emptyDir, 0755, true, true);

        $port = $this->service()->pickFreePort();
        $log = [];
        $result = null;
        $started = $this->service()->start($this->project, $emptyDir, null, $port, $log, $result);

        $this->assertFalse($started);
        $this->assertStringContainsString('Tidak ada entry point', implode("\n", $log));

        File::deleteDirectory($emptyDir);
    }

    public function test_start_accepts_entry_in_subdirectory(): void
    {
        // Entry berada di subfolder: server/start.cjs → ryaze-start server/start.cjs
        File::makeDirectory($this->sandboxDir.'/server', 0755, true, true);
        File::put($this->sandboxDir.'/server/start.cjs', <<<'JS'
            require('http')
                .createServer((req, res) => { res.end('pong:' + process.env.PORT); })
                .listen(process.env.PORT, '0.0.0.0', () => console.log('listening-subdir'));
            JS);

        $port = $this->service()->pickFreePort();
        $log = [];
        $result = null;
        $started = $this->service()->start($this->project, $this->sandboxDir, 'server/start.cjs', $port, $log, $result);

        $this->assertTrue($started, 'start subfolder harus sukses: '.implode("\n", $log));
        $this->assertSame($port, $result['port']);
        $this->childPid = (int) $result['pid'];
        $this->assertTrue($this->service()->isPortListening('127.0.0.1', $port));
    }

    public function test_start_rejects_path_escape_entry(): void
    {
        // Simulasi path escape: file di luar project tetap ditolak oleh service.
        $outsideDir = storage_path('app/testing/nodesrv-outside-'.uniqid());
        File::makeDirectory($outsideDir, 0755, true, true);
        File::put($outsideDir.'/evil.cjs', 'x');

        $log = [];
        $result = null;
        $started = $this->service()->start($this->project, $this->sandboxDir, '../nodesrv-outside-'.basename($outsideDir).'/evil.cjs', 3000, $log, $result);
        $this->assertFalse($started);
        $this->assertStringContainsString('di luar root project', implode("\n", $log));

        File::deleteDirectory($outsideDir);
    }

    public function test_prefer_npm_start_from_package_json(): void
    {
        // resolveStartCommand: package.json tanpa scripts.start tapi main
        // terisi → memakai main tersebut (bukan auto-detect).
        File::put($this->sandboxDir.'/package.json', json_encode(['main' => 'server.cjs']));

        $port = $this->service()->pickFreePort();
        $log = [];
        $result = null;
        $started = $this->service()->start($this->project, $this->sandboxDir, null, $port, $log, $result);

        $this->assertTrue($started, implode("\n", $log));
        $this->assertStringContainsString('package.json main', implode("\n", $log));
    }
}
