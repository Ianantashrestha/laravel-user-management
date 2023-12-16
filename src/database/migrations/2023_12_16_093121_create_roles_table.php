<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->unique();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->index(['role_id', 'permission_id']);
        });

         Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('admin_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->index(['role_id','admin_id']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('admin_roles');
        

    }
};
