<?php

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
    public static function withScheme($scheme)
    {
        return new static("Scheme '$scheme' is not a valid scheme. Use 'http' or 'https'.");
    }
}
