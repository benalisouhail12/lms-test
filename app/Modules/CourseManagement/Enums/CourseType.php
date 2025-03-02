<?php

namespace App\Modules\CourseManagement\Enums;

enum CourseType: string
{
    case ONLINE = 'ONLINE';
    case HYBRID = 'HYBRID';
    case IN_PERSON = 'IN_PERSON';

    public function label(): string
    {
        return match($this) {
            self::ONLINE => 'En ligne',
            self::HYBRID => 'Hybride',
            self::IN_PERSON => 'En personne',
        };
    }
}







