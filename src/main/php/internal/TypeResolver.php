<?php
declare(strict_types=1);

namespace bovigo\callmap\internal;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

class TypeResolver
{
    /**
     * Resolves union and intersection types so that any generaded code is compatible signature wise.
     *
     * @internal
     * @since   6.2.0
     * @template T of object
     * @param ReflectionUnionType|ReflectionIntersectionType $unionType
     * @param ReflectionClass<T>|null $containingClass
     * @return string
     */
    protected static function resolveCombinedTypes(
        ReflectionUnionType|ReflectionIntersectionType $unionType,
        ?ReflectionClass $containingClass = null
    ): string {
        $types = [];
        foreach ($unionType->getTypes() as $type) {
            if ($type instanceof ReflectionNamedType) {
                $type = $type->getName();
                $types[] = self::resolveType($type, $containingClass);
            } else {
                $types[] = '(' . self::resolveCombinedTypes($type, $containingClass) . ')';
            }
        }

        return join(
            $unionType instanceof ReflectionUnionType ? '|' : '&',
            $types
        );
    }

    /**
     * Converts type string to proper formatted type.
     *
     * @internal
     * @since   6.2.0
     * @template T of object
     * @param string $type
     * @param ReflectionClass<T>|null $containingClass
     * @return string
     */
    protected static function resolveType(string $type, ?ReflectionClass $containingClass): string
    {
        if ('self' === $type && null !== $containingClass) {
            return '\\' . $containingClass->getName();
        } elseif (class_exists($type) || interface_exists($type)) {
            return '\\' . $type;
        }

        return $type;
    }

}
