<?php

namespace Singular;

use Singular\Application;

/**
 * Class Service.
 *
 * @package Singular
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
class Service
{
    /**
     * Nome do pacote a que o serviço pertence.
     *
     * @var string
     */
    protected $pack = '';

    /**
     * Inicializa o serviço com a referência à aplicação.
     *
     * @param Application $app
     * @param String      $pack
     */
    public function __construct(Application $app, $pack)
    {
        $this->app = $app;
        $this->pack = $pack;
    }
}