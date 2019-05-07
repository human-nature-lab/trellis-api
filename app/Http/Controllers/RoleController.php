<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use App\Services\PermissionService;
use App\Services\RespondentService;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;

class RoleController extends Controller {

    public function remove ($roleId) {
        $validator = Validator::make([
          'roleId' => $roleId
        ], [
          'roleId' => 'required|string|min:36|exists:role,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $role = Role::find($roleId);

        if (!$role->can_delete) {
          return response()->json([
            'msg' => 'Cannot delete this role'
          ], Response::HTTP_BAD_REQUEST);
        }

        $role->delete();

        return response()->json([
            'msg' => 'Deleted this role'
        ], Response::HTTP_NO_CONTENT);
    }

    public function create (Request $request) {
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:3'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'msg' => $validator->errors()
        ], $validator->statusCode());
      }

      $role = new Role;
      $role->id = Uuid::uuid4();
      $role->can_edit = true;
      $role->can_delete = true;
      $role->name = $request->get('name');
      $role->save();

      $role->permissions = [];

      return response()->json([
        'role' => $role
      ], Response::HTTP_CREATED);

    }

    public function all () {
      return response()->json([
        'roles' => Role::with('permissions')->get()
      ], Response::HTTP_OK);
    }

    public function copy (Request $request, PermissionService $permissionService) {

      $validator = Validator::make($request->all(), [
        'fromId' => 'required|string|exists:role,id',
        'toId' => 'required|string|exists:role,id'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'msg' => $validator->errors()
        ], $validator->statusCode());
      }

      $toRole = Role::find($request->get('toId'));

      if (!$toRole->can_edit) {
        return response()->json([
          'msg' => 'Cannot modify this role'
        ], Response::HTTP_BAD_REQUEST);
      }

      // Reset existing permissions
      DB::transaction(function () use ($request, $permissionService) {
        DB::table('role_permission')->where('role_id', $request->get('toId'))->update(['value' => false]);
        // TODO: Set "to" permissions to the "from" permissions
        $permissions = $permissionService->getRolePermissions($request->get('fromId'));
        foreach ($permissions as $rp) {
          $model = RolePermission::firstOrNew(['role_id' => $request->get('toId'), 'permission_id' => $rp->permission_id]);
          $model->value = true;
          $model->save();
        }
      });

      return response()->json([
        'role' => Role::with('permissions')->find($request->get('toId'))
      ], Response::HTTP_CREATED);

    }
}
