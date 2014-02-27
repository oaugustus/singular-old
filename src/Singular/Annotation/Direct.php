<?php

namespace Singular\Annotation;

/**
 * Class Direct
 *
 * @package Singular\Silex\Framework\Annotation
 *
 * @Annotation
 * @Target("METHOD")
 */
class Direct
{
    /**
     * @var bool
     */
    public $form = false;
}