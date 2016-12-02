<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Models\RedirectLog;
use BackendMenu;
use Backend\Classes\Controller;
use Flash;
use Lang;

/**
 * Class Logs
 *
 * @package Adrenth\Redirect\Controllers
 * @method array listRefresh()
 */
class Logs extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    /** @var string */
    public $listConfig = 'config_list.yaml';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', 'logs');
    }

    // @codingStandardsIgnoreStart

    public function index_onRefresh()
    {
        return $this->listRefresh();
    }

    public function index_onEmptyLog()
    {
        RedirectLog::truncate();
        Flash::success(Lang::get('adrenth.redirect::lang.flash.truncate_success'));
        return $this->listRefresh();
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked', []))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            foreach ((array) $checkedIds as $recordId) {
                if (!$record = RedirectLog::find($recordId)) {
                    continue;
                }
                $record->delete();
            }

            Flash::success(Lang::get('adrenth.redirect::lang.flash.delete_selected_success'));
        }
        else {
            Flash::error(Lang::get('backend::lang.list.delete_selected_empty'));
        }

        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd
}
