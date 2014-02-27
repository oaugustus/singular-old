<?php

namespace Singular\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Route
{
    /**
     * @var string
     */
    public $pattern = null;

    /**
     * @var bool
     */
    //public $patternOverride = false;

    /**
     * @var string
     */
    //public $template = null;

    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $method = null;

    /**
     * @var array<string>
     */
    public $methods;
}