<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Redefinir Senha - SitePulse Widgets')
            ->greeting('Olá!')
            ->line('Você está recebendo este email porque recebemos uma solicitação de redefinição de senha para sua conta no **SitePulse Widgets**.')
            ->action('Redefinir Senha', $url)
            ->line('⏰ Este link expira em 60 minutos por motivos de segurança.')
            ->line('🔒 **Dica de Segurança:** Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária. Sua conta permanece segura.')
            ->line('Após redefinir sua senha, você poderá acessar sua conta normalmente e continuar usando todas as funcionalidades do SitePulse Widgets.')
            ->salutation('Atenciosamente, Equipe SitePulse');
    }
}
