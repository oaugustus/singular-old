<?php
namespace Acme\Controller;


use Singular\CrudController;
use Singular\Annotation\Controller;

/**
 * Class BookController.
 *
 * @Controller
 *
 * @package Acme\Controller
 */
class Book extends CrudController
{
    protected $table = 'book';
} 