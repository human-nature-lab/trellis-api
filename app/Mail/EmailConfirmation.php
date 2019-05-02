<?php

namespace App\Mail;

use App\Services\ConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable {
  use Queueable, SerializesModels;

  public $name;
  public $link;

  public function __construct($key, $name) {
    $this->link = 'https://' . ConfigService::get('webRoot') . '/#/email-confirmation/' . urlencode($key);
    $this->name = $name;
  }

  public function build() {
    return $this
      ->subject('Confirm Email for ' . ConfigService::get('siteName'))
      ->from('do-not-reply@' . ConfigService::get('webRoot'), ConfigService::get('siteName'))
      ->view('emails.confirmation');
  }
}