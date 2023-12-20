<?php 
namespace Ananta\UserManagement\Repository;
use Ananta\UserManagement\Models\Admin;
class AdminRepository{
	private $query;

	public function __construct(Admin $query){
		$this->query = $query;
	}

	public function getAdmin(array $params){
		$query = $this->query;
		if(!empty($params['search'])){
			 $query = $query
			 			->where('name', 'like', '%' . $params['search'] . '%')
			 			->orWhere('username','like', '%' . $params['search'] . '%')
			 			->orWhere('email','like', '%' . $params['search'] . '%');
		}

		$query = $query->orderBy('id','desc')
		if($params['paginate'] === true){
			return $query->paginate($params['limit'] ?? 10, ['*'], 'page', $params['pageNumber'] ?? 1)
		}else{
			return $query
					->get();
		}
	}

	public function getTrashedAdmin(array $params){
		$query = $this
					->query
					->onlyTrashed();

		if(!empty($params['search'])){
			 $query = $query
			 			->where('name', 'like', '%' . $params['search'] . '%')
			 			->orWhere('username','like', '%' . $params['search'] . '%')
			 			->orWhere('email','like', '%' . $params['search'] . '%');
		}

		return $query->paginate($params['limit'] ?? 10, ['*'], 'page', $params['pageNumber'] ?? 1)
	}

	public function storeAdmin(array $data){
		$userData=[
			'name'=>$data['name'],
			'username'=>$data['username'],
			'email'=>$data['email'],
			'password'=>$data['password'],
			'created_by' => \Auth::guard('admin')->user()->id
		];
		$admin = $this
					->query
					->create($data);
		if(!empty($data->role)){
			$admin->roles()->attach($role);
		}

		return $admin;
	}

	public function findAdmin(int $id){
		return $this
				->query
				->with(['roles'])
				->findOrFail($id);
	}

	public function updateAdmin(array $data,int $id){
		$userData=[
			'name'=>$data['name'],
			'username'=>$data['username'],
			'email'=>$data['email'],
			'updated_by' => \Auth::guard('admin')->user()->id,
		];
		if($data->password){
			$data['password']=$request->password;
		}
		$admin = $this->findAdmin($id);
		$admin->update($userData);
		if($data->role){
			$admin->roles()->detach();
			$admin->roles()->attach($role);
		}

		\Cache::forget('user-permissions'.$user->id);

		return $admin;
	}

	public function deleteAdmin(int $id){
		$admin = $this->findAdmin($id);
		$admin->update([
			'deleted_by' =>  \Auth::guard('admin')->user()->id
		]);
		return $admin->delete();
	}

	public function deleteAdminForever(int $id){
		$admin =$this->findAdmin($id);
		return $admin->forceDelete();
	}

	public function restoreAdmin(int $id){
		$admin = $this
					->query
					->withTrashed()
					->findOrFail($id);

		return $admin->restore();
	}



}