<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;

use ParseError;
use ReflectionClass;
use ReflectionFunction;
/**
 * Allows to create new callable instances of given function.
 */
class NewCallable
{
    /**
     * map of already evaluated functions
     *
     * @var array<string,ReflectionClass<callable&FunctionProxy>>
     */
    private static array $functions = [];

    /**
     * returns a new callable instance of the given function
     *
     * @api
     * @param  string function function to create a new callable of
     * @return callable&FunctionProxy
     */
    public static function of(string $function): FunctionProxy
    {
        return self::callMapClass($function)->newInstanceArgs([$function]);
    }

    /**
     * returns a new callable instance of the given function
     *
     * @api
     * @param  string $function function to create a new callable of
     * @return callable&FunctionProxy
     */
    public static function stub(string $function): FunctionProxy
    {
        $proxy = self::of($function);
        $proxy->preventParentCalls();
        return $proxy;
    }

    /**
     * returns the class name of any new instance for given function
     *
     * @api
     */
    public static function classname(string $function): string
    {
        return self::callMapClass($function)->getName();
    }

    /**
     * returns the proxy class for given function
     *
     * @param   string  $function
     * @return  ReflectionClass<callable&FunctionProxy>
     */
    private static function callMapClass(string $function): ReflectionClass
    {
        if (!isset(self::$functions[$function])) {
            self::$functions[$function] = self::forkCallMapClass(
                new ReflectionFunction($function)
            );
        }

        return self::$functions[$function];
    }

    /**
     * reference to compile function
     *
     * @var  callable
     * @internal
     */
    public static $compile = __NAMESPACE__ . '\compile';

    /**
     * creates a new class from the given function which uses the CallMap trait
     *
     * @param   ReflectionFunction  $function
     * @return  ReflectionClass<callable&FunctionProxy>
     * @throws  ProxyCreationFailure
     */
    private static function forkCallMapClass(
        ReflectionFunction $function
    ): ReflectionClass {
        try {
            $compile = self::$compile;
            $compile(self::createProxyCode($function));
        } catch (ParseError $pe) {
            throw new ProxyCreationFailure(
                'Failure while creating callable CallMap instance of '
                . $function->getName() . '(): ' . $pe->getMessage(),
                $pe
            );
        }

        /** @var  class-string<FunctionProxy&callable> $functionProxy */
        $functionProxy = $function->getName() . 'CallMapProxy';
        /** @var  ReflectionClass<FunctionProxy&callable> $refClass */
        $refClass = new ReflectionClass($functionProxy);
        return $refClass;
    }

    /**
     * creates code for new class
     */
    private static function createProxyCode(ReflectionFunction $function): string
    {
        $param = paramsOf($function);
        $return = true;
        $returnType = determineReturnTypeOf($function);
        if (in_array($returnType, [': void', ': never'])) {
            $return = false;
        }

        $code  = sprintf(
            "class %sCallMapProxy extends \bovigo\callmap\FunctionProxy{\n"
            . "    protected array \$paramNames = %s;\n"
            . "    public function __invoke(%s)%s {\n"
            . "        %s\$this->handleFunctionCall(func_get_args());\n"
            . "    }\n"
            . "    protected bool \$returnVoid = %s;"
            . "}\n",
            ucfirst($function->getShortName()),
            var_export($param['names'], true),
            $param['string'],
            $returnType,
            $return ? 'return ' : '',
            $return ? 'false' : 'true'
        );
        if ($function->inNamespace()) {
            return sprintf(
                "namespace %s {\n%s}\n",
                $function->getNamespaceName(),
                $code
            );
        }

        return $code;
    }
}
