<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use BackendMenu;
use Carbon\Carbon;
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

    /** @type string */
    private $redirectsFile;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Adrenth.Redirect', 'redirects');

        $this->loadAssets();

        $this->requiredPermissions = ['adrenth.redirect.access_redirects'];
        $this->redirectsFile = storage_path('app/redirects.csv');

        // Make sure that all redirects are marked un-published if redirect file is not present
        if (!file_exists($this->redirectsFile)) {
            Redirect::unpublishAll();
        }

        $this->vars['match'] = null;
        $this->vars['unpublishedCount'] = $this->getUnpublishedCount();
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
     * @return int
     */
    private function getUnpublishedCount()
    {
        $total = 0;

        /** @type Redirect $redirect */
        $redirect = Redirect::select([DB::raw('COUNT(id) AS redirect_count')])
            ->where('publish_status', '<>', Redirect::STATUS_PUBLISHED)
            ->where('is_enabled', '=', 1)
            ->first(['redirect_count']);

        $total += (int) $redirect->getAttribute('redirect_count');

        $redirect = Redirect::select([DB::raw('COUNT(id) AS redirect_count')])
            ->where('publish_status', '=', Redirect::STATUS_CHANGED)
            ->where('is_enabled', '=', 0)
            ->first(['redirect_count']);

        $total += (int) $redirect->getAttribute('redirect_count');

        return $total;
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
        /** @type Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get([
                'id',
                'match_type',
                'from_url',
                'to_url',
                'status_code',
                'requirements',
                'from_date',
                'to_date',
            ]);

        $writer = Writer::createFromPath($this->redirectsFile, 'w+');
        $writer->insertAll($redirects->toArray());

        $table = (new Redirect())->table;

        DB::table($table)->where('is_enabled', '=', 1)
            ->update(['publish_status' => Redirect::STATUS_PUBLISHED]);

        DB::table($table)->where('is_enabled', '=', 0)
            ->update(['publish_status' => Redirect::STATUS_NOT_PUBLISHED]);

        Flash::success(Lang::trans('adrenth.redirect::lang.redirect.publish_success', [
            'number' => $redirects->count(),
        ]));

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
                            'unpublishedCount' => $this->getUnpublishedCount(),
                        ]
                    ),
                ]
            );
        } catch (\SystemException $e) {
            return [];
        }
    }
}
