<?php

class Route
{
    private $method = '';
    private $resolver;
    private $callback;
    private $isRegex;

    public function __construct($resolver, $method, $callback, $isRegex = false)
    {
        $this->resolver = $resolver;
        $this->method = $method;
        $this->callback = $callback;
        $this->isRegex = $isRegex;
    }

    public function matches($uri, $method)
    {
        if ($method != $this->method) {
            return false;
        }

        if ($this->isRegex) {
            return preg_match($this->resolver, $uri) == 1;
        } else {
            return $uri == $this->resolver;
        }
    }

    public function execute()
    {
        call_user_func_array($this->callback, []);
    }
}