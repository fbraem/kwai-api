<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Pages\Domain\Application;

/**
 * Class ApplicationResource
 */
#[JSONAPI\Resource(type: 'applications', id: 'getId')]
class ApplicationResource
{
    /**
     * @param Entity<Application> $application
     */
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
