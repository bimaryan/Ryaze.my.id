<?php

namespace App\Services;

use App\Models\HostingDomain;
use App\Models\HostingDomainRedirect;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Menulis aturan redirect ke file yang dibaca nginx.
 *
 * Format file `.redirects/<host>`:
 *     <kode> <url tujuan>      contoh: "308 https://toko-baru.com"
 *
 * nginx membacanya lewat rewrite_by_lua_block di
 * conf/conf.d/hosting_clients.conf. Karena kode ditulis literal di file,
 * nginx bisa mengirim status persis seperti yang dipilih user.
 */
class DomainRedirectService
{
    private function dir(): string
    {
        return hosting_clients_dir().'/.redirects';
    }

    /**
     * Simpan (atau ganti) aturan redirect untuk satu domain.
     */
    public function save(HostingDomain $domain, string $target, int $statusCode): HostingDomainRedirect
    {
        $redirect = HostingDomainRedirect::updateOrCreate(
            ['hosting_domain_id' => $domain->id],
            ['target' => $target, 'status_code' => $statusCode],
        );

        $this->writeFiles($domain, $redirect);

        return $redirect;
    }

    public function delete(HostingDomain $domain): void
    {
        HostingDomainRedirect::where('hosting_domain_id', $domain->id)->delete();
        $this->removeFiles($domain);
    }

    /** Tulis ulang file untuk semua hostname milik domain ini. */
    public function writeFiles(HostingDomain $domain, HostingDomainRedirect $redirect): void
    {
        $dir = $this->ensureDir();
        $line = $redirect->status_code.' '.$redirect->target;

        foreach ($domain->hostnames() as $hostname) {
            @file_put_contents("{$dir}/{$hostname}", $line);
        }
    }

    public function removeFiles(HostingDomain $domain): void
    {
        $dir = $this->dir();

        foreach ($domain->hostnames() as $hostname) {
            if (file_exists("{$dir}/{$hostname}")) {
                @unlink("{$dir}/{$hostname}");
            }
        }
    }

    /**
     * Buat ulang semua file dari database.
     * Dipakai command `domain:rebuild-redirects` supaya tidak perlu menulis
     * satu per satu kalau file .redirects hilang atau server di-restart.
     */
    public function rebuildAll(): int
    {
        $this->ensureDir();
        $count = 0;

        $domains = HostingDomain::with('redirect')->get();

        foreach ($domains as $domain) {
            $redirect = $domain->redirect;
            if (! $redirect) {
                continue;
            }
            $this->writeFiles($domain, $redirect);
            $count++;
        }

        return $count;
    }

    private function ensureDir(): string
    {
        $dir = $this->dir();

        if (! file_exists($dir)) {
            mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }

        return $dir;
    }
}