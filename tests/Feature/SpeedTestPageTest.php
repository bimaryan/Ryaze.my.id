<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeedTestPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_speed_test_page_renders_for_public_guest(): void
    {
        $resp = $this->get(route('speed-test.index'));

        $resp->assertOk();
        $resp->assertSee('Speed Test Internet', false);
        $resp->assertSee('Download', false);
        $resp->assertSee('Upload', false);
        $resp->assertSee('Latency', false);
        $resp->assertDontSee('logo-sidebar', false);
        $resp->assertDontSee('pjax-container', false);
        $resp->assertSee('application/ld+json', false);
        $resp->assertSee('WebPage', false);
        $resp->assertSee('BreadcrumbList', false);
        $resp->assertSee('rel="canonical"', false);
    }

    public function test_ping_endpoint_returns_json(): void
    {
        $resp = $this->getJson(route('speed-test.ping'));

        $resp->assertOk();
        $resp->assertJsonStructure(['status', 'timestamp']);
        $resp->assertJsonPath('status', 'ok');
    }

    public function test_download_endpoint_returns_binary(): void
    {
        $resp = $this->get(route('speed-test.download', ['size' => 1]));

        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'application/octet-stream');
        $this->assertGreaterThan(0, strlen($resp->getContent()));
    }

    public function test_upload_endpoint_returns_json(): void
    {
        $data = random_bytes(1024); // 1 KB

        $resp = $this->call('POST', route('speed-test.upload'), [], [], [], [
            'CONTENT_TYPE' => 'application/octet-stream',
            'CONTENT_LENGTH' => strlen($data),
        ], $data);

        $resp->assertOk();
        $resp->assertJsonStructure(['status', 'received_bytes', 'received_mb', 'timestamp']);
        $resp->assertJsonPath('status', 'ok');
    }

    public function test_download_size_validation(): void
    {
        // Test size clamp (min 1, max 100)
        $resp = $this->get(route('speed-test.download', ['size' => 200]));
        $resp->assertOk(); // Should clamp to 100

        $resp = $this->get(route('speed-test.download', ['size' => 0]));
        $resp->assertOk(); // Should clamp to 1
    }
}