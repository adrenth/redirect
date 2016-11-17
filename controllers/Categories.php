<?php

namespace Adrenth\Redirect\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class Categories
 *
 * @package Adrenth\Redirect\Controllers
 */
class Categories extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'categories');
    }
}
