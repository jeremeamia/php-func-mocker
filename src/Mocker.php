<?php

namespace FuncMocker;

/**
 * Provides the ability to override (or mock) a global function within a given namespace to exhibit a given behavior.
 *
 * The class is meant to be a companion library to PHPUnit.
 */
class Mocker
{
    const TEMPLATE = 'namespace %s{function %s(){$f=\FuncMocker\Mocker::get(__FUNCTION__);return$f(func_get_args());}}';

    /** @var array A registry of functions that are referenced by their fully-qualified name. */
    private static $functionRegistry = [];

    /** @var bool Records whether or not the custom stream wrapper has been registered. */
    private static $streamWrapperReady = false;

    /**
     * Overrides (mocks) the given global function in the given namespace to exhibit the given behavior.
     *
     * This is a useful technique for mocking the usage of global functions with a namespaced object. This allows you to
     * write unit tests for objects that must call functions that you don't want your code to execute in the context of
     * the test.
     *
     * @param string $name The name of the function being mocked.
     * @param string $namespace The namespace the function should be mocked in.
     * @param \Closure|null $behavior The behavior of the mocked function. Leave null for no-op.
     * @return Func
     */
    public static function mock($name, $namespace, \Closure $behavior = null)
    {
        // Registers our custom stream wrapper if it hasn't been already.
        if (!self::$streamWrapperReady) {
            self::$streamWrapperReady = (bool) stream_wrapper_register(Stream::PROTOCOL, Stream::class);
        }

        // Determine the fully-qualified function name.
        $namespace = trim($namespace, '\\');
        $fqfn = $namespace . '\\' . $name;

        // Ensure that the function has not already been created.
        if (function_exists($fqfn)) {
            throw new \RuntimeException(
                'The function cannot be mocked into the given namespace, because it already exists.'
            );
        }

        // Store a representation of the function in the registry by it's fully-qualified name.
        self::$functionRegistry[$fqfn] = new Func($name, $behavior);

        // Dynamically creates and includes a function using code template.
        // Note: This is using stream wrappers in a clever way to avoid eval()'ing
        //       in an inappropriate context or having to write to a temp file.
        include Stream::PROTOCOL . '://' . sprintf(self::TEMPLATE, $namespace, $name);

        return self::$functionRegistry[$fqfn];
    }

    /**
     * Fetches a previously registered function by it's fully-qualified name.
     *
     * @param string $fqfn Fully-qualified function name to fetch.
     * @return Func
     */
    public static function get($fqfn)
    {
        if (!isset(self::$functionRegistry[$fqfn])) {
            throw new \RuntimeException("The {$fqfn} function has not been registered with FuncMocker.");
        }

        return self::$functionRegistry[$fqfn];
    }
}
