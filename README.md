# PHP FuncMocker

**FuncMocker** – Mocking PHP functions like a... punk rocker?

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Allows you to override (i.e., mock) global functions used within a given namespace for the purposes of testing.

## Install

Via Composer

``` bash
$ composer require jeremeamia/func-mocker
```

## Usage

Assume there is a class that uses a global function (e.g., `time()`) that you'd like to mock.

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

Here is an example of a test that uses **FuncMocker** to mock `time()` to return a fixed value.

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

## Testing

``` bash
$ composer test
```

## Credits

- [Jeremy Lindblom][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jeremeamia/func-mocker.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/jeremeamia/php-func-mocker/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jeremeamia/php-func-mocker.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/jeremeamia/php-func-mocker.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jeremeamia/func-mocker.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jeremeamia/func-mocker
[link-travis]: https://travis-ci.org/jeremeamia/php-func-mocker
[link-scrutinizer]: https://scrutinizer-ci.com/g/jeremeamia/php-func-mocker/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jeremeamia/php-func-mocker
[link-downloads]: https://packagist.org/packages/jeremeamia/func-mocker
[link-author]: https://github.com/jeremeamia
[link-contributors]: ../../contributors
