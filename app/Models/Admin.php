<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    protected $guarded = [];

    public function role()
    { 
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    { 
        return $this->belongsToMany(Permission::class, 'role_permissions', 'admin_id', 'permission_id');
    }

    public function hasPermission($permission)
    { 
        if ($this->isSuperAdmin()) { 
            return true; 
        } 

        return $this->permissions()->where('permission', $permission)->exists();
    }

    public function isAdmin()
    { 
        return $this->role && $this->role->name === 'super_admin';
    }

    public function isSuperAdmin()
    {
        return $this->role && $this->role->name === 'super_admin';
    }

    /**
     * Get messages received by the admin
     */
    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'receiver');
    }

    /**
     * Get messages sent by the admin
     */
    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }
}