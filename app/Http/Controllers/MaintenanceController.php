<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MaintenanceController extends Controller {

  private $file = 'MAINTENANCE';

  public function checkMaintenance(Request $request) {
    $path = base_path($this->file);
    if (file_exists($path)) {
      $res = file_get_contents($path);
      $data = json_decode($res, true);
      $data['active'] = true;
      return response()->json($data);
    } else {
      return response()->json([
        'active' => false,
      ]);
    } 
  }

}
