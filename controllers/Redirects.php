<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
use DB;
use Flash;
use Illuminate\Database\Eloquent\Collection;
use Lang;
use League\Csv\Writer;
use Request;
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

        $this->vars['match'] = null;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function onDelete()
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
     * @return void
     * @throws \InvalidArgumentException
     */
    public function onPublish()
    {
        /** @type Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get(['id', 'match_type', 'from_url', 'to_url', 'status_code', 'requirements']);

        $path = storage_path('app/redirects.csv');

        $writer = Writer::createFromPath($path, 'w+');
        $writer->insertAll($redirects->toArray());

        DB::table((new Redirect())->table)
            ->where('is_enabled', '=', 1)
            ->update(['is_published' => 1]);

        Flash::success(Lang::trans('adrenth.redirect::lang.redirect.publish_success', [
            'number' => $redirects->count()
        ]));
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
            $match = $manager->match($inputPath);
        } catch (\Exception $e) {
            throw new \ApplicationException($e->getMessage());
        }

        return [
            '#testResult' => $this->makePartial('redirect_test_result', [
                'match' => $match
            ]),
        ];
    }
}
