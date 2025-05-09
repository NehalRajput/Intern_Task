<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    /** @use HasFactory<\Database\Factories\RolePermssionFactory> */
    use HasFactory;
    protected $guarded = [];
    protected $table = 'role_permissions';

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}