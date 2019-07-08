<?php

namespace WecarSwoole\Middleware;

final class Next
{
    private $callable;
    private $next;

    public function __construct($callable, Next $next = null)
    {
        $this->callable = $callable;
        $this->next = $next;
    }

    public function addNext(Next $next)
    {
        $this->next = $next;
    }

    public function __invoke(...$params)
    {
        if (!$this->callable) {
            return null;
        }

        return call_user_func($this->callable, $this->next, ...$params);
    }
}
