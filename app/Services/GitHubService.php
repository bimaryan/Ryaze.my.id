<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    private const CACHE_TTL = 3600;

    public function getStats(string $username): ?array
    {
        if ($username === '') {
            return null;
        }

        return Cache::remember('github_stats_' . $username, self::CACHE_TTL, function () use ($username) {
            return $this->fetchStats($username);
        });
    }

    private function fetchStats(string $username): ?array
    {
        try {
            $headers = ['Accept' => 'application/vnd.github+json', 'User-Agent' => config('app.url', 'portfolio')];

            $userResp = Http::withHeaders($headers)->timeout(8)->get("https://api.github.com/users/{$username}");
            if (!$userResp->successful()) {
                return null;
            }
            $user = $userResp->json();

            $repos = [];
            $page = 1;
            while ($page <= 3) {
                $resp = Http::withHeaders($headers)->timeout(8)
                    ->get("https://api.github.com/users/{$username}/repos", [
                        'per_page' => 100,
                        'page' => $page,
                        'sort' => 'pushed',
                    ]);
                if (!$resp->successful()) {
                    break;
                }
                $batch = $resp->json();
                $repos = array_merge($repos, $batch);
                if (count($batch) < 100) {
                    break;
                }
                $page++;
            }

            $events = Http::withHeaders($headers)->timeout(8)
                ->get("https://api.github.com/users/{$username}/events/public", ['per_page' => 100])
                ->json() ?? [];

            $totalStars = 0;
            $languageCounts = [];
            $forks = 0;
            foreach ($repos as $repo) {
                $totalStars += (int) ($repo['stargazers_count'] ?? 0);
                $forks += (int) ($repo['forks_count'] ?? 0);
                $lang = $repo['language'] ?? null;
                if ($lang) {
                    $languageCounts[$lang] = ($languageCounts[$lang] ?? 0) + 1;
                }
            }
            arsort($languageCounts);

            $topLanguages = [];
            foreach (array_slice($languageCounts, 0, 5, true) as $lang => $count) {
                $topLanguages[] = ['name' => $lang, 'repos' => $count];
            }

            $contributions = ['commits' => 0, 'prs' => 0, 'issues' => 0, 'reviews' => 0];
            foreach ($events as $event) {
                switch ($event['type'] ?? '') {
                    case 'PushEvent':
                        $contributions['commits'] += count($event['payload']['commits'] ?? []);
                        break;
                    case 'PullRequestEvent':
                        if (($event['payload']['action'] ?? '') === 'opened') {
                            $contributions['prs']++;
                        }
                        break;
                    case 'IssuesEvent':
                        if (($event['payload']['action'] ?? '') === 'opened') {
                            $contributions['issues']++;
                        }
                        break;
                    case 'PullRequestReviewEvent':
                        $contributions['reviews']++;
                        break;
                }
            }

            return [
                'username' => $username,
                'name' => $user['name'] ?? $username,
                'avatar' => $user['avatar_url'] ?? null,
                'bio' => $user['bio'] ?? null,
                'public_repos' => (int) ($user['public_repos'] ?? 0),
                'followers' => (int) ($user['followers'] ?? 0),
                'following' => (int) ($user['following'] ?? 0),
                'total_stars' => $totalStars,
                'total_forks' => $forks,
                'created_at' => $user['created_at'] ?? null,
                'top_languages' => $topLanguages,
                'contributions' => $contributions,
            ];
        } catch (\Throwable $e) {
            Log::warning('GitHub API failed for ' . $username . ': ' . $e->getMessage());
            return null;
        }
    }
}
