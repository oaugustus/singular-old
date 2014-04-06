<?php
namespace Singular;
use Singular\Application;
use Symfony\Component\Finder\Finder;
use Igorw\Silex\ConfigServiceProvider;

/**
 * Class ConfigLoader
 *
 * @package Neton
 */
class ConfigLoader
{
    private $files = array();

    /**
     * Inicializa o carregador de aplicação.
     *
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Carrega as configurações do sistema.
     */
    public function loadConfigs()
    {
        $app = $this->app;
        $configDir = $app['base_dir']."/app/config";

        if (!is_dir($configDir)){
            throw Exception::directoryNotFound('O diretorio "'.$configDir.'" nao foi encontrado!');
        }

        $this->preloadFiles($configDir);
        $this->registerConfigs();
    }

    /**
     * Carrega os arquivos de configuração e os armazena em variável local.
     *
     * @param string $dir
     */
    private function preloadFiles($dir)
    {
        $finder = new Finder();

        $files = $finder->in($dir)->files()->name('*.json');

        foreach ($files as $file) {
            $filenameParts = explode('.',$file->getFilename());

            if (count($filenameParts) >= 3){
                $env = $filenameParts[count($filenameParts)-2];
            } else {
                $env = false;
            }

            if ($env == false || $env == $this->app['env']){
                $json = json_decode(file_get_contents($file->getRealpath()), true);

                $params = null;

                if (isset($json['_params'])) {
                    $params = $json['_params'];
                }

                $cfgIndex = count($this->files);

                $this->files[$cfgIndex] = array(
                    'file' => $file->getRealpath(),
                    '_params' => $params,
                    '_priority' => isset($json['_priority']) ? $json['_priority'] : 0
                );

            }

        }

        usort($this->files, array($this,'sortByPriority'));
    }

    /**
     * Ordena as entradas dos arquivos pela sua prioridade.
     *
     * Arquivos de configuração com prioridade maior serão posicionados no final da lista.
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function sortByPriority($a, $b)
    {
        $a = $a['_priority'];
        $b = $b['_priority'];

        if ($a == $b)
        {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    /**
     * Registra os provedores de serviço de configurações.
     */
    private function registerConfigs()
    {
        foreach ($this->files as $config){
            $params = array();

            if ($config['_params'] != null){
                if (isset($this->app[$config['_params']])){
                    $params = $this->app[$config['_params']];
                }
            }

            $this->app->register(new ConfigServiceProvider($config['file'], $params));
        }

        unset($this->app['_params']);
        unset($this->app['_priority']);
    }
} 