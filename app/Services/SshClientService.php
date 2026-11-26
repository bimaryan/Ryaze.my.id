<?php

namespace App\Services;

use App\Models\HostingProject;
use App\Models\HostingSshAccess;
use Illuminate\Support\Facades\Http;

/**
 * Jembatan panel -> provisioning SSH klien.
 *
 * Container PHP tidak punya docker/sudo, jadi permintaan ditulis sebagai file
 * JSON ke storage/app/ssh_jobs/pending. Daemon ssh-client-daemon (punya akses
 * docker socket) memprosesnya dan menulis hasil ke folder done/.
 */
class SshClientService
{
    public const HOST = '192.168.2.104';
    public const DOMAIN = 'ssh.ryz.my.id';

    private function queueBase(): string
    {
        $base = storage_path('app/ssh_jobs');
        foreach (['', '/pending', '/done'] as $dir) {
            $path = $base.$dir;
            if (!is_dir($path)) {
                @mkdir($path, 0777, true);
            }
            @chmod($path, 0777);
        }
        return $base;
    }

    /** Pilih IP Docker dan port host yang belum dipakai. */
    public function allocate(): array
    {
        $used = HostingSshAccess::whereNotIn('status', ['revoked'])
            ->get(['docker_ip', 'host_port']);
        $usedIps = $used->pluck('docker_ip')->all();
        $usedPorts = $used->pluck('host_port')->all();

        $ip = null;
        for ($i = 11; $i <= 250; $i++) {
            $candidate = "10.20.0.{$i}";
            if (!in_array($candidate, $usedIps, true)) {
                $ip = $candidate;
                break;
            }
        }
        if (!$ip) {
            throw new \RuntimeException('IP Docker 10.20.0.0/24 sudah penuh.');
        }

        $port = null;
        for ($attempt = 0; $attempt < 60; $attempt++) {
            $candidate = random_int(22000, 64000);
            if (in_array($candidate, $usedPorts, true)) {
                continue;
            }
            $socket = @fsockopen(self::HOST, $candidate, $errno, $errstr, 0.2);
            if ($socket) {
                fclose($socket);
                continue;
            }
            $port = $candidate;
            break;
        }
        if (!$port) {
            throw new \RuntimeException('Tidak ada port host yang tersedia.');
        }

        return ['ip' => $ip, 'port' => $port];
    }

    public function provision(HostingProject $project, string $package = 'sftp'): HostingSshAccess
    {
        $slug = explode('.', $project->ryaze_domain)[0];
        if (!preg_match('/^[a-z0-9][a-z0-9._-]*$/', $slug)) {
            throw new \RuntimeException('Nama folder klien tidak valid: '.$slug);
        }

        // Username SSH = username akun panel (bagian sebelum @ dari email).
        $client = $project->client;
        $login = strtolower(explode('@', (string) $client->email)[0]);
        $login = preg_replace('/[^a-z0-9_-]/', '-', $login) ?: 'user'.$client->id;
        $password = \Illuminate\Support\Str::random(20);

        $existing = HostingSshAccess::where('hosting_project_id', $project->id)
            ->whereIn('status', ['pending', 'active'])
            ->first();
        if ($existing) {
            return $existing;
        }

        // Baris lama yang sudah dicabut/gagal masih menyimpan slug, IP, dan port
        // yang sama. Hapus dulu supaya create() tidak kena duplicate entry.
        HostingSshAccess::where('slug', $slug)
            ->whereIn('status', ['revoked', 'failed'])
            ->delete();
        HostingSshAccess::where('hosting_project_id', $project->id)
            ->whereIn('status', ['revoked', 'failed'])
            ->delete();

        $allocation = $this->allocate();

        $access = HostingSshAccess::create([
            'hosting_project_id' => $project->id,
            'user_id' => $project->user_id,
            'slug' => $slug,
            'ssh_user' => $login,
            'package' => $package,
            'docker_ip' => $allocation['ip'],
            'host_port' => $allocation['port'],
            'status' => 'pending',
        ]);

        $result = $this->submitJob([
            'action' => 'provision',
            'slug' => $slug,
            'user' => $login,
            'password' => $password,
            'package' => $package,
            'ip' => $allocation['ip'],
            'port' => $allocation['port'],
        ]);

        if (!empty($result['success'])) {
            $hostname = 'ssh-'.$slug.'.ryz.my.id';
            $access->update([
                'status' => 'active',
                'tunnel_hostname' => $hostname,
                'ssh_user' => $login,
                'ssh_password' => $password,
                'private_key' => $result['private_key'] ?? '',
                'last_output' => $result['output'] ?? null,
                'provisioned_at' => now(),
            ]);
            $this->registerTunnelHost($hostname, $allocation['ip']);
        } else {
            $access->update([
                'status' => 'failed',
                'last_output' => $result['output'] ?? null,
            ]);
        }

        return $access;
    }

    public function revoke(HostingSshAccess $access): bool
    {
        if ($access->status === 'revoked') {
            return true;
        }

        $result = $this->submitJob([
            'action' => 'revoke',
            'slug' => $access->slug,
        ]);
        $ok = !empty($result['success']);

        if ($ok) {
            // Akses sudah dicabut: barisnya ikut dihapus supaya bisa dibuat ulang.
            $access->delete();
        } else {
            $access->update([
                'status' => 'failed',
                'ssh_password' => null,
                'last_output' => $result['output'] ?? null,
            ]);
        }

        return $ok;
    }

    /** Cabut semua akses SSH milik project (dipakai saat project dihapus). */
    public function revokeByProject(HostingProject $project): void
    {
        HostingSshAccess::where('hosting_project_id', $project->id)
            ->whereNotIn('status', ['revoked'])
            ->get()
            ->each(fn (HostingSshAccess $access) => $this->revoke($access));
    }

    public function tunnelHost(HostingSshAccess $access): string
    {
        return $access->tunnel_hostname ?: 'ssh-'.$access->slug.'.ryz.my.id';
    }

    /** Dua baris perintah untuk klien (SSH lewat Cloudflare Tunnel + Access). */
    public function command(HostingSshAccess $access): string
    {
        $host = $this->tunnelHost($access);
        $localPort = $access->host_port;

        $connect = $access->package === 'sftp'
            ? sprintf('sftp -P %d %s@127.0.0.1', $localPort, $access->ssh_user)
            : sprintf('ssh -p %d %s@127.0.0.1', $localPort, $access->ssh_user);

        return sprintf(
            "cloudflared access tcp --hostname %s --listener 127.0.0.1:%d\n%s",
            $host,
            $localPort,
            $connect
        );
    }

    /** Daftarkan DNS + ingress rule tunnel untuk hostname klien baru. */
    public function registerTunnelHost(string $hostname, string $dockerIp): void
    {
        $account = config('services.cloudflare_tunnel.account_id');
        $tunnel = config('services.cloudflare_tunnel.tunnel_id');
        $tunnelToken = config('services.cloudflare_tunnel.api_token');
        $zone = config('services.cloudflare.ryz_zone_id');
        $dnsToken = config('services.cloudflare.api_token');

        if (!$account || !$tunnel || !$tunnelToken || !$zone || !$dnsToken) {
            return;
        }

        $cfTarget = $tunnel.'.cfargotunnel.com';

        Http::withToken($dnsToken)->post(
            "https://api.cloudflare.com/client/v4/zones/{$zone}/dns_records",
            ['type' => 'CNAME', 'name' => $hostname, 'content' => $cfTarget, 'proxied' => true]
        );

        $configUrl = "https://api.cloudflare.com/client/v4/accounts/{$account}/cfd_tunnel/{$tunnel}/configurations";
        $response = Http::withToken($tunnelToken)->get($configUrl);
        $ingress = $response->json('result.config.ingress') ?: [];

        $catchAll = null;
        $rules = [];
        foreach ($ingress as $rule) {
            // Timpa rule lama untuk hostname yang sama (IP bisa berubah saat dibuat ulang).
            if (($rule['hostname'] ?? '') === $hostname || empty($rule['hostname'])) {
                if (empty($rule['hostname'])) {
                    $catchAll = $rule;
                }
                continue;
            }
            $rules[] = $rule;
        }
        $rules[] = ['hostname' => $hostname, 'service' => "tcp://{$dockerIp}:22"];
        if ($catchAll) {
            $rules[] = $catchAll;
        }

        Http::withToken($tunnelToken)->put($configUrl, ['config' => ['ingress' => array_values($rules)]]);
    }

    private function submitJob(array $payload): array
    {
        $base = $this->queueBase();
        $id = bin2hex(random_bytes(8));
        $payload['id'] = $id;

        $tmp = $base.'/pending/'.$id.'.tmp';
        file_put_contents($tmp, json_encode($payload));
        rename($tmp, $base.'/pending/'.$id.'.json');

        $resultFile = $base.'/done/'.$id.'.json';
        for ($i = 0; $i < 200; $i++) {
            if (file_exists($resultFile)) {
                // File bisa saja sedang ditulis daemon; tunggu sampai JSON valid.
                for ($try = 0; $try < 30; $try++) {
                    $data = json_decode((string) file_get_contents($resultFile), true);
                    if (is_array($data)) {
                        @unlink($resultFile);
                        return $data;
                    }
                    usleep(50_000);
                }
                @unlink($resultFile);
                return ['success' => false, 'output' => 'Hasil tidak valid.'];
            }
            usleep(100_000);
        }

        return [
            'success' => false,
            'output' => 'Timeout menunggu daemon SSH. Pastikan container ssh-client-daemon berjalan.',
        ];
    }
}
