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

namespace Adrenth\Redirect\Classes\Exceptions;

use RuntimeException;

/**
 * Class RulesPathNotReadable
 *
 * @package Adrenth\Redirect\Classes\Exceptions
 */
class RulesPathNotReadable extends RuntimeException
{
    /**
     * @param string $path
     * @return RulesPathNotReadable
     */
    public static function withPath($path): RulesPathNotReadable
    {
        return new static("Rules path $path is not readable.");
    }
}
