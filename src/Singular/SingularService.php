<?php

namespace Singular;

use Singular\Application;

class SingularService
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}