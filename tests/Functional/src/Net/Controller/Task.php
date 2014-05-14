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
class Task extends CrudController
{
    protected $table = 'task';
} 