<?php
namespace IAnanta\UserManagement\Models;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use IAnanta\UserManagement\Traits\UserPermissionTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Admin extends Model implements AuthenticatableContract,JWTSubject
{
	use Authenticatable,UserPermissionTrait;
    protected $table = 'admins';
    protected $guarded = [];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class,'admin_roles', 'admin_id', 'role_id');
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }    

}