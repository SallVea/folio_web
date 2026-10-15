<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Notifications\CertificateExpiryReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendCertificateReminders extends Command
{
    protected $signature = 'certificates:remind';
    protected $description = 'Kirim pengingat sertifikat yang mendekati kadaluarsa (email + notifikasi aplikasi)';

    public function handle(): int
    {
        $certificates = Certificate::with('user')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->where(function ($query) {
                $query->whereNull('reminder_sent_at')
                    ->orWhere('reminder_sent_at', '<', now()->subDays(7));
            })
            ->get();

        $sent = 0;

        foreach ($certificates as $certificate) {
            if (! $certificate->user?->email) {
                continue;
            }

            Notification::send($certificate->user, new CertificateExpiryReminder($certificate));
            $certificate->forceFill(['reminder_sent_at' => now()])->save();
            $sent++;
        }

        $this->info($sent.' pengingat sertifikat dikirim.');

        return self::SUCCESS;
    }
}
