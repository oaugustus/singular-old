<?php

namespace Singular;

class SingularController
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}