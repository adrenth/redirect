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

        $this->addCss('/plugins/adrenth/redirect/assets/css/backend.css');

        $this->requiredPermissions = ['adrenth.redirect.access_redirects'];
        $this->redirectsFile = storage_path('app/redirects.csv');

        // Make sure that all redirects are marked un-published if redirect file is not present
        if (!file_exists($this->redirectsFile)) {
            Redirect::unPublishAll();
        }

        $this->vars['match'] = null;
        $this->vars['unpublishedCount'] = $this->getUnpublishedCount();
    }

    /**
     * @return int
     */
    private function getUnpublishedCount()
    {
        return Redirect::where(['is_published' => 0, 'is_enabled' => 1])->count();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked'))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
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
                    ->update(['is_published' => 0]);
            }
        }

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
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     */
    public function index_onPublish()
    {
        /** @type Collection $redirects */
        $redirects = Redirect::query()
            ->where('is_enabled', '=', 1)
            ->orderBy('sort_order')
            ->get(['id', 'match_type', 'from_url', 'to_url', 'status_code', 'requirements']);

        $writer = Writer::createFromPath($this->redirectsFile, 'w+');
        $writer->insertAll($redirects->toArray());

        DB::table((new Redirect())->table)
            ->where('is_enabled', '=', 1)
            ->update(['is_published' => 1]);

        Flash::success(Lang::trans('adrenth.redirect::lang.redirect.publish_success', [
            'number' => $redirects->count(),
        ]));

        return $this->listRefresh();
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
                'match' => $match,
            ]),
        ];
    }
}
