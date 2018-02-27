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

namespace Adrenth\Redirect\Models;

use Backend\Models\ImportModel;
use Eloquent;
use Event;
use Exception;

/** @noinspection LongInheritanceChainInspection */

/**
 * Class RedirectImport
 *
 * @package Adrenth\Redirect\Models
 * @mixin Eloquent
 */
class RedirectImport extends ImportModel
{
    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirect_redirects';

    /**
     * Basic validation rules.
     * More (conditional) rules will be applied when importing.
     *
     * @var array
     */
    public $rules = [
        'from_url' => 'required',
        'match_type' => 'required|in:exact,placeholders',
        'target_type' => 'required|in:path_or_url,cms_page,static_page,none',
        'status_code' => 'required|in:301,302,303,404,410',
    ];

    private static $nullableAttributes = [
        'category_id',
        'from_date',
        'to_date',
        'last_used_at',
        'to_url',
        'test_url',
        'cms_page',
        'static_page',
        'requirements',
        'test_lab_path',
    ];

    /**
     * {@inheritdoc}
     */
    public function importData($results, $sessionKey = null)//: void
    {
        foreach ((array) $results as $row => $data) {
            try {
                $source = Redirect::make();

                $except = ['id'];

                foreach (array_except($data, $except) as $attribute => $value) {
                    if ($attribute === 'requirements') {
                        $value = json_decode($value);
                    } elseif (empty($value) && in_array($attribute, self::$nullableAttributes, true)) {
                        $value = null;
                    }

                    $source->setAttribute($attribute, $value);
                }

                $source->save();

                $this->logCreated();
            } catch (Exception $e) {
                $this->logError($row, $e->getMessage());
            }
        }

        Event::fire('redirects.changed');
    }
}
