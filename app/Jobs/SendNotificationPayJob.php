<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationStatePay;

class SendNotificationPayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $toEmail;

    /**
     * Create a new job instance.
     */
    public function __construct(string $toEmail)
    {
        $this->toEmail = $toEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->toEmail)->send(new NotificationStatePay());
    }
}
