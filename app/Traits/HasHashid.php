<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashid
{
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
