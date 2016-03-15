<?php

namespace FuncMocker;

/**
 * Encapsulates a function that has been mocked by FuncMocker.
 */
class Func
{
    /** @var callable The original function that is being mocked. */
    private $originalFn;

    /** @var \Closure The function that should be used instead of the original (i.e., the new behavior). */
    private $mockedFn;

    /** @var bool Whether or not the function should use the mocked behavior or original. */
    private $enabled = true;

    /**
     * @param callable $original The original function being mocked.
     * @param callable|null $mocked The new behavior for the mocked function. Leave null for no-op.
     */
    public function __construct(callable $original, callable $mocked = null)
    {
        $this->originalFn = $original;
        $this->mockedFn = $mocked ?: function () {
            /* NO-OP */
        };
    }

    /**
     * Enables the new mocked behavior.
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disables the mocked behavior so that the original behavior is used.
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Makes the Func object callable and executes the mocked function's behavior.
     *
     * @param array $args The arguments to apply to the function.
     * @return mixed The result of the function
     */
    public function __invoke(array $args)
    {
        $callable = $this->enabled ? $this->mockedFn : $this->originalFn;

        return $callable(...$args);
    }
}
