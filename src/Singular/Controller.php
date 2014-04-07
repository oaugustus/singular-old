<?php

namespace Singular;

/**
 * Class Controller.
 *
 * @package Singular
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
class Controller
{
    /**
     * Nome do pacote a que o controlador pertence.
     *
     * @var string
     */
    protected $pack = '';

    /**
     * @param Application $app
     * @param String      $pack
     */
    public function __construct(Application $app, $pack)
    {
        $this->app = $app;
        $this->pack = $pack;
    }

    /**
     * Retorna o nome do serviço local do controlador.
     *
     * @return string
     */
    protected function getServiceName()
    {
        $reflector = new \ReflectionClass($this);

        return strtolower($reflector->getShortName());
    }

    /**
     * Retorna o nome da classe do controlador.
     *
     * @return string
     */
    protected function getClassName()
    {
        $reflector = new \ReflectionClass($this);

        return $reflector->getName();
    }
}