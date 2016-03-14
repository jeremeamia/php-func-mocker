<?php

namespace FuncMocker;

/**
 * Provides the ability to override (or mock) a global function within a given namespace to exhibit a given behavior.
 *
 * The class is meant to be a companion library to PHPUnit.
 */
class Mocker
{
    const TEMPLATE = 'namespace %s{function %s(){return\FuncMocker\Mocker::call(__FUNCTION__,func_get_args());}}';

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
     */
    public static function mock($name, $namespace, \Closure $behavior = null)
    {
        // Registers our custom stream wrapper if it hasn't been already.
        if (!self::$streamWrapperReady) {
            self::$streamWrapperReady = (bool) stream_wrapper_register(Stream::PROTOCOL, Stream::class);
        }

        // If no behavior is provided, it is set as a no-op.
        $behavior = $behavior ?: self::noop();

        // Prepares the fully-qualified function name (FQFN).
        $namespace = trim($namespace, '\\');
        $fqfn = $namespace . '\\' . $name;

        // Ensures that the function does not already exist.
        if (function_exists($fqfn)) {
            throw new \RuntimeException("Function {$fqfn} already exists.");
        }

        // Stores the function in the function registry by it's fully-qualified name.
        self::$functionRegistry[$fqfn] = $behavior;

        // Dynamically creates and includes a function using code template.
        // Note: This is using stream wrappers in a clever way to avoid eval()'ing
        //       in an inappropriate context or having to write to a temp file.
        include Stream::PROTOCOL . '://' . sprintf(self::TEMPLATE, $namespace, $name);
    }

    /**
     * Execute a registered function by it's fully-qualified name.
     *
     * @param string $fqfn Fully-qualified function name to execute.
     * @param array $args Arguments to execute the function with.
     * @return mixed The function's return value
     * @internal Not to be used outside of the library.
     */
    public static function call($fqfn, array $args)
    {
        $fn = self::$functionRegistry[$fqfn];
        return $fn(...$args);
    }

    /**
     * Used to retrieve a default value for the mocked functions behavior.
     *
     * It returns a no-op (no operation) function.
     *
     * @return \Closure
     */
    private static function noop()
    {
        return function () {
            // NO-OP
        };
    }
}
