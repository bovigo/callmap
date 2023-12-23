<?php
declare(strict_types=1);

namespace bovigo\callmap;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class ReturnType
{
    public function __construct(private string $typeName) { }

    /**
     * detects return type of method
     *
     * It will make use of reflection to detect the return type. In case this
     * does not yield a result the doc comment will be parsed for the return
     * annotation.
     */
    public static function detect(ReflectionMethod $method): ?self
    {
        $returnType = $method->getReturnType();
        if (null !== $returnType) {
            return self::detectFromDeclaration($returnType);
        }

        $docComment = $method->getDocComment();
        if (false === $docComment) {
            return null;
        }

        return self::detectFromDocComment($docComment);
    }

    public function represents(ReflectionClass $class): bool
    {
        return $class->getName() === $this->typeName
            || $class->getShortName() === $this->typeName;
    }

    public function isSelf(): bool
    {
        return in_array($this->typeName, ['$this', 'self', 'static']);
    }

    private static function detectFromDeclaration(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType $returnType
    ): ?self {
        if ($returnType->allowsNull()) {
            return null;
        }

        if (
            $returnType instanceof ReflectionUnionType
            || $returnType instanceof ReflectionIntersectionType
        ) {
            return new self((string) $returnType);
        }

        return new self($returnType->getName());
    }

    private static function detectFromDocComment(string $docComment): ?self
    {
        $returnPart = strstr($docComment, '@return');
        if (false === $returnPart) {
            return null;
        }

        $returnParts = explode(' ', trim(str_replace('@return', '', $returnPart)));
        $returnType  = ltrim(trim($returnParts[0]), '\\');
        if (empty($returnType) || strpos($returnType, '*') !== false) {
            return null;
        }

        return new self($returnType);
    }
}
