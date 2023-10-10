<?php

namespace App\Enums;

enum FailureType: string
{
    case UNPROCESSABLE_ENTITY = 'UNPROCESSABLE_ENTITY';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';
    case NOT_FOUND = 'NOT_FOUND';
    case SERVER_ERROR = 'SERVER_ERROR';

    public static function values(): array
    {
        return array_column(FailureType::cases(), 'value');
    }
}
