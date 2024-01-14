# Changelog

## 8.0.4 (2024-01-14)

* Fixed bug with non-optional nullable parameters

## 8.0.3 (2023-12-31)

* Prevent deprecation notice when calling `get_parent_class()` in generated proxy
* Replace deprecated call of `is_callable(['parent', $someMethod])`

## 8.0.2 (2023-22-26)

* Added proper return type hints to `bovigo\callmap\FunctionProxy::returns()` and `bovigo\callmap\FunctionProxy::throws()` so the instance is further recognized as a callable.

## 8.0.1 (2023-12-25)

* Provide more details about mocked method/function in deprecation notice when method or function doesn't have a return type declaration.
* Improve support for mocking `\IteratorAggregate` by providing specific return type
information, as reflection doesn't provide a return type for `\IteratorAggregate::getIterator()`.

## 8.0.0 (2023-12-25)

### HEADS UP

* Raised minimum required PHP version to 8.2
* Deprecated support for methods and functions without a return type declaration, will be removed with 9.0.0. In case no sensible return type can be specified a "mixed" return type continues to be
supported. Starting with 9.0.0, providing a callmap to `returns()` containing methods that don't have a return type declaration will result in an `InvalidArgumentException`.

### Other changes

* Added support for return type never

## 7.0.0 (2022-12-25)

### HEADS UP

* Raised minimum required PHP version to 8.0

### Other changes

* Added support for intersection types
* Added support for combined union and intersection types

## 6.2.1 (2020-12-18)

* Replace `ReflectionParameter::getClass()` by `::getType()` (#22) PHP 8 deprecates the usage of ::getClass. So switch to an implementation using getType equal to the existing resolveUnionTypes function.

## 6.2.0 (2020-11-15)

* Ensured compatibility with PHP 8 (#14)
  * Added support for union types
  * Added support for mixed type hint
  * Added support for static return type hint
* Ensured compatibility with PHPUnit 9.2 and later

## 6.1.0 (2020-03-02)

* Verifications (`verify()->wasCalled*()`, `verify()->receivedNothing()`) now increase assertion counter of PHPUnit

## 6.0.0 (2020-02-12)

### HEADS UP

* raised minimum required PHP version to 7.3.0

### Other changes

* fixed handling of return values from PHPUnit constraints in argument verification

## 5.2.1 (2019-12-12)

* changed return type hint of `NewCallable::of()`  and `NewCallable::stub()` to `callable&\bovigo\callmap\FunctionProxy`

## 5.2.0 (2019-12-10)

* added phpstan annotations so instances created with `bovigo\callmap\NewInstance::of()` and `bovigo\callmap\NewInstance::stub()` have the proper intersection type

## 5.1.0 (2019-11-19)

* added `bovigo\callmap\ClassProxy::stub(string ...$methods)` to allow stubbing of methods
on a proxy instance that otherwise forwards method calls to the original class
* fixed bug that optional return type hints with builtin types aren't optional any more in mock instances

## 5.0.2 (2019-11-13)

* fixed bug that optional return type hints aren't optional any more in mock instances

## 5.0.1 (2019-07-25)

* prevent deprecation notice in PHP 7.4 by using proper methods

## 5.0.0 (2019-04-08)

### HEADS UP

* raised minimum required PHP version to 7.2.0
* removed `bovigo\callmap\ClassProxy::mapCalls()` and `bovigo\callmap\FunctionProxy::mapCall()`, deprecated since 3.2.0

### Other changes

* lots of code cleanups

## 4.0.1 (2018-02-11)

* fixed invalid handling of methods and functions with return type declared as `void`

## 4.0.0 (2017-09-20)

### BC breaks

* raised minimum required PHP version to 7.1.0
* ensured compatibility with PHPUnit 6.x

## 3.2.0 (2016-08-06)

### HEADS UP

* added `bovigo\callmap\ClassProxy::returns()`, `bovigo\callmap\ClassProxy::mapCalls()` is now an alias for this and should be considered deprecated
* added `bovigo\callmap\FunctionProxy::returns()`, `bovigo\callmap\FunctionProxy::mapCall()` is now an alias for this and should be considered deprecated

### Other changes

* added `bovigo\callmap\FunctionProxy::throws()`

## 3.1.1 (2016-07-29)

* removed usage of `call_user_func_array()`, instead call functions and methods directly
* added shortcut to prevent iterating over implemented interfaces and parent classes when no return type for a method can be detected
* minor doc comment fixes

## 3.1.0 (2016-07-28)

* introduced possibility to mock functions as callable
* `bovigo\callmap\throws()` now accepts all instances of `\Throwable`, not just `\Exception`

## 3.0.3 (2016-07-21)

* fixed bug that optional string arguments with default values where generated wrong in callmap proxy

## 3.0.2 (2016-07-21)

* fixed bug that return type hint `self` was not used correctly and lead to a fatal error when creating callmap instances of interfaces or classes using such a return type hint

## 3.0.1 (2016-07-11)

* switch recommendation from xp-framework/core to xp-framework/unittest

## 3.0.0 (2016-07-10)

### BC breaks

* raised minimum required PHP version to 7.0.0
* introduced scalar type hints and strict type checking
* `bovigo\callmap\NewInstance::of()` and `bovigo\callmap\NewInstance::stub()` now throw a `bovigo\callmap\ProxyCreationFailure` instead of `\ReflectionException` when creation of proxy fails

### Other changes

* added support scalar type hints

## 2.1.0 (2016-06-20)

* added support for variadic arguments, fixes #9

## 2.0.1 (2015-12-30)

* fixed bug that mapping a return value to `null` called original method instead of returning `null`

## 2.0.0 (2015-12-29)

* raised minimum required PHP version to 5.6.0
* added support for argument verification with [bovigo/assert](https://github.com/mikey179/bovigo-assert)
* added support for PHP 7 return type hints
* `bovigo\callmap\Verification` is now automatically blacklisted in PHPUnit and will not appear in PHPUnit error stacks any more

## 1.1.0 (2015-10-05)

* added support for argument verification with xp-framework/core

## 1.0.0 (2015-05-23)

* fixed conversion spec in parameter error message

## 0.6.1 (2015-04-22)

* fixed bug with optional default value null in user defined classes

## 0.6.0 (2015-04-18)

* failing argument verification now also lists the name of the argument, not just its position, fixes #2
* fixed bug with optional array parameters
* `onConsecutiveCalls()` now falls back to the default return value in case a method gets invoked more often than results are defined
* fixed #4: `onConsecutiveCalls()` should allow callables which are executed
* fixed #5: returning a callable requires another callable by providing `bovigo\callmap\wrap()`

## 0.5.0 (2015-04-12)

* added `bovigo\callmap\verify()` as possibility to verify an expected call amount as well as expected arguments
* retrieving call for a method which doesn't exist or is not applicable for mapping now throws an `\InvalidArgumentException`
* retrieving received arguments for a method which doesn't exist or is not applicable for mapping now throws an `\InvalidArgumentException`
* changed API status for `bovigo\callmap\Proxy::callsReceivedFor()` to internal, use verify()->was*() instead
* changed API status for `bovigo\callmap\Proxy::argumentsReceivedFor()` to internal, use verify()->received*() instead

## 0.4.0 (2015-04-12)

* calling `bovigo\callmap\NewInstance::*()` with a final class now throws an `\InvalidArgumentException`
* passing a method within the callmap to `bovigo\callmap\Proxy::mapCalls()` which doesn't exist or is not applicable for mapping now throws an `\InvalidArgumentException`

## 0.3.0 (2015-04-11)

* added automatic return of instance when return type hint denotes a type within the type hierarchy of the proxied class
* renamed `bovigo\callmap\Proxy::argumentsReceived()` to `bovigo\callmap\Proxy::argumentsReceivedFor()`
* fixed missing `&` for parameters that must be passed by reference

## 0.2.0 (2015-04-10)

* added possibility to force a method to throw an exception using `bovigo\callmap\throws()`
* added `bovigo\callmap\NewInstance::classname()` to retrieve the name of the generated proxy
* added `bovigo\callmap\onConsecutiveCalls()` for passing a list of invocation results, removing api status from `bovigo\callmap\InvocationResults`

## 0.1.0 (2015-04-09)

* Initial release.
