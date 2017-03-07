<?php

namespace Adrenth\Redirect\Classes\Exceptions;

use RuntimeException;

/**
 * Class RulesPathNotReadable
 *
 * @package Adrenth\Redirect\Classes\Exceptions
 */
final class RulesPathNotReadable extends RuntimeException
{
    /**
     * @param string $path
     * @return RulesPathNotReadable
     */
    public static function withPath($path)
    {
        return new static("Rules path $path is not readable.");
    }
}
