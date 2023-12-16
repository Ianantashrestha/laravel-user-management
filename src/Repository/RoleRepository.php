<?php 

namespace Ananta\UserManagement\Repository;
use Ananta\UserManagement\Models\Role;

class RoleRepository{
	private $query;

	public function __construct(Role $query){
		$this->query = $query;
	}

	public function getRoles($params){
			$query = $this->query;
		if(!empty($params['search'])){
			 $query = $query
			 			->where('name', 'like', '%' . $params['search'] . '%');
		}
		$query = $query->orderBy('id','desc')

		if($params['paginate'] === true){
			return $query->paginate($params['limit'] ?? 10, ['*'], 'page', $params['pageNumber'] ?? 1);
		}else{
			return $query
					->get();
		}
	}

	public function storeRole(array $data){
		$role = $this
					->query
					->create([
						'name' => $data->name,
						'created_by' => \Auth::guard('admin')->user()->id
					]);
	}

	public function findRole(int $id){
		return $this
				->query
				->findOrFail($id);
	}


	public function updateRole(array $data,int $id){
		$user =  \Auth::guard('admin')->user();
		$roleData = [
			'name' => $data->name,
			'updated_by' =>$user->id
		];
		$role = $this->findRole($id);
		$role->update($roleData);
		if($data->permissions){
			$role->permissions()->detach();
			$role->permissions()->attach($role);
		}
		\Cache::forget('user-permissions'.$user->id);

		return $role;
	}


	public function deleteRole(int $id){
		$role = $this->findRole($id);
		$role->permissions()->detach();
		return $role->delete();
	}
}