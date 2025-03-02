<?php

namespace App\Modules\CourseManagement\Enums;
enum ProgressStatus: string
{
    case NOT_STARTED = 'NOT_STARTED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Non commencé',
            self::IN_PROGRESS => 'En cours',
            self::COMPLETED => 'Terminé',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NOT_STARTED => 'gray',
            self::IN_PROGRESS => 'blue',
            self::COMPLETED => 'green',
        };
    }

    public function percentage(): int
    {
        return match($this) {
            self::NOT_STARTED => 0,
            self::IN_PROGRESS => 50,
            self::COMPLETED => 100,
        };
    }
}
