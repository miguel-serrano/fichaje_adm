<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Criteria;

enum OrderDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
