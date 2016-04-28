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
use Flash;
use Lang;
use League\Csv\Writer;
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

        $this->publishManager = new PublishManager();

        $this->vars['match'] = null;
        $this->vars['unpublishedCount'] = $this->publishManager->getUnpublishedCount();
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

    /**
     * @return array
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function index_onDelete()
    {
        $checkedIds = $this->getCheckedIds();

        $deleteCount = 0;
        foreach ($checkedIds as $redirectId) {
            if (!$redirect = Redirect::find($redirectId)) {
                continue;
            }

            $redirect->delete();
            $deleteCount++;
        }

        if ($deleteCount > 0) {
            DB::table((new Redirect())->table)
                ->whereNotIn('id', $checkedIds)
                ->where('is_enabled', '=', 1)
                ->update(['publish_status' => Redirect::STATUS_CHANGED]);
        }

        return $this->listAndPublishButtonRefresh();
    }

    /**
     * @return array
     */
    public function index_onEnable()
    {
        $checkedIds = $this->getCheckedIds();

        foreach ($checkedIds as $redirectId) {
            if (!$redirect = Redirect::find($redirectId)) {
                continue;
            }

            $redirect->update(['is_enabled' => 1]);
        }

        return $this->listAndPublishButtonRefresh();
    }

    /**
     * @return array
     */
    public function index_onDisable()
    {
        $checkedIds = $this->getCheckedIds();

        foreach ($checkedIds as $redirectId) {
            if (!$redirect = Redirect::find($redirectId)) {
                continue;
            }

            $redirect->update(['is_enabled' => 0]);
        }

        return $this->listAndPublishButtonRefresh();
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    public function index_onPublish()
    {
        $numberOfRedirects = $this->publishManager->publish();

        if ($numberOfRedirects) {
            Flash::success(Lang::trans('adrenth.redirect::lang.flash.publish_success', [
                'number' => $numberOfRedirects,
            ]));
        } else {
            Flash::info(Lang::trans('adrenth.redirect::lang.flash.publish_no_redirects'));
        }

        return $this->listAndPublishButtonRefresh();
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

    /**
     * @return array
     */
    private function listAndPublishButtonRefresh()
    {
        try {
            return array_merge(
                $this->listRefresh(),
                [
                    '#publishButton' => $this->makePartial(
                        'button_publish',
                        [
                            'unpublishedCount' => $this->publishManager->getUnpublishedCount(),
                        ]
                    ),
                ]
            );
        } catch (\SystemException $e) {
            return [];
        }
    }
}
