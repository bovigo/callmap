0.5.0 (2015-??-??)
------------------

  * added `bovigo\callmap\verify()` as possibility to verify an expected call amount as well as expected arguments
  * retrieving call for a method which doesn't exist or is not applicable for mapping now throws an `\InvalidArgumentException`
  * retrieving received arguments for a method which doesn't exist or is not applicable for mapping now throws an `\InvalidArgumentException`


0.4.0 (2015-04-12)
------------------

  * calling `bovigo\callmap\NewInstance::*()` with a final class now throws an `\InvalidArgumentException`
  * passing a method within the callmap to `bovigo\callmap\Proxy::mapCalls()` which doesn't exist or is not applicable for mapping now throws an `\InvalidArgumentException`


0.3.0 (2015-04-11)
------------------

  * added automatic return of instance when return type hint denotes a type within the type hierarchy of the proxied class
  * renamed `bovigo\callmap\Proxy::argumentsReceived()` to `bovigo\callmap\Proxy::argumentsReceivedFor()`
  * fixed missing `&` for parameters that must be passed by reference


0.2.0 (2015-04-10)
------------------

  * added possibility to force a method to throw an exception using `bovigo\callmap\throws()`
  * added `bovigo\callmap\NewInstance::classname()` to retrieve the name of the generated proxy
  * added `bovigo\callmap\onConsecutiveCalls()` for passing a list of invocation results, removing api status from `bovigo\callmap\InvocationResults`


0.1.0 (2015-04-09)
------------------

  * Initial release.
