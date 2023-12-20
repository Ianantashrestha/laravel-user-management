<?php
namespace IAnanta\UserManagement\Models;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use IAnanta\UserManagement\Traits\UserPermissionTrait;
class Admin extends Model implements AuthenticatableContract
{
	use Authenticatable,UserPermissionTrait;
    protected $table = 'admins';
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'password',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

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


}
