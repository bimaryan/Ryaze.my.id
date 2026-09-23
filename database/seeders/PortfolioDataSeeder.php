<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\SkillGroup;
use App\Models\Skill;
use App\Models\TechBadge;
use Illuminate\Database\Seeder;

class PortfolioDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Educations ─────────────────────────────────────
        Education::create([
            'period' => '2022 — Sekarang',
            'degree' => 'S1 Teknik Informatika',
            'institution' => 'Universitas Bina Darma',
            'location' => 'Palembang, Sumatera Selatan',
            'description' => 'Fokus pada pengembangan perangkat lunak, algoritma, basis data, dan rekayasa sistem. Aktif dalam pengembangan project nyata dan kompetisi pemrograman.',
            'icon' => 'fa-graduation-cap',
            'color' => '#6366f1',
            'tags' => ['Informatika', 'Programming', 'Software Engineering'],
            'sort_order' => 1,
        ]);

        Education::create([
            'period' => '2019 — 2022',
            'degree' => 'SMK — Rekayasa Perangkat Lunak',
            'institution' => 'SMKN 1 Muara Beliti',
            'location' => 'Musi Rawas, Sumatera Selatan',
            'description' => 'Belajar dasar-dasar pemrograman, web development, dan basis data. Mengerjakan berbagai project praktik industri sebagai bagian dari kurikulum RPL.',
            'icon' => 'fa-school',
            'color' => '#8b5cf6',
            'tags' => ['RPL', 'Web Development', 'Database'],
            'sort_order' => 2,
        ]);

        // ── Experiences ────────────────────────────────────
        Experience::create([
            'period' => '2024 — Sekarang',
            'role' => 'Founder & Full-Stack Developer',
            'company' => 'Ryaze.my.id',
            'type' => 'Full-time',
            'location' => 'Remote',
            'description' => 'Membangun platform SaaS multi-layanan dari nol — shared hosting Indonesia, jasa pembuatan website, dan layanan APK builder. Menangani seluruh stack: infrastruktur server, backend Laravel, frontend Inertia/React, dan sistem deployment otomatis.',
            'icon' => 'fa-rocket',
            'color' => '#6366f1',
            'tags' => ['Laravel', 'React', 'Node.js', 'Docker', 'Linux', 'SaaS'],
            'sort_order' => 1,
        ]);

        Experience::create([
            'period' => '2023 — 2024',
            'role' => 'Freelance Web Developer',
            'company' => 'Independent',
            'type' => 'Freelance',
            'location' => 'Remote',
            'description' => 'Mengerjakan berbagai project website dan aplikasi web untuk klien dari berbagai sektor — sistem informasi sekolah, toko online, landing page bisnis, hingga dashboard admin kustom.',
            'icon' => 'fa-laptop-code',
            'color' => '#8b5cf6',
            'tags' => ['PHP', 'Laravel', 'MySQL', 'Bootstrap', 'JavaScript'],
            'sort_order' => 2,
        ]);

        Experience::create([
            'period' => '2022',
            'role' => 'Praktik Kerja Industri (PKL)',
            'company' => 'Dinas Kominfo',
            'type' => 'Internship',
            'location' => 'Musi Rawas',
            'description' => 'Praktik kerja industri di instansi pemerintah. Membantu pengembangan dan maintenance sistem informasi internal, serta dokumentasi teknis.',
            'icon' => 'fa-building-columns',
            'color' => '#0ea5e9',
            'tags' => ['PHP', 'HTML', 'CSS', 'MySQL'],
            'sort_order' => 3,
        ]);

        // ── Skill Groups & Skills ──────────────────────────
        $backend = SkillGroup::create(['label' => 'Backend', 'icon' => 'fa-server', 'color' => '#6366f1', 'sort_order' => 1]);
        Skill::create(['skill_group_id' => $backend->id, 'name' => 'Laravel / PHP', 'percentage' => 92, 'sort_order' => 1]);
        Skill::create(['skill_group_id' => $backend->id, 'name' => 'Node.js / Express', 'percentage' => 80, 'sort_order' => 2]);
        Skill::create(['skill_group_id' => $backend->id, 'name' => 'REST API Design', 'percentage' => 88, 'sort_order' => 3]);
        Skill::create(['skill_group_id' => $backend->id, 'name' => 'Database (MySQL, SQLite, PostgreSQL)', 'percentage' => 85, 'sort_order' => 4]);

        $frontend = SkillGroup::create(['label' => 'Frontend', 'icon' => 'fa-display', 'color' => '#8b5cf6', 'sort_order' => 2]);
        Skill::create(['skill_group_id' => $frontend->id, 'name' => 'React / Inertia.js', 'percentage' => 82, 'sort_order' => 1]);
        Skill::create(['skill_group_id' => $frontend->id, 'name' => 'HTML & CSS (Tailwind)', 'percentage' => 90, 'sort_order' => 2]);
        Skill::create(['skill_group_id' => $frontend->id, 'name' => 'JavaScript / Alpine.js', 'percentage' => 85, 'sort_order' => 3]);
        Skill::create(['skill_group_id' => $frontend->id, 'name' => 'Next.js', 'percentage' => 70, 'sort_order' => 4]);

        $devops = SkillGroup::create(['label' => 'DevOps & Tools', 'icon' => 'fa-gears', 'color' => '#0ea5e9', 'sort_order' => 3]);
        Skill::create(['skill_group_id' => $devops->id, 'name' => 'Linux Server Admin', 'percentage' => 78, 'sort_order' => 1]);
        Skill::create(['skill_group_id' => $devops->id, 'name' => 'Docker', 'percentage' => 72, 'sort_order' => 2]);
        Skill::create(['skill_group_id' => $devops->id, 'name' => 'Git & GitHub', 'percentage' => 92, 'sort_order' => 3]);
        Skill::create(['skill_group_id' => $devops->id, 'name' => 'Nginx / Caddy', 'percentage' => 75, 'sort_order' => 4]);

        // ── Tech Badges ────────────────────────────────────
        $badges = [
            ['icon' => 'fa-brands fa-laravel', 'label' => 'Laravel', 'color' => '#ef4444'],
            ['icon' => 'fa-brands fa-react', 'label' => 'React', 'color' => '#22d3ee'],
            ['icon' => 'fa-brands fa-node-js', 'label' => 'Node.js', 'color' => '#22c55e'],
            ['icon' => 'fa-brands fa-python', 'label' => 'Python', 'color' => '#eab308'],
            ['icon' => 'fa-brands fa-vuejs', 'label' => 'Vue.js', 'color' => '#4ade80'],
            ['icon' => 'fa-brands fa-docker', 'label' => 'Docker', 'color' => '#3b82f6'],
            ['icon' => 'fa-brands fa-git-alt', 'label' => 'Git', 'color' => '#f97316'],
            ['icon' => 'fa-brands fa-html5', 'label' => 'HTML5', 'color' => '#f97316'],
            ['icon' => 'fa-brands fa-css3-alt', 'label' => 'CSS3', 'color' => '#6366f1'],
            ['icon' => 'fa-brands fa-js', 'label' => 'JavaScript', 'color' => '#eab308'],
            ['icon' => 'fa-solid fa-database', 'label' => 'MySQL', 'color' => '#0ea5e9'],
            ['icon' => 'fa-brands fa-linux', 'label' => 'Linux', 'color' => '#f8fafc'],
        ];

        foreach ($badges as $i => $badge) {
            TechBadge::create(array_merge($badge, ['sort_order' => $i + 1]));
        }
    }
}
