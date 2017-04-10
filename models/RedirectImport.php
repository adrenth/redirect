<?php

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
     * Validation rules
     *
     * @var array
     */
    public $rules = [
        'to_scheme' => 'in:http,https,auto',
        'from_url' => 'required',
        'from_scheme' => 'in:http,https,auto',
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
    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {
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

                $source->forceSave();

                $this->logCreated();
            } catch (Exception $e) {
                $this->logError($row, $e->getMessage());
            }
        }

        Event::fire('redirects.changed');
    }
}
