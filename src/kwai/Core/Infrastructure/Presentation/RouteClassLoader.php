<?php
/**
 * @package Infrastructure
 * @subpackage Presentation
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteClassLoader
 *
 * The route loader for the actions.
 */
class RouteClassLoader extends AnnotationClassLoader
{
    protected function configureRoute(
        Route $route, ReflectionClass $class,
        ReflectionMethod $method,
        object $annot
    ) {
        $route->setDefault('_action', $class);
    }

    public function loadAll(array $classes): RouteCollection
    {
        $collection = new RouteCollection();

        array_map(
            fn($class) => $collection->addCollection($this->load($class)),
            $classes
        );

        return $collection;
    }
}
