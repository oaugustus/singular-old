<?php
namespace Net\Controller;


use Singular\CrudController;
use Singular\Annotation\Controller;

/**
 * Class AuthorController.
 *
 * @Controller
 *
 * @package Skill\Controller
 */
class Author extends CrudController
{
    protected $table = 'author';
} 