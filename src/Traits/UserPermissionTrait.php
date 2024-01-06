<?php 
namespace IAnanta\UserManagement\Traits;
trait UserPermissionTrait{
	protected static $allPermissions = null;
    protected static $allViewPermissions = null;

     /**
     * Get all permissions of user.
     *
     * @return mixed
     */
    public static function allPermissions()
    {
        if(self::$allPermissions == null){
            $user =\Auth::guard(config('permission.guard'))->user();
            self::$allPermissions=\Cache::rememberForever('user-permissions-'.$user->id,function() use ($user) {  
                $roles=$user->roles()->get();
                $rolesId=[];
                foreach($roles as $role){
                    $rolesId[]=$role->id;
                }
                return \DB::table('role_permissions')
                        ->join('roles', 'role_permissions.role_id', '=', 'roles.id')
                        ->whereIn('roles.id',$rolesId)
                        ->join('permissions','role_permissions.permission_id','=','permissions.id')
                        ->select('permissions.id','permissions.name','permissions.access_uri')
                        ->get();
            });
        } 
        return self::$allPermissions;
    }

     /**
     * Get all view permissions of user.
     *
     * @return mixed
     */
    public function allViewPermissions()
    {
        if(self::$allPermissions===null){
            $arrView = [];
            $allPermissionTmp =$this->allPermissions();
            $allPermissionTmp = $allPermissionTmp->pluck('access_uri')->toArray();
            if($allPermissionTmp){
                foreach($allPermissionTmp as $actionList){
                    foreach(explode(',',$actionList) as $action){
                        $arrScheme = ['https://', 'http://'];
                        $arrView[] =str_replace($arrScheme, '', url($action));
                    }
                }
            }
            self::$allViewPermissions=$arrView;
        }
        return self::$allViewPermissions;
    }

    public function checkUrlAllowAccess($url){
    	$listUrlAllowAccess = $this->allViewPermissions();
 		$arrScheme = ['https://', 'http://'];
        $pathCheck = strtolower(str_replace($arrScheme, '', $url));
        $adminUrl = strtolower(str_replace($arrScheme,'',url('/'))).'/*';
        if(in_array($adminUrl,$listUrlAllowAccess)){
        	return true;
        }
        if($listUrlAllowAccess){
            foreach($listUrlAllowAccess as $pathAllow){
                if( $pathCheck == $pathAllow ||
                    $pathCheck === $pathAllow.'/' ||
                    (\Str::endsWith($pathAllow, '{id}') && ($pathCheck === str_replace('/{id}', '', $pathAllow) || strpos($pathCheck, str_replace('{id}', '', $pathAllow)) === 0))
                )

                    return true;
            }
        }

        return false;
    }
}
