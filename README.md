# PHP FuncMocker

**FuncMocker** – Mocking PHP functions like a... punk rocker?

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

Allows you to overwrite (i.e., mock) global functions used within a given namespace for the purposes of testing.

There are _two_ main use cases I developed this for:

1. When you are testing objects that call non-deterministic functions like `time()` and `rand()`, and you need these
   functions to return deterministic values for the sake of the test.
2. When you are working with code where you have objects that must make calls to functions. This is pretty common in
   legacy codebases that were not previously object-oriented in nature. For example, if you're project has a function
   called `db_add()` that you end up using in an object in your model layer, you might want to "mock" that function when
   you are testing so you don't actually make calls to the database in your unit tests.

## Install

Via Composer

``` bash
$ composer require jeremeamia/func-mocker
```

## Usage

Let's you have a `RandomNumberGenerator` class in the namespace, `My\App`, that calls the global function `rand()`.
You could overwrite the usage of `rand()` for that particular namespace by using **FuncMocker**.

```php
use My\App\RandomNumberGenerator;
use FuncMocker\Mocker;

Mocker::mock('rand', 'My\App', function () {
    return 5;
});

$rng = new RandomNumberGenerator(1, 10);
echo $rng->getNumber();
//> 5
echo $rng->getNumber();
//> 5
echo $rng->getNumber();
//> 5
```

### Longer Example

Assume there is a class that uses the global function (e.g., `time()`) that you'd like to mock.

``` php
<?php

namespace My\Crypto;

use Psr\Http\Message\RequestInterface as Request;

class Signer
{
    // ...

    public function getStringToSign(Request $request)
    {
        return $request->getMethod() . "\n"
            . time() . "\n"
            . $request->getHeader('X-API-Operation')[0] . "\n"
            . $request->getBody();            
    }
    
    // ...
}
```

Here is an example of a PHPUnit test that uses **FuncMocker** to mock `time()` to return a fixed value, making it much
easier to write a test.

```php
<?php

namespace My\App\Tests;

use FuncMocker\Mocker as FuncMocker;
use My\Crypto\Signer;
use Psr\Http\Message\RequestInterface as Request;

class SignerTest extends \PHPUnit_Framework_TestCase
{
    // ...

    public function testCanGetStringToSign()
    {
        // Mock the request with PHPUnit
        $request = $this->getMock(Request::class);
        $request->method('getMethod')->returnValue('POST');
        $request->method('getHeader')->returnValue(['CREATE_THING']);
        $request->method('getBody')->returnValue('PARAMS');
        
        // Mock the call to PHP's time() function to give us a deterministic value.
        FuncMocker::mock('time', 'My\Crypto', function () {
            return 12345;
        });
                
        $signer = new Signer();
        
        // Check to see that the string to sign is constructed how we would expect.
        $this->assertEquals(
            "POST\n12345\nCREATE_THING\nPARAMS",
            $signer->getStringToSign()
        );
    }
    
    // ...
}

```

### Disabling and Re-Enabling

FuncMocker also lets you disable mocks you've setup, in case you need the function to behave normally in your some of
your tests.

```php
$func = FuncMocker::mock('time()', 'My\App', function () {
    return 1234567890;
});

echo My\App\time();
//> 1234567890

$func->disable();
echo My\App\time();
//> 1458018866

$func->enable();
echo My\App\time();
// > 1234567890
```

## Testing

``` bash
$ composer test
```

## Credits

- [Jeremy Lindblom][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jeremeamia/func-mocker.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/jeremeamia/php-func-mocker/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jeremeamia/func-mocker.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jeremeamia/func-mocker
[link-travis]: https://travis-ci.org/jeremeamia/php-func-mocker
[link-scrutinizer]: https://scrutinizer-ci.com/g/jeremeamia/php-func-mocker/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jeremeamia/php-func-mocker
[link-downloads]: https://packagist.org/packages/jeremeamia/func-mocker
[link-author]: https://github.com/jeremeamia
[link-contributors]: ../../contributors
