# PHP FuncMocker

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

FuncMocker – Mocking PHP functions like a... punk rocker?

## Install

Via Composer

``` bash
$ composer require jeremeamia/func-mocker
```

## Usage

``` php
<?php

namespace Foo
{
    class Bar
    {
        public static function baz()
        {
            return time();
        }
    }
}

namespace
{
    require 'Mocker.php';
    require 'Stream.php';

    FuncMocker\Mocker::mock('time', 'Foo', function () {
        return 123456789;
    });

    assert(function_exists('Foo\time'));
    assert(123456789 === Foo\Bar::baz());
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
