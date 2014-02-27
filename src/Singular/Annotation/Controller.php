<?php

namespace Singular\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Controller
{
    /**
     * @var array
     */
    public $filters = array();

    /**
     * @var string
     */
    public $mount = '';
}