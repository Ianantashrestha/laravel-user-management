<?php 
namespace IAnanta\UserManagement;
use Illuminate\Support\ServiceProvider;
class UserManagementServiceProvider extends ServiceProvider{
	public function boot(){
		$this->loadMigrationsFrom(__DIR__.'/database/migrations');
		$this->loadMigrationsFrom(__DIR__.'/database/seeders');
	}

	public function register(){

	}
}	