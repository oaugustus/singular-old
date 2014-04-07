<?php
namespace Singular;
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
    public function get(Request $request)
    {
        $app = $this->app;

        $id = $request->get('id',1);//@todo: remover default = 1
        $rec = $this->getStore()->find($id);

        return $app->json(array(
            'success' => $rec ? true : false,
            'result' => $rec
        ));
    }

    /**
     * Localiza e retorna um conjunto de registros da tabela.
     *
     * @route(method="get")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request)
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
     * @route(method="get")
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
     * @route(method="get")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function remove(Request $request)
    {
        $app = $this->app;

        $id = $request->get('id',2); // @todo: remover default

        return $app->json(array(
            'success' => $this->getStore()->remove($id)
        ));
    }

    /**
     * Remove uma seleção de registros da tabela.
     *
     * @route(method="get")
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

    /**
     * Testa a compilação do formulário.
     *
     * @route(method="get")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function compileForm(Request $request)
    {
        return $this->app->json($this->getForm()->compile());
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
                return 'Store';
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