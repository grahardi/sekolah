<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomRolePermission extends Model
{
    protected $table = 'custom_role_permissions';
    protected $fillable = ['custom_role_id', 'modul_key', 'boleh_akses', 'read_only'];
    protected $casts = ['boleh_akses' => 'boolean', 'read_only' => 'boolean'];

    public function role()
    {
        return $this->belongsTo(CustomRole::class, 'custom_role_id');
    }
}
