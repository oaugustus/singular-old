<?php
namespace Singular;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Singular\Annotation\Route;
use Singular\Crud\Form;
use Singular\Crud\Grid;
use Singular\Store;

/**
 * Class CrudController.
 *
 * @package Singular
 * @todo    Alternar métodos 'get' para 'post'
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
class CrudController extends Controller
{
    /**
     * Referência ao nome da tabela a ser utilizada no crud.
     *
     * @var string
     */
    protected $table = '';

    /**
     * Store associado ao CrudController.
     *
     * @var null|Store
     */
    protected $store = null;

    /**
     * Título a ser exibido no painel principal do módulo.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Título a ser exibido no painel de listagem do módulo.
     *
     * @var string
     */
    protected $listTitle = '';

    /**
     * Referência à conexão de banco de dados.
     *
     * @var string
     */
    protected $conn = 'default';

    public function __construct(Application $app, $pack)
    {
        if ($this->table == ''){
            throw new \Exception('A propriedade $table do CrudController "'.$this->getClassName().'" precisa ser definida');
        }

        if ($this->title == ''){
            $this->title = $this->getShortName();
        }

        if ($this->listTitle == ''){
            $this->listTitle = 'Lista de '.$this->getShortName().'s';
        }

        parent::__construct($app, $pack);
    }

    /**
     * Recupera um um registro da tabela.
     *
     * @route(method="get")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function get(Request $request, $id)
    {
        $app = $this->app;

        $rec = $this->getStore()->find($id);

        return $app->json(array(
            'success' => $rec ? true : false,
            'result' => $rec
        ));
    }

    /**
     * Localiza e retorna um conjunto de registros da tabela.
     *
     * @route(method="post")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function query(Request $request)
    {
        $app = $this->app;

        $success = true;

        try {
            $request->request->set('name','Mas');//@todo: remover atribuição
            $result = $this->getStore()->findBy($request->request->all());

        } catch(\Exception $e) {
            $result = false;
            $success = false;
        }

        return $app->json(array(
            'success' => $success,
            'result' => $result
        ));
    }

    /**
     * Cria ou atualiza um registro na tabela.
     *
     * @route(method="post")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        $app = $this->app;

        $data = $request->request->all();
        $saved = $this->getStore()->save($data);

        return $app->json(array(
            'success' => (bool)$saved,
            'result' => $saved
        ));
    }

    /**
     * Remove um registro na tabela.
     *
     * @route(method="delete")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function remove(Request $request, $id)
    {
        $app = $this->app;

        //$id = $request->get('id',2); // @todo: remover default

        return $app->json(array(
            'success' => $this->getStore()->remove($id)
        ));
    }

    /**
     * Remove uma seleção de registros da tabela.
     *
     * @route(method="delete")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeSelection(Request $request)
    {
        $app = $this->app;

        $ids = $request->get('ids',array(5,6)); // @todo: remover default

        $allRemoved = true;

        foreach ($ids as $id) {
            if (!$this->getStore()->remove($id)) {
                $allRemoved = false;
            }
        }

        return $app->json(array(
            'success' => $allRemoved
        ));
    }

    public function _compile($package, $module)
    {
        $app = $this->app;

        $fs = new Filesystem();

        $runtimeDir = $app['base_dir'].'/web/runtime';
        $compiled = $this->_compileModule($package, $module)."\n\n";
        $compiled.= $this->_compileStore($package, $module)."\n\n";
        $compiled.= $this->_compileList($package, $module)."\n\n";
        $compiled.= $this->_compileForm($package, $module)."\n\n";

        file_put_contents("$runtimeDir/$package.$module.js", $compiled);

        $tpl = '<script type="text/javascript" src="%s"></script>';

        echo sprintf($tpl, "runtime/$package.$module.js");
    }

    /**
     * Compila o script de definição do módulo.
     *
     * @param $package
     * @param $module
     */
    private function _compileModule($package, $module)
    {
        $moduleScript = file_get_contents(__DIR__."/Crud/tpl/module.tpl.js");
        $moduleScript = str_replace('@package', $package, $moduleScript);
        $moduleScript = str_replace('@module', $module, $moduleScript);

        return "//$package.$module module \n".$moduleScript;
    }

    /**
     * Compila os componentes da página de listagem do módulo.
     *
     * @param $package
     * @param $module
     */
    private function _compileList($package, $module)
    {
        $html = file_get_contents(__DIR__."/Crud/tpl/list.tpl.html");
        $html = str_replace('@package', $package, $html);
        $html = str_replace('@module', $module, $html);
        $html = str_replace('@title', $this->title, $html);
        $html = str_replace('@listTitle', $this->listTitle, $html);

        $id = "src/$package/$module/views/list.html";
        $tpl = '<script type="text/ng-template" id="%s">%s;</script>';
        echo sprintf($tpl, $id, $html);

        $listCtrl = file_get_contents(__DIR__."/Crud/tpl/list.controller.tpl.js");
        $listCtrl = str_replace('@package', $package, $listCtrl);
        $listCtrl = str_replace('@Package', ucfirst($package), $listCtrl);
        $listCtrl = str_replace('@module', $module, $listCtrl);
        $listCtrl = str_replace('@Module', ucfirst($module), $listCtrl);

        return "//$package.$module list controller\n".$listCtrl;
    }

    /**
     * Compila o o serviço utilizado para salvamento/recuperação de registros.
     *
     * @param $package
     * @param $module
     */
    private function _compileStore($package, $module)
    {
        $store = file_get_contents(__DIR__."/Crud/tpl/store.tpl.js");
        $store = str_replace('@package', $package, $store);
        $store = str_replace('@Package', ucfirst($package), $store);
        $store = str_replace('@module', $module, $store);
        $store = str_replace('@Module', ucfirst($module), $store);

        return "//$package.$module resource service\n".$store;
    }

    /**
     * Compila os componentes da página de cadastro/edição do módulo.
     *
     * @param $package
     * @param $module
     */
    private function _compileForm($package, $module)
    {
        $html = file_get_contents(__DIR__."/Crud/tpl/form.tpl.html");
        $html = str_replace('@package', $package, $html);
        $html = str_replace('@module', $module, $html);
        $html = str_replace('@title', $this->title, $html);
        $html = str_replace('@formName', $module, $html);
        $html = str_replace('@form', $this->getForm()->compile(), $html);


        $id = "src/$package/$module/views/form.html";
        $tpl = '<script type="text/ng-template" id="%s">%s;</script>';
        echo sprintf($tpl, $id, $html);

        $newCtrl = file_get_contents(__DIR__."/Crud/tpl/new.controller.tpl.js");
        $newCtrl = str_replace('@package', $package, $newCtrl);
        $newCtrl = str_replace('@Package', ucfirst($package), $newCtrl);
        $newCtrl = str_replace('@module', $module, $newCtrl);
        $newCtrl = str_replace('@Module', ucfirst($module), $newCtrl);

        $return = "//$package.$module new controller\n".$newCtrl;

        $editCtrl = file_get_contents(__DIR__."/Crud/tpl/edit.controller.tpl.js");
        $editCtrl = str_replace('@package', $package, $editCtrl);
        $editCtrl = str_replace('@Package', ucfirst($package), $editCtrl);
        $editCtrl = str_replace('@module', $module, $editCtrl);
        $editCtrl = str_replace('@Module', ucfirst($module), $editCtrl);

        $return .= "//$package.$module edit controller\n".$editCtrl;

        return $return;
    }


    /**
     * Recupera o store associado ao CrudController.
     *
     * @return Store
     */
    private function getStore()
    {
        return $this->getService('store');
    }

    /**
     * Recupera o serviço de formulário associado ao CrudController.
     *
     * @return Form
     */
    private function getForm()
    {
        return $this->getService('form');
    }

    /**
     * Recupera o serviço de grid associado ao CrudController
     *
     * @return Grid
     */
    private function getGrid()
    {
        return $this->getService('grid');
    }

    /**
     * Retorna um serviço (Form,Grid,Store) associado ao CrudController.
     *
     * @param String $service
     *
     * @return Service
     */
    private function getService($service)
    {
        $app = $this->app;
        $pack = $this->pack;
        $table = $this->table;
        $serviceName = $pack.".$service.".$this->getServiceName();

        if (!isset($app[$serviceName])) {
            $class = $this->getServiceClass($service);

            $app[$serviceName] = $app->share(function () use ($app, $pack, $table, $class) {
                $service = new $class($app, $pack);
                $service->setTable($table, $this->conn);

                return $service;
            });

        } else {
            $app[$serviceName]->setTable($table, $this->conn);
        }

        return $app[$serviceName];
    }

    /**
     * Retorna o nome da classe base de um serviço do CrudController.
     *
     * @param String $service
     *
     * @return String
     */
    private function getServiceClass($service)
    {
        switch ($service) {
            case 'store':
                return 'Singular\Store';
            break;
            case 'form':
                return 'Singular\Crud\Form';
            break;
            case 'grid':
                return 'Singular\Crud\Grid';
            break;
            default:
                throw new \Exception('Serviço '.$service.' não foi localizado');
        }
    }
} 