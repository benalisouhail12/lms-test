<?php

namespace App\Modules\CourseManagement\Enums;
enum EnrollmentStatus: string
{
    case PENDING = 'PENDING';
    case ACTIVE = 'ACTIVE';
    case COMPLETED = 'COMPLETED';
    case DROPPED = 'DROPPED';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::ACTIVE => 'Actif',
            self::COMPLETED => 'Terminé',
            self::DROPPED => 'Abandonné',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::ACTIVE => 'blue',
            self::COMPLETED => 'green',
            self::DROPPED => 'red',
        };
    }
}
