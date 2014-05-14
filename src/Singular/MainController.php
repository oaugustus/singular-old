<?php

namespace Singular;

/**
 * Class MainController.
 *
 * Controlador principal de uma aplicação singular.
 *
 * @package Singular
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
class MainController
{
    /**
     * Referência à aplicação Singular.
     *
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->defineRoutes();
    }

    /**
     * Define rotas básicas utilizadas e disponibilizadas pela aplicação principal.
     */
    private function defineRoutes()
    {
        $app = $this->app;

        if (!isset($app['main_route_pattern'])){
            $app['main_route_pattern'] = '/main.app';
        }

        /**
         * Rota principal da aplicação.
         */
        $app->get($app['main_route_pattern'], function() use ($app) {

            //echo "<pre>";
            //print_r($this->app);
            //echo "</pre>";
            //die();
            return $app['twig']->render('singular/singular.main.html');
        });

        /**
         * Rota que instala os componentes do singular na aplicação.
         *
         * @param Boolean $override se os arquivos de views devem ser sobrescritos
         */
        $app->get('singular/install/{override}', function($override) use ($app) {
            $app['singular.installer']->install((boolean)$override);
            return "Singular instalado";
        })->value('override', false);

    }
} 