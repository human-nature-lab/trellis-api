<?php

namespace App\Console\Commands;


use App\Mail\DiskSpaceAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckDiskSpace extends Command {

    protected $signature = 'trellis:check:disk-space {--email=* : emails to send an alert to} {--min= : the minimum amount of free disk space to trigger an email in MB}';

    protected $description = 'Check the amount of disk space available on the server and send an email if there is not a lot of free space.';

    public function handle () {

      $totalSpace = disk_total_space('/');
      $freeSpace = disk_free_space('/');
      $this->info("Total space: $totalSpace, Free space: $freeSpace");

      $minSpace = $this->option('min') ? $this->option('min') * pow(10, 6) : $totalSpace * 0.1;
      $this->info("Minimum space $minSpace");
      $emails = $this->option('email');

      if ($freeSpace <= $minSpace) {
        foreach ($emails as $email) {
          $this->info("Emailing disk space alert to $email");
          Mail::to($email)->send(new DiskSpaceAlert($totalSpace, $freeSpace, $minSpace));
        }
      }

    }

}
