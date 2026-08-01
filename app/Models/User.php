<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'image',
        'email_verified_at',
        'is_featured'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function userInformation()
    {
        return $this->hasOne(UserInformation::class);
    }

    public function bussinessinformation()
    {
        return $this->hasMany(BusinessInformation::class);
    }

    public function countries()
    {
        return $this->belongsTo(Country::class);
    }

    public function roleBadge()
    {
        return $this->hasOne(Badge::class, 'role_id', 'role_id');
    }

    // The paid "get a badge" status — distinct from roleBadge() above, which is
    // just the cosmetic per-role icon. This one gates forum posting (and, in a
    // future phase, listing creation) behind a one-time payment Mike's merchant
    // services confirms out-of-band, activated manually by an admin.
    public function userBadge()
    {
        return $this->hasOne(UserBadge::class);
    }

    public function hasActiveBadge(): bool
    {
        // Buyers, Admins, and Moderators never get a UserBadge row (nothing to
        // gate for them) — everyone else needs an active one.
        if (in_array($this->role, ['Buyer', 'Admin', 'Moderator'], true)) {
            return true;
        }
        return $this->userBadge?->status === 'active';
    }

    // The role icon (roleBadge) now represents "this professional has an
    // active paid badge" — never show it otherwise. Callers must eager-load
    // roleBadge/userBadge themselves; this only gates what's already loaded.
    public function activeBadgeIcon(): ?string
    {
        if ($this->userBadge?->status !== 'active') {
            return null;
        }
        return $this->roleBadge?->icon;
    }

    // Distinct from hasActiveBadge(): Buyers are exempt from the forum gate but
    // are NOT allowed to create/manage listings (Phase 2 spec) — only a
    // non-Buyer with an active paid badge may.
    public function canManageListings(): bool
    {
        return $this->role !== 'Buyer' && $this->userBadge?->status === 'active';
    }
}
