<?php

namespace Adrenth\Redirect\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Class Redirects
 *
 * @package Adrenth\Redirect\Controllers
 */
class Redirects extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController'
    ];

    /** @type string */
    public $formConfig = 'config_form.yaml';

    /** @type string */
    public $listConfig = 'config_list.yaml';

    /** @type string */
    public $reorderConfig = 'config_reorder.yaml';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->requiredPermissions = ['adrenth.redirect.access_redirects'];

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Adrenth.Redirect', 'redirects');
    }
}
