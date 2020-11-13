<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Converter;

use InvalidArgumentException;

/**
 * Class ConverterFactory
 */
class ConverterFactory
{
    private array $converters = [];

    public function createConverter(string $type) : Converter
    {
        if (isset($this->converters[$type])) {
            return new $this->converters[$type]();
        }
        throw new InvalidArgumentException("No converter registered for $type");
    }

    public function register(string $type, string $class)
    {
        $this->converters[$type] = $class;
    }
}
