<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkillGroup;
use App\Models\Skill;
use App\Models\TechBadge;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class SkillController extends Controller
{
    public function index()
    {
        $skillGroups = SkillGroup::with('skills')->orderBy('sort_order')->get();
        $techBadges = TechBadge::orderBy('sort_order')->get();
        return view('pages.admin.skills.index', compact('skillGroups', 'techBadges'));
    }

    // ── Skill Group CRUD ──────────────────────────────────────

    public function storeGroup(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);

        SkillGroup::create([
            'label' => $request->label,
            'icon' => $request->icon ?? 'fa-layer-group',
            'color' => $request->color ?? '#6366f1',
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('superadmin.skills.index')->with('success', 'Grup skill berhasil ditambahkan.');
    }

    public function updateGroup(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $group = SkillGroup::findOrFail($decoded[0]);

        $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);

        $group->update([
            'label' => $request->label,
            'icon' => $request->icon ?? 'fa-layer-group',
            'color' => $request->color ?? '#6366f1',
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('superadmin.skills.index')->with('success', 'Grup skill berhasil diperbarui.');
    }

    public function destroyGroup($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $group = SkillGroup::findOrFail($decoded[0]);
        $group->delete();

        return redirect()->route('superadmin.skills.index')->with('success', 'Grup skill berhasil dihapus.');
    }

    // ── Skill CRUD ────────────────────────────────────────────

    public function storeSkill(Request $request)
    {
        $request->validate([
            'skill_group_id' => 'required|exists:skill_groups,id',
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
        ]);

        Skill::create([
            'skill_group_id' => $request->skill_group_id,
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);

        return redirect()->route('superadmin.skills.index')->with('success', 'Skill berhasil ditambahkan.');
    }

    public function updateSkill(Request $request, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $skill = Skill::findOrFail($decoded[0]);

        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
        ]);

        $skill->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);

        return redirect()->route('superadmin.skills.index')->with('success', 'Skill berhasil diperbarui.');
    }

    public function destroySkill($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $skill = Skill::findOrFail($decoded[0]);
        $skill->delete();

        return redirect()->route('superadmin.skills.index')->with('success', 'Skill berhasil dihapus.');
    }

    // ── Tech Badge CRUD ───────────────────────────────────────

    public function storeBadge(Request $request)
    {
        $request->validate([
            'icon' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        TechBadge::create([
            'icon' => $request->icon,
            'label' => $request->label,
            'color' => $request->color ?? '#6366f1',
        ]);

        return redirect()->route('superadmin.skills.index')->with('success', 'Tech badge berhasil ditambahkan.');
    }

    public function destroyBadge($hashid)
    {
        $decoded = Hashids::decode($hashid);
        if (empty($decoded)) abort(404);

        $badge = TechBadge::findOrFail($decoded[0]);
        $badge->delete();

        return redirect()->route('superadmin.skills.index')->with('success', 'Tech badge berhasil dihapus.');
    }
}
