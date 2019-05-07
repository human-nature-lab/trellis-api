<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;

class PermissionService {

  /**
   * Check if a user has a specific permission.
   * @param User $user
   * @param String $permissionId
   * @return bool
   */
  public function hasPermission (User $user, String $permissionId) {
       $p = RolePermission::where('role_id', $user->role_id)->where('permission_id', $permissionId)->first();
       if (!isset($p)) {
         return false;
       } else {
         return !!$p->value;
       }
  }

  /**
   * Get all permissions for all roles
   * @return Role[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
   */
  public function getAllRolesPermissions () {
    return Role::with('permissions')->get();
  }

  /**
   * Get all of the permissions for the system
   * @return mixed
   */
  public function getAllPermissions () {
    return Permission::get();
  }

  /**
   * Get all of the permissions for a specific user.
   * @param User $user
   * @return mixed
   */
  public function getUserPermissions (User $user) {
    return RolePermission::where('role_id', $user->role_id)->get();
  }

  /**
   * Return all of the defined permissions for this role
   * @param String $roleId
   */
  public function getRolePermissions (String $roleId) {

    if ($roleId === 'admin') {
      return Permission::select('id as permission_id')->get();
    }

    return RolePermission::where('role_id', $roleId)->where('value', 1)->select('permission_id')->get();
  }


  public function setRolePermission (Role $role, String $permissionId, bool $val) {
    $rp = RolePermission::firstOrNew(['role_id' => $role->id, 'permission_id' => $permissionId]);
    $rp->value = $val;
    $rp->save();
  }

}