<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Une commande a été complétée')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('La commande #' . $this->order->id . ' a été marquée comme reçue par le client.')
            ->line('Cette commande est maintenant terminée et apparaîtra dans vos ventes conclues.')
            ->action('Voir la commande', route('ressourcerie.orders.show', $this->order))
            ->line('Merci de votre confiance !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'La commande #' . $this->order->id . ' a été marquée comme reçue par le client.',
        ];
    }
} 