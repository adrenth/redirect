<?php
/**
 * October CMS plugin: Adrenth.Redirect
 *
 * Copyright (c) 2016 - 2018 Alwin Drenth
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
