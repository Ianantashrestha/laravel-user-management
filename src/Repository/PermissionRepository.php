<?php 
namespace Ananta\UserManagement\Repository;
use Ananta\UserManagement\Models\Permission;
class PermissionRepository{
	private $query;

	public function __construct(Permission $query){
		$this->query = $query;
	}

	public function getPermissions($params){
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

	public function storePermission(array $data){
		return $this
				->query
				->create([
					"name" => $data->name,
					'access_uri' => $data->access_uri,
					'created_by' =>  \Auth::guard('admin')->user()->id
				]);
	}


	public function findPermission(int $id){
		return $this
				->query
				->findOrFail($id);
	}


	public function updatePermission(array $data,int $id){
		$user =  \Auth::guard('admin')->user();
		\Cache::forget('user-permissions'.$user->id);
		return $this
				->query
				->where('id',$id)
				->update(
					[
						"name" => $data->name,
						'access_uri' => $data->access_uri,
						'updated_by' =>  \Auth::guard('admin')->user()->id
					]
				);
	}


	public function deletePermission(int $id){
		return $this->
				->query
				->where('id',$id)
				->delete();
	}

}