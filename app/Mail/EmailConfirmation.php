<?php

namespace App\Mail;

use App\Services\ConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable {
  use Queueable, SerializesModels;

  /**
   * The order instance.
   *
   * @var Order
   */
  public $username;
  public $link;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($key, $username) {
    $this->link = 'https://' . ConfigService::get('webRoot') . '/#/email-confirmation/' . urlencode($key);
    $this->username = $username;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build() {
    return $this
      ->from('do-not-reply@' . ConfigService::get('webRoot'), ConfigService::get('siteName'))
      ->view('emails.confirmation');
  }
}