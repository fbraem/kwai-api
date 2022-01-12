<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Converter;

use InvalidArgumentException;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;

/**
 * Class ConverterFactory
 */
class ConverterFactory
{
    private array $converters = [];

    public function createConverter(DocumentFormat $format) : Converter
    {
        if (isset($this->converters[$format->value])) {
            return new $this->converters[$format->value]();
        }
        throw new InvalidArgumentException("No converter registered for {$format->value}");
    }

    public function register(string $type, string $class)
    {
        $this->converters[$type] = $class;
    }
}
