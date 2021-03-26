<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use App\Library\RestValidator;
use App\Services\SnapshotService;
use Validator;

class AppServiceProvider extends ServiceProvider {
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    $this->app->bind('SnapshotService', function () {
      return new SnapshotService();
    });
  }

  public function boot() {
    Validator::resolver(function ($translator, $data, $rules, $messages) {
      return new RestValidator($translator, $data, $rules, $messages);
    });
    if (env('APP_LOG_QUERIES', 0)) {
      DB::listen(function ($query) {
        Log::debug(json_encode([
          $query->sql,
          $query->bindings,
          $query->time
        ]));
      });
    }
    Queue::looping(function () {
      while (DB::transactionLevel() > 0) {
        DB::rollBack();
      }
    });
  }
}
