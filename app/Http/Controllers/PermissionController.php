<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use App\Services\PermissionService;
use App\Services\RespondentService;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class PermissionController extends Controller {

    public function rolePermissions (PermissionService $permissionService, $roleId) {
        $validator = Validator::make([
          'roleId' => $roleId
        ], [
          'roleId' => 'required|string|min:3|exists:role,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $permissions = $permissionService->getRolePermissions($roleId);

        return response()->json([
            'permissions' => $permissions
        ], Response::HTTP_OK);
    }

    public function all (PermissionService $permissionService) {
      return response()->json([
        'roles' => $permissionService->getAllRolesPermissions(),
        'all' => $permissionService->getAllPermissions()
      ], Response::HTTP_OK);
    }


    public function updateRolePermission (Request $request, $roleId, $permissionId) {
      $validator = Validator::make([
        'value' => $request->get('value'),
        'roleId' => $roleId,
        'permissionId' => $permissionId
      ], [
        'value' => 'required|boolean',
        'roleId' => 'required|string|exists:role,id',
        'permissionId' => 'required|string|exists:permission,id'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'msg' => $validator->errors()
        ], $validator->statusCode());
      }

      $rolePermission = RolePermission::firstOrNew([
        'role_id' => $roleId,
        'permission_id' => $permissionId
      ]);

      $rolePermission->value = $request->get('value');
      $rolePermission->save();

      return response()->json([
        'rolePermission' => $rolePermission
      ], Response::HTTP_CREATED);

    }

}
