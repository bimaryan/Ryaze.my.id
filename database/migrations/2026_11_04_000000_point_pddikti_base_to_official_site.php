<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const OFFICIAL_BASE = 'https://pddikti.kemdiktisaintek.go.id';

    /** Mirror komunitas yang sudah mati; nilai lama disimpan supaya down() bisa memulihkannya. */
    private ?string $previous = null;

    public function up(): void
    {
        $current = Setting::where('key', 'pddikti_api_base')->value('value');

        if ($current === null || str_contains((string) $current, 'pddikti.rone.dev') || str_contains((string) $current, 'fastapicloud.dev')) {
            $this->previous = $current;
            Setting::setVal('pddikti_api_base', self::OFFICIAL_BASE);
        }
    }

    public function down(): void
    {
        if ($this->previous !== null) {
            Setting::setVal('pddikti_api_base', $this->previous);
        }
    }
};
