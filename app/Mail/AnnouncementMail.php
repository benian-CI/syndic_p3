<?php

namespace App\Mail;

use App\Models\Announcement;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use SerializesModels;

    public Announcement $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function build()
    {
        return $this->subject($this->announcement->title)
            ->view('emails.announcement')
            ->with(['announcement' => $this->announcement]);
    }
}
