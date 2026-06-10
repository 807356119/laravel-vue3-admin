<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'account_name',
        'account_number',
        'password',
        'bank_name',
        'website',
        'description',
        'images',
        'extra_fields',
        'category',
        'is_favorite',
        'expires_at',
    ];

    protected $casts = [
        'images' => 'array',
        'extra_fields' => 'array',
        'is_favorite' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * 账户类型
     */
    const TYPE_BANK = 'bank';
    const TYPE_EMAIL = 'email';
    const TYPE_SOCIAL = 'social';
    const TYPE_WEBSITE = 'website';
    const TYPE_OTHER = 'other';

    /**
     * 所属用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 设置密码（加密）
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    /**
     * 获取密码（解密）
     */
    public function getDecryptedPasswordAttribute()
    {
        if ($this->password) {
            try {
                return Crypt::decryptString($this->password);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 获取类型文本
     */
    public function getTypeTextAttribute()
    {
        $types = [
            self::TYPE_BANK => '银行卡',
            self::TYPE_EMAIL => '邮箱',
            self::TYPE_SOCIAL => '社交账号',
            self::TYPE_WEBSITE => '网站',
            self::TYPE_OTHER => '其他',
        ];

        return $types[$this->type] ?? '其他';
    }

    /**
     * 是否已过期
     */
    public function getIsExpiredAttribute()
    {
        if ($this->expires_at) {
            return $this->expires_at->isPast();
        }
        return false;
    }
}
