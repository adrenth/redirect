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
 * Class InvalidScheme
 *
 * @package Adrenth\Redirect\Classes\Exceptions
 */
class InvalidScheme extends RuntimeException
{
    /**
     * @param string $scheme
     * @return InvalidScheme
     */
    public static function withScheme($scheme): InvalidScheme
    {
        return new static("Scheme '$scheme' is not a valid scheme. Use 'http' or 'https'.");
    }
}
