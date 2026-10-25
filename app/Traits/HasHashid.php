<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashid
{
    /**
     * Laravel memanggil initialize{Trait} saat model dibuat.
     * $appends akan bentrok fatal dengan properti $appends milik Eloquent
     * Model jika dideklarasikan langsung di trait, jadi di-push per instance.
     */
    public function initializeHasHashid(): void
    {
        if (!in_array('hashid', $this->appends)) {
            $this->appends[] = 'hashid';
        }
    }

    /**
     * Alias Hash ID untuk URL yang elegan
     *
     * @return string
     */
    public function getHashidAttribute()
    {
        return Hashids::encode($this->id);
    }

    /**
     * Scope untuk mencari model berdasarkan hashid
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $hashid
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeFindByHashidOrFail($query, $hashid)
    {
        $decoded = Hashids::decode($hashid);
        
        if (empty($decoded)) {
            abort(404, 'Data tidak ditemukan.');
        }

        return $query->findOrFail($decoded[0]);
    }
}
