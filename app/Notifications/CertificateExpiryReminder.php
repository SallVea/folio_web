<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateExpiryReminder extends Notification
{
    use Queueable;

    public function __construct(public Certificate $certificate)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];

        if (config('services.sms.enabled') && ! empty($notifiable->phone)) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = now()->startOfDay()->diffInDays($this->certificate->expiry_date->startOfDay(), false);

        return (new MailMessage())
            ->subject('Sertifikat "'.$this->certificate->title.'" akan kadaluarsa')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Sertifikat "'.$this->certificate->title.'" dari '.($this->certificate->issuer ?: 'penyedia').' akan kadaluarsa dalam '.max($days, 0).' hari.')
            ->line('Tanggal kadaluarsa: '.$this->certificate->expiry_date->format('d M Y'))
            ->action('Kelola Sertifikat', route('certificates.index'))
            ->line('Perbarui sertifikatmu agar profil publik tetap relevan.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'certificate_expiry',
            'title'       => 'Sertifikat akan kadaluarsa',
            'message'     => '"'.$this->certificate->title.'" akan kadaluarsa pada '.$this->certificate->expiry_date->format('d M Y').'.',
            'certificate_id' => $this->certificate->id,
            'url'         => route('certificates.index'),
        ];
    }

    public function toSms(object $notifiable): string
    {
        return 'Folio: Sertifikat "'.$this->certificate->title.'" akan kadaluarsa pada '
            .$this->certificate->expiry_date->format('d M Y').'. Perbarui di dashboard.';
    }
}
