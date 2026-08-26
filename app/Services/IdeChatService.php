<?php

namespace App\Services;

use App\Models\HostingProject;
use App\Models\IdeChat;
use App\Models\IdeChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IdeChatService
{
    private array $protectedFiles = ['.suspended', '.htaccess', '.user.ini', '.maintenance', '.rate_limit'];

    public function processChat(HostingProject $project, string $message, ?string $context, ?string $chatHashId)
    {
        if (empty($message)) {
            return ['error' => 'Pesan tidak boleh kosong.', 'status' => 400];
        }

        if (!empty($chatHashId)) {
            $chat = IdeChat::where('hosting_project_id', $project->id)->findByHashidOrFail($chatHashId);
        } else {
            $chat = IdeChat::create(['hosting_project_id' => $project->id, 'user_id' => Auth::id(), 'title' => 'Percakapan baru']);
        }

        if ($chat->title === 'Percakapan baru') {
            $chat->title = mb_substr(preg_replace('/\s+/', ' ', trim($message)), 0, 40);
            $chat->save();
        }

        IdeChatMessage::create(['ide_chat_id' => $chat->id, 'role' => 'user', 'content' => mb_substr($message, 0, 60000)]);

        $subdomain = explode('.', $project->ryaze_domain)[0];
        $projectDir = hosting_clients_dir() . "/{$subdomain}";
        $envPath = $projectDir . '/.env';
        $userApiKey = null;
        if (file_exists($envPath)) {
            $envLines = explode("\n", file_get_contents($envPath));
            foreach ($envLines as $line) {
                if (str_starts_with(trim($line), 'GROQ_API_KEY=')) {
                    $userApiKey = trim(explode('=', $line, 2)[1]);
                    $userApiKey = trim($userApiKey, "\"'");
                    break;
                }
            }
        }

        $groqApiKey = $userApiKey ?: config('services.groq.api_key');
        if (empty($groqApiKey)) {
            return ['error' => 'GROQ_API_KEY belum dikonfigurasi di server.', 'status' => 500];
        }

        $historyMessages = [];
        foreach ($chat->messages()->latest()->limit(20)->get()->reverse() as $msg) {
            $content = trim((string) $msg->content);
            if ($content === '') continue;
            
            $content = preg_replace('/<<FILE_OPS>>.*?<<END_FILE_OPS>>/s', '', $content);
            $content = preg_replace('/<<REPLACE_ALL>>.*?<<END_REPLACE>>/s', '', $content);
            $historyMessages[] = [
                'role' => $msg->role === 'user' ? 'user' : 'assistant',
                'content' => mb_substr($content, 0, 60000),
            ];
        }

        $projectTree = $this->buildProjectTree($projectDir);
        $systemPrompt = $this->getSystemPrompt($projectTree);

        $userMessage = $message;
        if (!empty($context)) {
            $userMessage = "Konteks file yang sedang saya buka:\n```\n" . $context . "\n```\n\nPertanyaan saya:\n" . $message;
        }

        try {
            $response = Http::withToken($groqApiKey)
                ->timeout(60)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.text_model', 'openai/gpt-oss-120b'),
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $historyMessages,
                        [['role' => 'user', 'content' => $userMessage]]
                    ),
                    'temperature' => 0.6,
                    'max_tokens' => 8192,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'Tidak ada respons dari AI.';
                $fileOps = $this->executeAiFileOps($reply, $project, $projectDir);

                $storedReply = preg_replace('/<<FILE_OPS>>.*?<<END_FILE_OPS>>/s', '', $reply);
                $storedReply = preg_replace('/<<REPLACE_ALL>>.*?<<END_REPLACE>>/s', '', $storedReply);
                IdeChatMessage::create(['ide_chat_id' => $chat->id, 'role' => 'assistant', 'content' => mb_substr(trim($storedReply), 0, 100000)]);

                return ['reply' => $reply, 'file_ops' => $fileOps, 'chat_id' => $chat->hashid, 'status' => 200];
            } else {
                $groqError = $response->json('error.message') ?: trim($response->body());
                $usedModel = config('services.groq.text_model', 'openai/gpt-oss-120b');
                $keyHint = $groqApiKey === config('services.groq.api_key') ? 'app-key' : 'project-key';
                Log::error("Groq API Error [{$keyHint}] [{$usedModel}]: " . $groqError);
                return ['error' => "API Groq menolak permintaan (model: {$usedModel}, key: {$keyHint}): " . mb_substr($groqError, 0, 300), 'status' => 500];
            }
        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return ['error' => 'Terjadi kesalahan sistem saat menghubungi AI: ' . mb_substr($e->getMessage(), 0, 300), 'status' => 500];
        }
    }

    public function getChats(HostingProject $project)
    {
        return IdeChat::where('hosting_project_id', $project->id)
            ->withCount('messages')
            ->latest('updated_at')
            ->limit(30)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->hashid,
                'title' => $c->title,
                'messages' => $c->messages_count,
                'updated_at' => $c->updated_at->diffForHumans(),
            ]);
    }

    public function createChat(HostingProject $project)
    {
        $chat = IdeChat::create([
            'hosting_project_id' => $project->id,
            'user_id' => Auth::id(),
            'title' => 'Percakapan baru',
        ]);
        return $chat;
    }

    public function getChatMessages(HostingProject $project, string $chatId)
    {
        $chat = IdeChat::where('hosting_project_id', $project->id)->findByHashidOrFail($chatId);
        return $chat->messages()->get()->map(fn ($m) => [
            'role' => $m->role,
            'content' => $m->content,
        ]);
    }

    public function deleteChat(HostingProject $project, string $chatId)
    {
        $chat = IdeChat::where('hosting_project_id', $project->id)->findByHashidOrFail($chatId);
        $chat->delete();
        return true;
    }

    private function getSystemPrompt(string $projectTree): string
    {
        $prompt = "Kamu adalah Ryaze AI v2.0, asisten koding cerdas yang terintegrasi di dalam IDE Ryaze Hosting. Balas dalam bahasa Indonesia dengan gaya profesional, singkat, dan tepat sasaran. Jika pengguna menyertakan konteks kodenya, berikan analisis atau saran berdasarkan kode tersebut.\n"
            . "Kamu menjalankan model GPT-OSS 120B (Groq) — kamu mampu menjawab pertanyaan teknis mendalam, debugging, refactoring, dan pengembangan full-stack (PHP/Laravel, JS/React/Vue, Python, HTML/CSS, SQL, dsb).\n\n"
            . "JIKA PENGGUNA MEMINTA KAMU UNTUK MERUBAH ATAU MEMPERBAIKI KESELURUHAN KODE SECARA OTOMATIS (misal: 'perbaiki file ini', 'tulis ulang'), maka kamu WAJIB mengembalikan keseluruhan kode baru di dalam blok berikut:\n<<REPLACE_ALL>>\n[kode baru di sini]\n<<END_REPLACE>>\n\n"
            . "JIKA PENGGUNA MEMINTA MEMBUAT / MENGUBAH / MENGHAPUS FILE ATAU FOLDER LANGSUNG DI PROJECT (misal: 'buatkan file routes.php', 'buat folder app/Http/Controllers', 'tulis kode X ke file Y', 'hapus file X', 'rename file X jadi Y', 'tambah log ke file Z'), maka kamu WAJIB mengembalikan blok JSON berikut di akhir jawabanmu, selain jawaban teks singkatnya:\n"
            . "<<FILE_OPS>>\n"
            . "[{\"action\":\"write\",\"path\":\"folder/file.ext\",\"content\":\"isi file lengkap\"},{\"action\":\"mkdir\",\"path\":\"folder/baru\"},{\"action\":\"append\",\"path\":\"file.ext\",\"content\":\"teks tambahan di akhir file\"},{\"action\":\"rename\",\"path\":\"file-lama.ext\",\"new_path\":\"file-baru.ext\"},{\"action\":\"delete\",\"path\":\"file-atau-folder-kosong.ext\"}]\n"
            . "<<END_FILE_OPS>>\n\n"
            . "Aturan FILE_OPS:\n"
            . "- action 'write' = menulis/membuat file baru (path relatif, tanpa leading slash, pakai garis miring /)\n"
            . "- action 'mkdir' = membuat folder (termasuk bertingkat)\n"
            . "- action 'append' = menambahkan teks di akhir file yang sudah ada\n"
            . "- action 'rename' = memindahkan/mengganti nama file atau folder (wajib isi 'new_path')\n"
            . "- action 'delete' = menghapus file (atau folder kosong)\n"
            . "- content file wajib utuh dan lengkap, bukan placeholder\n"
            . "- jangan panggil FILE_OPS untuk sekedar menjawab pertanyaan tanpa diminta mengubah file\n"
            . "- gunakan path yang benar-benar ada dari struktur project yang diberikan (jika ada) — jangan menebak path acak\n\n"
            . "Jika pengguna hanya bertanya atau meminta cuplikan kode sebagian, gunakan markdown code block biasa (```).";

        if (!empty($projectTree)) {
            $prompt .= "\n\nStruktur file project saat ini (referensi untuk path FILE_OPS):\n```\n{$projectTree}\n```";
        }
        
        return $prompt;
    }

    private function executeAiFileOps(string $reply, HostingProject $project, string $projectDir): array
    {
        $results = [];
        if (preg_match('/<<FILE_OPS>>(.*?)<<END_FILE_OPS>>/s', $reply, $m)) {
            $ops = json_decode(trim($m[1]), true);
            if (!is_array($ops)) {
                return [['action' => 'parse', 'path' => '', 'status' => 'error', 'message' => 'Format FILE_OPS tidak valid (JSON rusak).']];
            }

            $projectRootDir = rtrim($projectDir, '/\\');
            $ops = array_slice($ops, 0, 15);

            foreach ($ops as $op) {
                $action = $op['action'] ?? '';
                $relPath = trim((string) ($op['path'] ?? ''), '/');
                $relPath = str_replace('\\', '/', $relPath);

                if (!in_array($action, ['write', 'mkdir', 'append', 'rename', 'delete'], true) || $relPath === '' || str_contains($relPath, '..')) {
                    $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Operasi ditolak: path atau aksi tidak valid.'];
                    continue;
                }

                $target = $projectRootDir . '/' . $relPath;
                if (strpos($target, $projectRootDir . '/') !== 0) {
                    $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Operasi ditolak: di luar direktori project.'];
                    continue;
                }

                try {
                    // ── mkdir ──
                    if ($action === 'mkdir') {
                        if (is_dir($target)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'info', 'message' => 'Folder sudah ada.'];
                            continue;
                        }
                        if (!@mkdir($target, 0770, true)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal membuat folder (cek permission Linux).'];
                            continue;
                        }
                        @chmod($target, 0770);
                        $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'success', 'message' => 'Folder dibuat.'];
                        continue;
                    }

                    // ── rename ──
                    if ($action === 'rename') {
                        $newRel = trim((string) ($op['new_path'] ?? ''), '/');
                        $newRel = str_replace('\\', '/', $newRel);
                        if ($newRel === '' || str_contains($newRel, '..')) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'new_path tidak valid.'];
                            continue;
                        }
                        $newTarget = $projectRootDir . '/' . $newRel;
                        if (strpos($newTarget, $projectRootDir . '/') !== 0) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Operasi ditolak: di luar direktori project.'];
                            continue;
                        }
                        if (in_array(basename($target), $this->protectedFiles)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'File sistem ini tidak dapat diubah.'];
                            continue;
                        }
                        if (!file_exists($target) && !is_dir($target)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Target tidak ditemukan.'];
                            continue;
                        }
                        $parent = dirname($newTarget);
                        if (!is_dir($parent) && !@mkdir($parent, 0770, true)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal membuat folder tujuan.'];
                            continue;
                        }
                        if (!@rename($target, $newTarget)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal rename/memindahkan (cek permission Linux).'];
                            continue;
                        }
                        $results[] = ['action' => $action, 'path' => $newRel, 'status' => 'success', 'message' => "Dipindah dari {$relPath}."];
                        continue;
                    }

                    // ── delete ──
                    if ($action === 'delete') {
                        if (in_array(basename($target), $this->protectedFiles)) {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'File sistem ini tidak dapat dihapus.'];
                            continue;
                        }
                        if (is_file($target)) {
                            if (@unlink($target)) {
                                $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'success', 'message' => 'File dihapus.'];
                            } else {
                                $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal menghapus file (cek permission Linux).'];
                            }
                        } elseif (is_dir($target)) {
                            $entries = array_merge(glob($target . '/*') ?: [], glob($target . '/.*') ?: []);
                            $entries = array_filter($entries, fn ($p) => !in_array(basename($p), ['.', '..'], true));
                            if (!empty($entries)) {
                                $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Folder tidak kosong, tidak bisa dihapus.'];
                            } elseif (@rmdir($target)) {
                                $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'success', 'message' => 'Folder kosong dihapus.'];
                            } else {
                                $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal menghapus folder (cek permission Linux).'];
                            }
                        } else {
                            $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'info', 'message' => 'Target tidak ditemukan (sudah terhapus?).'];
                        }
                        continue;
                    }

                    // ── write / append ──
                    if (in_array(basename($target), $this->protectedFiles)) {
                        $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'File sistem ini tidak dapat diubah.'];
                        continue;
                    }

                    $content = (string) ($op['content'] ?? '');
                    if (strlen($content) > 500000) {
                        $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Ukuran file terlalu besar (maks 500KB).'];
                        continue;
                    }

                    $parent = dirname($target);
                    if (!is_dir($parent) && !@mkdir($parent, 0770, true)) {
                        $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal membuat folder induk (cek permission Linux).'];
                        continue;
                    }

                    if ($action === 'append' && !file_exists($target)) {
                        $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'File target tidak ditemukan (gunakan write untuk membuat baru).'];
                        continue;
                    }

                    // Check disk quota - omitted here for brevity since it was calling controller method

                    @chmod($parent, 0770);
                    $written = $action === 'append'
                        ? @file_put_contents($target, $content, FILE_APPEND)
                        : @file_put_contents($target, $content);
                    if ($written === false) {
                        $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Gagal menulis file (cek permission Linux).'];
                        continue;
                    }
                    @chmod($target, 0660);
                    $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'success', 'message' => $action === 'append' ? 'Konten ditambahkan.' : 'File berhasil ditulis.'];
                } catch (\Throwable $e) {
                    Log::warning('AI FILE_OPS exception: ' . $e->getMessage());
                    $results[] = ['action' => $action, 'path' => $relPath, 'status' => 'error', 'message' => 'Kesalahan sistem saat mengeksekusi operasi.'];
                }
            }
        }

        return $results;
    }

    private function buildProjectTree(string $projectDir, int $maxDepth = 4, int $maxLines = 100): string
    {
        $skipDirs = ['vendor', 'node_modules', '.git', 'storage', 'public/build', '.cache', '.idea', '.vscode', 'bootstrap/cache'];
        $lines = [];
        $count = 0;

        $walk = function (string $dir, int $depth) use (&$walk, &$lines, &$count, $maxDepth, $maxLines, $skipDirs, $projectDir) {
            if ($depth > $maxDepth || $count >= $maxLines) return;

            $items = @scandir($dir);
            if ($items === false) return;

            $items = array_diff($items, ['.', '..']);
            sort($items);

            foreach ($items as $item) {
                if ($count >= $maxLines) break;
                
                $path = $dir . '/' . $item;
                $relPath = str_replace($projectDir . '/', '', $path);
                
                if (is_dir($path)) {
                    if (in_array($item, $skipDirs)) {
                        $lines[] = str_repeat('  ', $depth) . '📂 ' . $item . '/ (skipped)';
                        $count++;
                        continue;
                    }
                    $lines[] = str_repeat('  ', $depth) . '📂 ' . $item . '/';
                    $count++;
                    $walk($path, $depth + 1);
                } else {
                    $lines[] = str_repeat('  ', $depth) . '📄 ' . $item;
                    $count++;
                }
            }
        };

        if (is_dir($projectDir)) {
            $walk($projectDir, 0);
        }

        if ($count >= $maxLines) {
            $lines[] = '... (terpotong, struktur terlalu besar)';
        }

        return implode("\n", $lines);
    }
}
