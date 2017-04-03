<?php

namespace Adrenth\Redirect\ReportWidgets;

use Adrenth\Redirect\Models\Redirect;
use Redirect as RedirectFacade;
use Backend;
use Backend\Classes\ReportWidgetBase;
use Backend\Widgets\Form;
use Illuminate\Http\RedirectResponse;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class CreateRedirect
 *
 * @package Adrenth\Redirect\ReportWidgets
 */
class CreateRedirect extends ReportWidgetBase
{
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
    public function onSubmit()
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
