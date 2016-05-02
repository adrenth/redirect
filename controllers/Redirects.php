<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\PublishManager;
use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
use Carbon\Carbon;
use DB;
use League\Csv\Writer;
use October\Rain\Database\Builder;
use Request;

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
        'Backend.Behaviors.ReorderController',
        'Backend.Behaviors.ImportExportController',
    ];

    /** @type string */
    public $formConfig = 'config_form.yaml';

    /** @type string */
    public $listConfig = 'config_list.yaml';

    /** @type string */
    public $reorderConfig = 'config_reorder.yaml';

    /** @type string */
    public $importExportConfig = 'config_import_export.yaml';

    /** @type PublishManager */
    public $publishManager;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', $this->action);

        $this->loadAssets();

        $this->requiredPermissions = ['adrenth.redirect.access_redirects'];

        $this->vars['match'] = null;
    }

    /**
     * Load assets
     *
     * @return void
     */
    private function loadAssets()
    {
        $this->addCss('/plugins/adrenth/redirect/assets/css/backend.css');
    }

    // @codingStandardsIgnoreStart

    /**
     * @return array
     */
    public function index_onDelete()
    {
        Redirect::whereIn('id', $this->getCheckedIds())->delete();

        return $this->listRefresh();
    }

    /**
     * @return array
     */
    public function index_onEnable()
    {
        Redirect::whereIn('id', $this->getCheckedIds())->update(['is_enabled' => 1]);

        return $this->listRefresh();
    }

    /**
     * @return array
     */
    public function index_onDisable()
    {
        Redirect::whereIn('id', $this->getCheckedIds())->update(['is_enabled' => 0]);

        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd

    /**
     * Controller override: Extend the query used for populating the list
     * after the default query is processed.
     *
     * @param \October\Rain\Database\Builder $query
     * @param mixed $definition
     */
    public function listExtendQuery(Builder $query, $definition = null)
    {
        //$query->where('system', '=', 0);
    }

    /**
     * Test Input Path
     *
     * @throws \ApplicationException
     */
    public function onTest()
    {
        $inputPath = Request::get('inputPath');
        $redirect = new Redirect(Request::get('Redirect'));

        try {
            $rule = RedirectRule::createWithModel($redirect);
            $manager = RedirectManager::createWithRule($rule);

            $testDate = new Carbon(Request::get('test_date', date('Y-m-d')));
            $manager->setMatchDate($testDate);

            $match = $manager->match($inputPath);
        } catch (\Exception $e) {
            throw new \ApplicationException($e->getMessage());
        }

        return [
            '#testResult' => $this->makePartial('redirect_test_result', [
                'match' => $match,
                'url' => $match ? $manager->getLocation($match) : '',
            ]),
        ];
    }

    /**
     * @return array
     */
    private function getCheckedIds()
    {
        if (($checkedIds = post('checked'))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            return $checkedIds;
        }

        return [];
    }
}
