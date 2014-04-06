<?php
namespace Singular;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Singular\Annotation\Route;

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
    protected $conn = 'db';

    public function __construct(Application $app, $pack)
    {
        parent::__construct($app, $pack);

        $this->store = $this->getStore();
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
        $rec = $this->store->find($id);

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
            $request->request->set('name','Enx');//@todo: remover atribuição
            $result = $this->store->findBy($request->request->all());

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
            'success' => $this->store->remove($id)
        ));
    }

    /**
     * Remove uma seleção de registros da tabela.
     *
     * @route(method="post")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeSelection(Request $request)
    {

    }

    /**
     * Recupera o store associado ao CrudController.
     *
     * @return Store
     */
    private function getStore()
    {
        $app = $this->app;
        $pack = $this->pack;
        $table = $this->table;
        $storeService = $pack.".store.".$this->getServiceName();

        if (!isset($app[$storeService])) {
            $app[$storeService] = $app->share(function () use ($app, $pack, $table) {
                $store = new Store($app, $pack);
                $store->setTable($table);

                return $store;
            });
        } else {
            $app[$storeService]->setTable($table);
        }

        return $app[$storeService];
    }
} 