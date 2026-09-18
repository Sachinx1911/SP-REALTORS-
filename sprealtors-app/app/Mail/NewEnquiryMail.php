<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry) {}

    public function build(): self
    {
        $this->subject('New enquiry: '.($this->enquiry->name ?: 'Website visitor'))
            ->view('emails.new-enquiry');

        // So hitting "Reply" in the inbox goes straight to the visitor, not to no-reply@.
        if ($this->enquiry->email) {
            $this->replyTo($this->enquiry->email, $this->enquiry->name);
        }

        return $this;
    }
}
