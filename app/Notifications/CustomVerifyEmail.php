<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifique seu email - SitePulse Widgets')
            ->greeting('Olá!')
            ->line('Obrigado por se registrar no **SitePulse Widgets**! 🎉')
            ->line('Para continuar usando nossa plataforma e acessar todas as funcionalidades, você precisa verificar seu endereço de email.')
            ->action('Verificar Email', $verificationUrl)
            ->line('⏰ Este link expira em 60 minutos por motivos de segurança.')
            ->line('Se você não criou uma conta no SitePulse Widgets, nenhuma ação adicional é necessária. Você pode ignorar este email com segurança.')
            ->salutation('Atenciosamente, Equipe SitePulse');
    }
}
