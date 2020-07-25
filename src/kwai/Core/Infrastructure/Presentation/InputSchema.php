<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Nette\Schema\Elements\Structure;

/**
 * Interface InputSchema
 */
interface InputSchema
{
    /**
     * Create a schema structure
     * @return Structure
     */
    public function create(): Structure;

    /**
     * Process the normalized data
     *
     * @param mixed $normalized
     * @return mixed
     */
    public function process($normalized);
}
