<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

trait BelongsToSekolah
{
    protected static function bootBelongsToSekolah(): void
    {
        static::addGlobalScope('sekolah', function ($query) {
            if (Auth::check() && Auth::user()->sekolah_id) {
                $query->where((new static)->getTable() . '.sekolah_id', Auth::user()->sekolah_id);
            }
        });

        static::creating(function ($model) {
            if (! $model->sekolah_id && Auth::check()) {
                $model->sekolah_id = Auth::user()->sekolah_id;
            }
        });
    }
}
