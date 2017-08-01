<?php
/**
 * OctoberCMS plugin: Adrenth.Redirect
 *
 * Copyright (c) Alwin Drenth 2017.
 *
 * Licensing information:
 * https://octobercms.com/help/license/regular
 * https://octobercms.com/help/license/extended
 * https://octobercms.com/help/license/faqs
 */

declare(strict_types=1);

namespace Adrenth\Redirect\ReportWidgets;

use Adrenth\Redirect\Models\Redirect;
use Backend\Classes\Controller;
use Redirect as RedirectFacade;
use Backend;
use Backend\Classes\ReportWidgetBase;
use Backend\Widgets\Form;
use Illuminate\Http\RedirectResponse;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class CreateRedirect
 *
 * @property string alias
 * @package Adrenth\Redirect\ReportWidgets
 */
class CreateRedirect extends ReportWidgetBase
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Controller $controller, array $properties = [])
    {
        $this->alias = 'redirectCreateRedirect';

        parent::__construct($controller, $properties);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $widgetConfig = $this->makeConfig('~/plugins/adrenth/redirect/reportwidgets/createredirect/fields.yaml');
        $widgetConfig->model = new Redirect;
        $widgetConfig->alias = $this->alias.'Redirect';

        $this->vars['formWidget'] = $this->makeWidget(Form::class, $widgetConfig);

        return $this->makePartial('widget');
    }

    /**
     * @return RedirectResponse
     */
    public function onSubmit(): RedirectResponse
    {
        $redirect = Redirect::create([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => post('from_url'),
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => post('to_url'),
            'to_scheme' => Redirect::SCHEME_AUTO,
            'test_url' => post('from_url'),
            'requirements' => null,
            'status_code' => 302,
        ]);

        return RedirectFacade::to(Backend::url('adrenth/redirect/redirects/update/' . $redirect->getKey()));
    }
}
