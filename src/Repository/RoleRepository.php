<?php 

namespace IAnanta\UserManagement\Repository;
use IAnanta\UserManagement\Models\Role;

class RoleRepository{
	private $query;

	public function __construct(Role $query){
		$this->query = $query;
	}

	public function getRoles($params = []){
			$query = $this->query;
		if(!empty($params['search'])){
			 $query = $query
			 			->where('name', 'like', '%' . $params['search'] . '%');
		}
		$query = $query->orderBy('id','desc');

		if(isset($params['paginate']) && $params['paginate'] === true){
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
						'name' => $data['name'],
						'created_by' => \Auth::guard(config('permission.guard'))->user()->id
					]);
		if(isset($data['permissions']))
			$role->permissions()->attach($data['permissions']);

		return $data;

	}

	public function findRole(int $id){
		return $this
				->query
				->findOrFail($id);
	}


	public function updateRole(array $data,int $id){
		$user =  \Auth::guard(config('permission.guard'))->user();
		$roleData = [
			'name' => $data['name'],
			'updated_by' =>$user->id
		];
		$role = $this->findRole($id);
		$role->update($roleData);
		if(isset($data['permissions'])){
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