<?php

namespace Singular\Provider;


use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Classe PackServiceProvider, implementa a estrutura básica de um pacote.
 *
 * @package Singular\Provider
 */
class PackServiceProvider implements ServiceProviderInterface
{
    protected $pack = '';

    public function register(Application $app)
    {
    }

    public function boot(Application $app)
    {

    }

    /**
     * Retorna o shortname do pacote.
     *
     * @return string
     */
    public function getPackName()
    {
        return $this->pack;
    }

    /**
     * Retorna o namespace do pacote.
     *
     * @return string
     */
    public function getNameSpace()
    {
        $reflection = new \ReflectionClass(get_class($this));

        return $reflection->getNamespaceName();
    }
} 