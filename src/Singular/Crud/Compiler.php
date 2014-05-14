<?php

namespace Singular\Crud;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Compiler.
 *
 * @package Singular\Crud
 */
class Compiler
{
    protected $controllers = array();

    protected $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Adiciona um controlador na lista de crud controllers do compilador.
     *
     * @param String $service
     */
    public function addController($service)
    {
        $this->controllers[] = $service;
    }

    public function compile()
    {
        $app = $this->app;
        $controllers = $this->controllers;
        $modules = array();

        $fs = new Filesystem();
        $fs->remove($app['base_dir']."/web/runtime");
        $fs->mkdir($app['base_dir']."/web/runtime");

        foreach ($controllers as $service) {
            $serviceParts = explode('.', $service);
            $package = current($serviceParts);
            $module = end($serviceParts);

            $modules[] = sprintf('"%s.%s"',$package,$module);
            $app[$service]->_compile($package, $module);
        }

        $moduleScript = file_get_contents(__DIR__."/tpl/app.modules.tpl.js");
        $moduleScript = str_replace('@modules', (implode(',', $modules)), $moduleScript);

        $tpl = '<script type="text/javascript">%s;</script>';

        echo sprintf($tpl, $moduleScript);
    }
} 