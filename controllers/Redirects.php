<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\PublishManager;
use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\Widgets\Form;
use BackendMenu;
use Carbon\Carbon;
use DB;
use Event;
use Flash;
use Lang;
use Request;
use System\Models\RequestLog;

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
    public $listConfig = [
        'list' => 'config_list.yaml',
        'requestLog' => 'request-log/config_list.yaml',
    ];

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

        $this->requiredPermissions = ['adrenth.redirect.access_redirects'];

        $this->vars['match'] = null;
    }

    // @codingStandardsIgnoreStart

    /**
     * @return array
     */
    public function index_onDelete()
    {
        Redirect::destroy($this->getCheckedIds());
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * @return array
     */
    public function index_onEnable()
    {
        Redirect::whereIn('id', $this->getCheckedIds())->update(['is_enabled' => 1]);
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    /**
     * @return array
     */
    public function index_onDisable()
    {
        Redirect::whereIn('id', $this->getCheckedIds())->update(['is_enabled' => 0]);
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd

    /**
     * Called after the form fields are defined.
     *
     * @param Form $host
     * @param array $fields
     */
    public function formExtendFields(Form $host, array $fields = [])
    {
        $disableFields = [
            'from_url',
            'to_url',
            'cms_page',
            'target_type',
            'match_type',
        ];

        foreach ($disableFields as $disableField) {
            /** @type FormField $field */
            $field = $host->getField($disableField);
            $field->disabled = $host->model->getAttribute('system');
        }
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
     * Triggers Request Log dialog
     *
     * @return string
     * @throws \SystemException
     */
    public function onOpenRequestLog()
    {
        $this->makeLists();
        return $this->makePartial('request-log/modal');
    }

    /**
     * Create Redirects from Request Log items
     *
     * @return array
     */
    public function onCreateRedirectFromRequestLogItems()
    {
        $checkedIds = $this->getCheckedIds();
        $redirectsCreated = 0;

        foreach ($checkedIds as $checkedId) {
            /** @type RequestLog $requestLog */
            $requestLog = RequestLog::find($checkedId);
            $path = parse_url($requestLog->getAttribute('url'), PHP_URL_PATH);

            if ($path === false || $path === '/' || $path === '') {
                continue;
            }

            Redirect::create([
                'match_type' => Redirect::TYPE_EXACT,
                'target_type' => Redirect::TARGET_TYPE_PATH_URL,
                'from_url' => $path,
                'to_url' => '/',
                'status_code' => 301,
                'is_enabled' => false,
            ]);

            $redirectsCreated++;
        }

        if ((bool) Request::get('andDelete', false)) {
            RequestLog::destroy($checkedIds);
        }

        if ($redirectsCreated > 0) {
            Event::fire('redirects.changed');

            Flash::success(Lang::get(
                'adrenth.redirect::lang.flash.success_created_redirects',
                [
                    'count' => $redirectsCreated
                ]
            ));
        }

        return $this->listRefresh();
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
     * Called after the creation or updating form is saved.
     *
     * @param Model
     */
    public function formAfterSave($model)
    {
        Event::fire('redirects.changed');
    }

    /**
     * Called after the form model is deleted.
     *
     * @param Model
     */
    public function formAfterDelete($model)
    {
        Event::fire('redirects.changed');
    }
}
