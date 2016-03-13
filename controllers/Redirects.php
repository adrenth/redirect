<?php

namespace Adrenth\Redirect\Controllers;

use DB;
use Adrenth\Redirect\Models\Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Lang;
use League\Csv\Writer;
use System\Classes\SettingsManager;
use Flash;

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

    /**
     * @return mixed
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked'))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            foreach ($checkedIds as $redirectId) {
                if (!$redirect = Redirect::find($redirectId)) {
                    continue;
                }

                $redirect->delete();
            }
        }

        return $this->listRefresh();
    }

    /**
     * Disable checked users.
     */
    public function index_onDisable()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            $redirect = new Redirect;
            DB::table($redirect->getTable())->whereIn('id', $checkedIds)->update(['is_enabled' => 0]);
            $redirect->flushCache();

            Flash::success('Successfully disabled the selected redirects.');
        }
        else {
            Flash::error('There are no selected redirects to disable.');
        }

        return $this->listRefresh();
    }

    /**
     * Enable checked users.
     */
    public function index_onEnable()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            $redirect = new Redirect;
            DB::table($redirect->getTable())->whereIn('id', $checkedIds)->update(['is_enabled' => 1]);
            $redirect->flushCache();

            Flash::success('Successfully enabled the selected redirects.');
        }
        else {
            Flash::error('There are no selected redirects to enable.');
        }

        return $this->listRefresh();
    }

    public function index_onPublish()
    {
        /** @type Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get(['id', 'match_type', 'from_url', 'to_url', 'status_code']);

        $path = storage_path('app/redirects.csv');

        $writer = Writer::createFromPath($path, 'w+');
        $writer->insertAll($redirects->toArray());

        // export to csv

        Flash::success(Lang::trans('adrenth.redirect::lang.redirect.publish_success', [
            'number' => $redirects->count()
        ]));
    }
}
