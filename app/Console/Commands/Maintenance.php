<?php

namespace App\Console\Commands;

use App\Models\Token;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Maintenance extends Command {
    protected $signature = 'trellis:maintenance {--up} {--down} {--duration-mins=} {--key=maintenance}';
    protected $description = 'Put the server into maintenance mode or take it out';

    private $file = 'MAINTENANCE';

    public function handle () {
      $path = base_path($this->file);
      if ($this->option('up')) {
        // Put the site back up
        unlink($path);
      } else {
        // Take the site down
        $data = [
          'began' => date('m/d/Y h:i:s a'),
          'key' => $this->option('key'),
        ];
        if ($this->hasOption('duration-mins')) {
          $data['duration'] = $this->option('duration-mins');
        }
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), FILE_TEXT);
        // Log everyone out
        Cache::flush();
        Token::truncate();
      }
    }

}
