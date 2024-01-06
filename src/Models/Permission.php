<?php
namespace IAnanta\UserManagement\Models;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
   protected $table = 'permissions';

   public function setAccessUriAttribute($value){
   		  $this->attributes['access_uri'] = implode(',',$value);
   }

    public function getNameAttribute($value)
    {
        return explode(',',$value);
    }
}
