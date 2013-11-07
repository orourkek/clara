Clara\Exception
===============

Various exceptions and exception handling


Overview
--------

There are (currently) **four** base exception types used in Clara:

1. ClaraException - extends \Exception
2. ClaraRuntimeException - extends \RuntimeException
3. ClaraDomainException - extends \DomainException
4. ClaraLogicException - extends \LogicException

All Clara exceptions have the `$previous` property, which can be assigned using the `setPrevious()` method. This is used to chain exceptions together such that the most information possible is available at the receiving end of the thrown exception.