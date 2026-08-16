<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Attribute;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Ai\Concerns\HasConversations;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[Fillable(['username','email', 'password','role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasConversations;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper Checkers untuk Role User
     */

     public function isSuperAdmin(): bool
     {
        return $this->role === 'super_admin';
     }
     public function isAdmin(): bool
     {
        return $this->role === 'admin';
     }
     public function isUser(): bool
     {
        return $this->role === 'user';
     }

    public function files () {
        return $this->hasMany(FileStorage::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | JWTSubject (tymon/jwt-auth)
    |--------------------------------------------------------------------------
    | Ditambahkan supaya guard "api" (driver jwt) bisa dipakai. getJWTCustomClaims()
    | menyisipkan role & username langsung ke payload token, supaya resolver
    | GraphQL bisa cek akses tanpa query ulang ke DB tiap request kalau perlu.
    | Untuk aksi sensitif (delete, dsb) tetap query ulang $user->role dari DB
    | (lihat resolver), supaya perubahan role langsung berlaku tanpa menunggu
    | token lama expired.
    */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'username' => $this->username,
        ];
    }
}