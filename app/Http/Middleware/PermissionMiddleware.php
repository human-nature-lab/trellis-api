<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\Request;
use Log;


class PermissionMiddleware {

  private $permissionService;

  public function __construct (PermissionService $permissionService) {
    $this->permissionService = $permissionService;
  }

  /**
   * Check that the user has all of the supplied permissions before allowing them through.
   * Ex: 'requires:EDIT_PERMISSIONS'
   * @param $request
   * @param Closure $next
   * @param PermissionService $permissionService
   * @param $permissions
   * @return \Illuminate\Http\JsonResponse|int|\Symfony\Component\HttpFoundation\Response
   */
  public function handle (Request $request, Closure $next, $permissions) {

    $permissions = explode(',', $permissions);

    $user = $request->user();

    foreach ($permissions as $permission) {
      $res = $this->permissionService->hasPermission($user, $permission);
      Log::info("Permission $permission. Result $res");
      Log::info($res);
      if (!$res) {
        return response()->json([
          'msg' => 'Unauthorized'
        ], Response::HTTP_UNAUTHORIZED);
      }
    }

    return $next($request);

  }

}
