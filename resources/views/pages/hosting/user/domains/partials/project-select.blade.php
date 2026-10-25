{{--
    Select project yang dipakai bersama oleh kedua form subdomain
    (dari domain sendiri & subdomain gratis platform).
--}}
<div>
    <label for="subdomain_project{{ $idSuffix ?? '' }}" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
        Sambungkan ke Project <span class="text-slate-400 font-normal">(opsional)</span>
    </label>
    <select name="project_id" id="subdomain_project{{ $idSuffix ?? '' }}"
            class="ui-field ui-field-sm">
        <option value="">Nanti dulu</option>
        @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->project_name }} ({{ $p->ryaze_domain }})</option>
        @endforeach
    </select>
    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
        Kalau diisi, subdomain langsung dilayani. Kalau kosong, subdomain tetap disimpan dan bisa disambungkan nanti.
    </p>
    @error('project_id')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>