<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder {
  public function run () {

    DB::transaction(function () {

      $defaults = [
        'serverMode' => 'PROD',
        'debug' => false,
        'appMode' => 'PROD',
        'siteName' => 'Trellis Demo',
        'webRoot' => 'trellisdemo.net',
        'formBuilderUrl' => '/form-builder/index.html#/form/{form_id}/builder?token={token}&study={study}&locale={locale}&apiRoot={apiRoot}',   // The url template to use for the form builder iframe
        'mapTileLayer.url' => 'https://api.mapbox.com/styles/v1/mapbox/streets-v10/tiles/256/{z}/{x}/{y}?access_token={accessToken}', // The url template to use for the mapbox layer
        'mapTileLayer.attribution' => 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        'mapTileLayer.maxZoom' => 20,
        'mapTileLayer.id' => 'mapbox.emerald',
        'mapTileLayer.accessToken' => '',                               // The access token required for maps
        'mapTileLayer.style' => 'mapbox://styles/mapbox/streets-v10',
        'logging.console' => true,
        'logging.rate' => 0,
        'logging.levels' => '[]',
        'database.logging.levels' => '["error"]',
        'logging.max' => 100,
        'sentry.dsn' => '',                             // The sentry DSN for this server
        'sentry.offline' => true,                       // Use our custom, offline Transport
        'sentry.onlineIntervalRate' => 3 * 60 * 1000,   // Milliseconds
        'demo.expirationTime' => 24 * 60 * 2,           // Minutes
        'demo.defaultRole' => 'supervisor'
      ];

      $private = ['mapTileLayer.accessToken', 'demo.expirationTime'];

      $objectList = ["logging.levels", "database.logging.levels"];

      foreach ($defaults as $key => $value) {
        $c = DB::table('config')->where('key', $key)->first();

        // If the config already exists then we only update the default value
        if (isset($c)) {
          $vals = [
            'default_value' => $value === '' ? null : $value,
            'is_public' => !in_array($key, $private, true)
          ];
        } else {
          $vals = [
            'key' => $key,
            'value' => $value === '' ? null : $value,
            'default_value' => $value === '' ? null : $value,
            'is_public' => !in_array($key, $private, true)
          ];
        }

        if (array_key_exists($value, $objectList)) {
          $vals['type'] = 'object';
        } else if (is_string($value) ) {
          $vals['type'] = 'string';
        } else if (is_bool($value)) {
          $vals['type'] = 'boolean';
        } else if (is_int($value)) {
          $vals['type'] = 'integer';
        } else if (is_float($value)) {
          $vals['type'] = 'float';
        }

        if (isset($c)) {
          DB::table('config')->where('key', $key)->update($vals);
        } else {
          Log::info("Adding new configuration $key to database");
          DB::table('config')->insert($vals);
        }
      }

    });

  }
}
