<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Nette\Schema\Processor;
use Nette\Schema\ValidationException;

/**
 * Class InputSchemaProcessor
 */
class InputSchemaProcessor
{
    private InputSchema $schema;

    /**
     * InputSchemaProcessor constructor.
     *
     * @param InputSchema $schema
     */
    public function __construct(InputSchema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Process the schema with the given data. When the data is invalid,
     * a ValidationException will be thrown. On success the result of the schema
     * process will be returned.
     *
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function process($data)
    {
        $processor = new Processor();
        $normalized = $processor->process($this->schema->create(), $data);
        return $this->schema->process($normalized);
    }

    /**
     * Factory method
     *
     * @param InputSchema $schema
     * @return static
     */
    public static function create(InputSchema $schema): self
    {
        return new self($schema);
    }
}
