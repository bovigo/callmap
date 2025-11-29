<?php
declare(strict_types=1);

namespace bovigo\callmap\internal\returntypes;

use Override;
use ReflectionClass;

/**
 * @deprecated will be removed with 10.0.0
 */
class UndefinedReturnType extends CodedReturnType
{
    public function __construct(
        private string $docComment,
        ?ReflectionClass $containingClass = null
    ) {
        parent::__construct('', false, $containingClass);
    }

    #[Override]
    public function allowsSelfReturn(): bool
    {
        $returnType = $this->parseDocComment();
        if (null === $returnType) {
            return false;
        }

        if (in_array($returnType, ['$this', 'self', 'static'])) {
            return true;
        }

        $this->returnType = $returnType;
        return parent::allowsSelfReturn();
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
