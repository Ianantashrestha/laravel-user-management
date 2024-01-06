<?php
namespace IAnanta\UserManagement\Models;
use Illuminate\Database\Eloquent\Model;
use IAnanta\UserManagement\Traits\PermissionRouteTrait;
class Permission extends Model
{
   use PermissionRouteTrait;
   protected $table = 'permissions';

   public function setAccessUriAttribute($value){
   		  $this->attributes['access_uri'] = implode(',',$value);
   }

    public function getNameAttribute($value)
    {
        return explode(',',$value);
    }
}
