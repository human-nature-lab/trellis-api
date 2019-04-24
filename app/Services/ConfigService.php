<?php

namespace app\Services;

use App\Models\ConditionTag;
use App\Models\Config;
use Ramsey\Uuid\Uuid;
use DB;

class ConfigService {

    public static function all () {
        return Config::all();
    }

    public static function get ($key) {
        $c = Config::find($key);
        return $c->value;
    }

    public static function reset ($key) {
        $c = Config::find($key);
        $c->value = $c->default_value;
        $c->save();
        return $c;
    }

    public static function set ($key, $val) {
        $c = Config::firstOrNew(['key' => $key]);
        $c->value = $val;
        $c->save();
        return $c;
    }
}
