<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\SocialAccount;

class User extends Authenticatable
{
    protected $fillable = ['username', 'email', 'phone', 'password', 'full_name', 'role', 'status', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function avatarSrc(): ?string
    {
        if (! $this->avatar_url) {
            return null;
        }
        if (str_starts_with($this->avatar_url, 'storage/avatars/')) {
            return route('customer.avatar', ['v' => substr(sha1($this->avatar_url), 0, 12)]);
        }
        return asset($this->avatar_url);
    }

    public function displayName(): string
    {
        return $this->full_name ?: $this->username;
    }
}
