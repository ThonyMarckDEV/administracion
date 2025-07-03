<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationStatePayTrue;

class SendPdfEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $pdfPath,
        public string $toEmail
    ) {}

    public function handle(): void
    {
        Mail::to($this->toEmail)->send(new NotificationStatePayTrue($this->pdfPath));
    }
}

