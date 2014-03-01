<?php
namespace Singular;

use Singular\Provider\PackServiceProvider;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Finder\Finder;
use Silex\Provider\ServiceControllerServiceProvider;
use Singular\Exception;


/**
 * Classe principal do Framework.
 *
 * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
 */
class Application extends BaseApplication
{
    protected $packs = array();

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $app = $this;
        $app->register(new ServiceControllerServiceProvider());


        if (!isset($app['base_dir'])){
            throw Exception::baseDirNotFoundError('O diretorio raiz da aplicacao "base_dir" nao foi definido!');
        } elseif (!is_dir($app['base_dir'])){
            throw Exception::baseDirNotFoundError('O diretorio raiz da aplicacao "base_dir" nao foi encontrado!');
        }

        $this['pack_register'] = $this->share(function() use ($app){
            return new Register($app);
        });

        if (!isset($app['env'])){
            $app['env'] = 'dev';
        }

        $this->configure();
        $this->autoinclude();
    }

    /**
     * Inicializa a classe de configuração.
     */
    private function configure()
    {
        $loader = new ConfigLoader($this);

        $loader->loadConfigs();
    }

    /**
     * Inclúi arquivos PHP na pasta da aplicação automaticamente.
     */
    private function autoinclude()
    {
        $app = $this;
        $finder = new Finder();

        $appDir = $app['base_dir']."/app";

        if (!is_dir($appDir)){
            throw Exception::directoryNotFound('O diretório '.$appDir.' nao foi encontrado');
        }

        foreach ($finder->in($app['base_dir']."/app")->files()->name('*.php') as $file){
            include_once $file->getRealpath();
        }
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return Application
     */
    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        if ($provider instanceof PackServiceProvider){
            $this->packs[] = $provider;
        }

        parent::register($provider, $values);

        return $this;
    }

    /**
     * Processo de boot da Aplicação
     */
    public function boot()
    {
        if (!$this->booted){
            parent::boot();

            foreach ($this->packs as $pack){
                $this['pack_register']->register($pack);
            }
        }
    }

} 