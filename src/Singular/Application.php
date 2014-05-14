<?php
namespace Singular;

use Silex\Provider\TwigServiceProvider;
use Singular\Provider\PackServiceProvider;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Singular\Service\Installer;
use Symfony\Component\Finder\Finder;
use Silex\Provider\ServiceControllerServiceProvider;
use Singular\Exception;
use Singular\Crud\Compiler;
use Singular\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;


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

        if (!isset($app['base_dir'])){
            throw Exception::baseDirNotFoundError('O diretorio raiz da aplicacao "base_dir" nao foi definido!');
        } elseif (!is_dir($app['base_dir'])){
            throw Exception::baseDirNotFoundError('O diretorio raiz da aplicacao "base_dir" nao foi encontrado!');
        }

        $app['web_dir'] = $app['base_dir']."/web";

        $this['pack_register'] = $this->share(function() use ($app){
            return new Register($app);
        });

        $this['singular.installer'] = $this->share(function() use ($app){
            return new Installer($app['base_dir']);
        });

        $this['singular.compiler'] = $this->share(function() use ($app){
            return new Compiler($app);
        });

        if (!isset($app['env'])){
            $app['env'] = 'dev';
        }


        $app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });

        $this->configure();
        $this->autoinclude();
    }

    /**
     * Carrega os provedores de serviço que são dependência da aplicação.
     */
    private function registerDependencies()
    {
        $app = $this;

        $app->register(new ServiceControllerServiceProvider());

        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
            $twig->addGlobal('crud', $app['singular.compiler']);
            //$twig->addFilter('levenshtein', new \Twig_Filter_Function('levenshtein'));

            return $twig;
        }));
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

        $app['app_dir'] = $app['base_dir']."/app";

        if (!is_dir($app['app_dir'])) {
            throw Exception::directoryNotFound('O diretório '.$app['app_dir'].' nao foi encontrado');
        }

        foreach ($finder->in($app['base_dir']."/app")->files()->name('*.php') as $file){
            include_once $file->getRealpath();
        }
    }

    /**
     * Convert some data into a JSON response.
     *
     * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     *
     * @return JsonResponse
     */
    public function json($data = array(), $status = 200, array $headers = array())
    {
        return new JsonResponse($data, $status, $headers);
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
        if (!$this->booted) {

            $this->registerDependencies();

            parent::boot();


            foreach ($this->packs as $pack){
                $this['pack_register']->register($pack);
            }

            $mainController = new MainController($this);
        }
    }

} 