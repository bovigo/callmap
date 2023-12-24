<?php
declare(strict_types=1);

namespace bovigo\callmap\internal\returntypes;

use bovigo\callmap\internal\ReturnType;

class StaticReturnType extends ReturnType
{
    public const KEYWORD = 'static';

    public function allowsSelfReturn(): bool
    {
        return true;
    }

    public function returns(): bool
    {
        return true;
    }

    public function code(): string
    {
        return sprintf(': %s', self::KEYWORD);
    }
}
