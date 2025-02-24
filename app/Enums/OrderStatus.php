<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';           // Commande initiale
    case PROCESSING = 'processing';     // En cours de traitement par la ressourcerie
    case READY = 'ready';              // Prête à être récupérée/livrée
    case DELIVERED = 'delivered';       // Livrée/Disponible pour récupération
    case COMPLETED = 'completed';       // Confirmée reçue par le client
    case CANCELLED = 'cancelled';       // Annulée

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::PROCESSING => 'En traitement',
            self::READY => 'Prête',
            self::DELIVERED => 'Livrée',
            self::COMPLETED => 'Terminée',
            self::CANCELLED => 'Annulée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::READY => 'indigo',
            self::DELIVERED => 'purple',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::PENDING => in_array($newStatus, [self::PROCESSING, self::CANCELLED]),
            self::PROCESSING => in_array($newStatus, [self::READY, self::CANCELLED]),
            self::READY => in_array($newStatus, [self::DELIVERED, self::CANCELLED]),
            self::DELIVERED => in_array($newStatus, [self::COMPLETED, self::CANCELLED]),
            self::COMPLETED => false, // État final
            self::CANCELLED => false, // État final
        };
    }

    public static function availableForRessourcerie(): array
    {
        return [
            self::PENDING,
            self::PROCESSING,
            self::READY,
            self::DELIVERED,
            self::CANCELLED,
        ];
    }

    public static function availableForClient(): array
    {
        return [
            self::DELIVERED,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }
} 