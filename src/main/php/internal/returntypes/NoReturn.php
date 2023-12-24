<?php
declare(strict_types=1);

namespace bovigo\callmap\internal\returntypes;

use bovigo\callmap\internal\ReturnType;

class NoReturn extends ReturnType
{
    public const VOID = 'void';
    public const NEVER = 'never';

    private function __construct(private string $keyword) { }

    public static function withVoid(): self
    {
        return new self(self::VOID);
    }

    public static function withNever(): self
    {
        return new self(self::NEVER);
    }

    public function allowsSelfReturn(): bool
    {
        return false;
    }

    public function returns(): bool
    {
        return false;
    }

    public function code(): string
    {
        return sprintf(': %s', $this->keyword);
    }
}
