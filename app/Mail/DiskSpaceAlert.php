<?php

namespace App\Mail;

use App\Services\ConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DiskSpaceAlert extends Mailable {
  use Queueable, SerializesModels;

  public $total;
  public $free;
  public $min;
  public $site;

  public function __construct ($totalSpace, $freeSpace, $minSpace) {
    $this->total = floor($totalSpace / pow(10, 6));
    $this->free = floor($freeSpace / pow(10, 6));
    $this->min = floor($minSpace / pow(10, 6));
    $this->site = ConfigService::get('siteName') ?: 'Trellis';
  }

  public function build () {
    return $this
      ->subject('Disk space alert for ' . ConfigService::get('siteName'))
      ->from('do-not-reply@' . ConfigService::get('webRoot'), ConfigService::get('siteName'))
      ->view('emails.disk-space-alert');
  }
}