<?php
namespace Acme\Controller;


use Singular\CrudController;
use Singular\Annotation\Controller;

/**
 * Class AuthorController.
 *
 * @Controller
 *
 * @package Acme\Controller
 */
class Autor extends CrudController
{
    protected $table = 'autor';
    protected $title = 'Autor';
    protected $listTitle = 'Autores';
} 