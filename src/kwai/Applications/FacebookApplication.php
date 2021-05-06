<?php
/**
 * @package Applications
 * @subpackage Facebook
 */
namespace Kwai\Applications;

use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class FacebookApplication
 *
 * Application for the facebook crawler and the entry for all links that come from Facebook.
 */
class FacebookApplication extends Application
{
    /**
     * FacebookApplication constructor.
     */
    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('template', new TemplateDependency());
        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }

    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'facebook.news',
                '/news/{id}',
                fn (ContainerInterface $container) => new FacebookStoryAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            );
    }
}
