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

use Adrenth\Redirect\Classes\Sparkline;
use Adrenth\Redirect\Classes\StatisticsHelper;
use Backend\Models\BrandSetting;

Route::group(['middleware' => ['web']], function () {
    Route::get('adrenth/redirect/sparkline/{redirectId}', function ($redirectId) {
        if (!BackendAuth::check()) {
            return Redirect::home();
        }

        $primaryColor = BrandSetting::get('primary_color', BrandSetting::PRIMARY_COLOR);

        $sparkline = new Sparkline();
        $sparkline->setFormat('200x60');
        $sparkline->setPadding('2 0 0 2');
        $sparkline->setExpire('+5 minutes');
        $sparkline->setData((new StatisticsHelper())->getRedirectHitsSparkline((int) $redirectId));
        $sparkline->setLineThickness(3.5);
        $sparkline->setLineColorHex($primaryColor);
        $sparkline->setFillColorHex($primaryColor);
        $sparkline->deactivateBackgroundColor();
        $sparkline->display();
    });
});
