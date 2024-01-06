<?php
namespace IAnanta\UserManagement\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $table= 'roles';
	protected $guarded=[];
	public function permissions()
    {
        return $this->belongsToMany(Permission::class,'role_permissions', 'role_id', 'permission_id');
    }

}
