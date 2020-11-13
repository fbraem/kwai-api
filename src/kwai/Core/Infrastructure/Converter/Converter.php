<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);


namespace Kwai\Core\Infrastructure\Converter;


/**
 * Interface Converter
 *
 * Interface for a class that converts text in some format to HTML.
 */
interface Converter
{
    /**
     * Convert the given string to HTML
     * @param string $text
     * @return string
     */
    public function convert(string $text): string;
}
