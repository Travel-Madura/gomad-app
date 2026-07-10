<?php

namespace App\Enums;

enum RentalBookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case ON_GOING = 'on_going';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::CONFIRMED => 'Terkonfirmasi',
            self::PAID => 'Sudah Dibayar',
            self::CANCELLED => 'Dibatalkan',
            self::COMPLETED => 'Selesai',
            self::ON_GOING => 'Sedang Berjalan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::PAID => 'green',
            self::CANCELLED => 'red',
            self::COMPLETED => 'gray',
            self::ON_GOING => 'indigo',
        };
    }
}