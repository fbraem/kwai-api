<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;

/**
 * Class ApplicationResource
 */
#[JSONAPI\Resource(type: 'applications', id: 'getId')]
class ApplicationResource
{
    public function __construct(
        private Entity $application
    ) {
    }

    public function getId(): string
    {
        return (string) $this->application->id();
    }

    #[JSONAPI\Attribute(name: 'title')]
    public function getTitle(): string
    {
        return $this->application->getTitle();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->application->getName();
    }
}
