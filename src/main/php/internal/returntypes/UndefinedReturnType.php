<?php
declare(strict_types=1);

namespace bovigo\callmap\internal\returntypes;

use Override;
use ReflectionClass;

/**
 * @deprecated will be removed with 9.0.0
 */
class UndefinedReturnType extends CodedReturnType
{
    public function __construct(private string $docComment)
    {
        parent::__construct('', false);
        trigger_error(
            'Support for methods and functions without a return type declaration is deprecated'
            . ' and will be removed with 9.0.0.',
            E_USER_DEPRECATED
        );
    }

    #[Override]
    public function allowsSelfReturn(ReflectionClass $class): bool
    {
        $returnType = $this->parseDocComment();
        if (null === $returnType) {
            return false;
        }

        if (in_array($returnType, ['$this', 'self', 'static'])) {
            return true;
        }

        $this->returnType = $returnType;
        return parent::allowsSelfReturn($class);
    }

    #[Override]
    public function returns(): bool
    {
        return true;
    }

    #[Override]
    public function code(): string
    {
        return '';
    }

    private function parseDocComment(): ?string
    {
        $returnPart = strstr($this->docComment, '@return');
        if (false === $returnPart) {
            return null;
        }

        $returnParts = explode(' ', trim(str_replace('@return', '', $returnPart)));
        $returnType  = ltrim(trim($returnParts[0]), '\\');
        if (empty($returnType) || strpos($returnType, '*') !== false) {
            return null;
        }

        return $returnType;
    }
}
