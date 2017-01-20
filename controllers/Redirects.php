<?php

namespace Adrenth\Redirect\Controllers;

use Adrenth\Redirect\Classes\PublishManager;
use Adrenth\Redirect\Classes\RedirectManager;
use Adrenth\Redirect\Classes\RedirectRule;
use Adrenth\Redirect\Models\Redirect;
use ApplicationException;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ImportExportController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\ReorderController;
use Backend\Classes\Controller;
use Backend\Classes\FormField;
use Backend\Widgets\Form;
use BackendMenu;
use Carbon\Carbon;
use Event;
use Exception;
use Flash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Lang;
use Redirect as RedirectFacade;
use Request;
use System\Models\RequestLog;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Redirects
 *
 * @package Adrenth\Redirect\Controllers
 * @mixin FormController
 * @mixin ListController
 * @mixin ReorderController
 * @mixin ImportExportController
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

    /** @var string */
    public $formConfig = 'config_form.yaml';

    /** @var string */
    public $listConfig = [
        'list' => 'config_list.yaml',
        'requestLog' => 'request-log/config_list.yaml',
    ];

    /** @var string */
    public $reorderConfig = 'config_reorder.yaml';

    /** @var string */
    public $importExportConfig = 'config_import_export.yaml';

    /** @var PublishManager */
    public $publishManager;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $sideMenuItemCode = in_array($this->action, ['reorder', 'import', 'export'], true)
            ? $this->action
            : 'redirects';

        BackendMenu::setContext('Adrenth.Redirect', 'redirect', $sideMenuItemCode);

        $this->requiredPermissions = ['adrenth.redirect.access_redirects'];

        $this->vars['match'] = null;
    }

    /**
     * Edit Controller action
     *
     * @param int $recordId The model primary key to update.
     * @param string $context Explicitly define a form context.
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function update($recordId = null, $context = null)
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::findOrFail($recordId);

        if ($redirect->getAttribute('target_type') === Redirect::TARGET_TYPE_STATIC_PAGE
            && !class_exists('\RainLab\Pages\Classes\Page')
        ) {
            Flash::error(Lang::get('adrenth.redirect::lang.flash.static_page_redirect_not_supported'));
            return RedirectFacade::back();
        }

        parent::update($recordId, $context);
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
            /** @var FormField $field */
            $field = $host->getField($disableField);
            $field->disabled = $host->model->getAttribute('system');
        }

        if (in_array((int) $host->model->getAttribute('status_code'), [404, 410], true)) {
            /** @var FormField $field */
            $field = $host->getField('to_url');
            $field->cssClass = 'hidden';
        }
    }

    /**
     * Test Input Path
     *
     * @throws ApplicationException
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
        } catch (Exception $e) {
            throw new ApplicationException($e->getMessage());
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
            /** @var RequestLog $requestLog */
            $requestLog = RequestLog::find($checkedId);

            $url = $this->parseRequestLogItemUrl($requestLog->getAttribute('url'));

            if ($url === '') {
                continue;
            }

            Redirect::create([
                'match_type' => Redirect::TYPE_EXACT,
                'target_type' => Redirect::TARGET_TYPE_PATH_URL,
                'from_url' => $url,
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
                    'count' => $redirectsCreated,
                ]
            ));
        }

        return $this->listRefresh();
    }

    /**
     * @return array
     */
    public function onResetStatistics()
    {
        $checkedIds = $this->getCheckedIds();

        foreach ($checkedIds as $checkedId) {
            /** @var Redirect $redirect */
            $redirect = Redirect::find($checkedId);
            $redirect->update(['hits' => 0]);
            $redirect->clients()->delete();
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
     * @param string $url
     * @return string
     */
    private function parseRequestLogItemUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);

        if ($path === false || $path === '/' || $path === '') {
            return '';
        }

        // Using `parse_url($url, PHP_URL_QUERY)` will result in a string of sorted query params (2.0.23):
        // e.g ?a=z&z=a becomes ?z=a&a=z
        // So let's just grab the query part using string functions to make sure whe have the exact query string.
        $questionMarkPosition = strpos($url, '?');

        if ($questionMarkPosition !== false) {
            $path .= substr($url, $questionMarkPosition);
        }

        return $path;
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
