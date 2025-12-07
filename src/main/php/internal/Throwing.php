<?php
declare(strict_types=1);

namespace bovigo\callmap\internal;

use Throwable;

/**
 * @since 9.1.0
 */
class Throwing
{
    public function __construct(private Throwable $t) { }

    public function __invoke(): void
    {
        throw $this->t;
    }
}