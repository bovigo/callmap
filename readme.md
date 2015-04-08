bovigo/callmap
==============

Allows to stub and mock method calls by applying a callmap.

Build status
------------

[![Build Status](https://secure.travis-ci.org/mikey179/bovigo-callmap.png)](http://travis-ci.org/mikey179/bovigo-callmap) [![Coverage Status](https://coveralls.io/repos/mikey179/bovigo-callmap/badge.png?branch=master)](https://coveralls.io/r/mikey179/bovigo-callmap?branch=master)


Installation
------------

tbd


Usage
-----

Explore the [tests](https://github.com/mikey179/bovigo-callmap/tree/master/src/test/php)
to see how bovigo/callmap can be used. Please be aware that the array provided
with the `mapCalls()` method should contain all methods that should be stubbed.

In case you need to need to stub a series of return values for the same method
have a look at [InvocationResults](https://github.com/mikey179/bovigo-callmap/blob/master/src/test/php/InvocationResultsTest.php).

When retrieving the arguments that were passed in a method call please be aware
that the each method has its own invocation count (whereas in PHPUnit the
invocation count is for the whole mock object). Also, invocation count starts at
1 for the first invocation, not at 0.

