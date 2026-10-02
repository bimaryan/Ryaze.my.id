<?php

namespace Tests\Feature\Hosting;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhoisPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_whois_page_renders_for_public_guest(): void
    {
        $resp = $this->get(route('whois.index'));

        $resp->assertOk();
        $resp->assertSee('window.whoisLookup', false);
        $resp->assertSee('WHOIS Domain Lookup', false);
        $resp->assertDontSee('logo-sidebar', false);
        $resp->assertDontSee('pjax-container', false);
        $resp->assertSee('application/ld+json', false);
        $resp->assertSee('WebPage', false);
        $resp->assertSee('BreadcrumbList', false);
        $resp->assertSee('rel="canonical"', false);
    }

    public function test_lookup_validates_invalid_domain_format(): void
    {
        $resp = $this->postJson(route('whois.lookup'), ['domain' => 'abc']);

        $resp->assertStatus(422);
        $resp->assertJsonPath('success', false);
        $resp->assertJsonStructure(['success', 'message']);
    }

    public function test_lookup_requires_domain_field(): void
    {
        $resp = $this->postJson(route('whois.lookup'), ['domain' => '']);

        $resp->assertStatus(422);
    }

    public function test_lookup_returns_registered_domain_data(): void
    {
        Http::fake([
            'rdap.org/*' => Http::response([
                'objectClassName' => 'domain',
                'ldhName' => 'EXAMPLE.COM',
                'status' => ['client transfer prohibited'],
                'events' => [
                    ['eventAction' => 'registration', 'eventDate' => '1997-09-15T04:00:00Z'],
                    ['eventAction' => 'expiration', 'eventDate' => '2028-09-14T04:00:00Z'],
                ],
                'entities' => [
                    [
                        'roles' => ['registrar'],
                        'vcardArray' => ['vcard', [['fn', [], 'text', 'Test Registrar Inc.']]],
                    ],
                ],
                'nameservers' => [
                    ['ldhName' => 'NS1.EXAMPLE.COM'],
                    ['ldhName' => 'NS2.EXAMPLE.COM'],
                ],
                'secureDNS' => ['delegationSigned' => true],
            ], 200),
        ]);

        $resp = $this->post(route('whois.lookup'), ['domain' => 'example.com']);

        $resp->assertOk();
        $resp->assertJson([
            'success' => true,
            'registered' => true,
            'domain' => 'EXAMPLE.COM',
            'whois' => [
                'registrar' => 'Test Registrar Inc.',
                'registered_at' => '1997-09-15T04:00:00Z',
                'expires_at' => '2028-09-14T04:00:00Z',
                'nameservers' => ['ns1.example.com', 'ns2.example.com'],
                'dnssec' => ['enabled' => true],
            ],
        ]);
    }

    public function test_lookup_reports_available_domain_on_404(): void
    {
        Http::fake([
            'rdap.org/*' => Http::response([], 404),
        ]);

        $resp = $this->post(route('whois.lookup'), ['domain' => 'available-domain-test-xyz.com']);

        $resp->assertOk();
        $resp->assertJson([
            'success' => true,
            'registered' => false,
        ]);
    }

    public function test_lookup_normalizes_url_input(): void
    {
        Http::fake([
            'rdap.org/*' => Http::response([
                'ldhName' => 'RYAZE.MY.ID',
                'events' => [],
            ], 200),
        ]);

        $resp = $this->post(route('whois.lookup'), ['domain' => 'HTTPS://WWW.Ryaze.my.id/path?x=1']);

        $resp->assertOk();
        $resp->assertJsonPath('domain', 'RYAZE.MY.ID');
        // Request ke RDAP harus memakai domain yang sudah dinormalisasi
        Http::assertSent(fn ($request) => str_contains($request->url(), 'rdap.org/domain/ryaze.my.id'));
    }
}
