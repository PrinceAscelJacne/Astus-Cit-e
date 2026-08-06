<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Session;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use  softDeletes, HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function sessions()
    {
        return $this->hasMany(\App\Models\Session::class, 'user_id');
    }

    /**
     * Le rôle le plus élevé : accès complet à l'administration.
     */
    public function isBoss(): bool
    {
        return (int) $this->role_id === Role::BOSS;
    }

    /**
     * Chef de département : périmètre limité à son propre département.
     */
    public function isChefDepartement(): bool
    {
        return (int) $this->role_id === Role::CHEF_DEPARTEMENT;
    }

    public function isEmploye(): bool
    {
        return (int) $this->role_id === Role::EMPLOYE;
    }

    /**
     * Les rôles autorisés à inscrire de nouveaux utilisateurs, et les rôles
     * qu'ils peuvent attribuer. Un chef de département ne crée que des employés,
     * et uniquement dans son propre département.
     *
     * @return array<int, int>
     */
    public function rolesAttribuables(): array
    {
        if ($this->isBoss()) {
            return [Role::EMPLOYE, Role::CHEF_DEPARTEMENT, Role::BOSS];
        }

        if ($this->isChefDepartement()) {
            return [Role::EMPLOYE];
        }

        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone',
        'email',
        'password',
        'department_id',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
}
