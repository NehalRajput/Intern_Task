<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    public function tasks()
{
    return $this->belongsToMany(Task::class, 'intern_task');
}

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function isSuperAdmin()
    {
        return $this->userType->name === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->userType->name === 'admin';
    }

    public function isIntern()
    {
        return $this->userType->name === 'intern';
    }
}