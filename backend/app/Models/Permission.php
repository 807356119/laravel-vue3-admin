<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'sort',
    ];

    /**
     * 权限关联的角色
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * 父权限
     */
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    /**
     * 子权限
     */
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id')->orderBy('sort');
    }
}
