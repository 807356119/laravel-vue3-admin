<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // 获取第一个角色名称
        $roleName = $this->whenLoaded('roles', function () {
            return $this->roles->first()?->name ?? null;
        });

        // 获取第一个角色ID
        $roleId = $this->whenLoaded('roles', function () {
            return $this->roles->first()?->id ?? null;
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'status_text' => $this->status === 'active' ? '正常' : '禁用',
            'role' => $roleName, // 前端需要的字段
            'role_id' => $roleId, // 编辑时需要的ID
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
