<?php

namespace Singular\Annotation;

/**
 * Class Service
 *
 * @package Singular\Silex\Framework\Annotation
 *
 * @Annotation
 */
class Service
{
    /**
     * @var string
     */
    public $definition = 'shared';

    /**
     * @var string
     */
    public $type = 'shared';

}