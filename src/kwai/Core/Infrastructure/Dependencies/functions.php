<?php

declare(strict_types=1);

use Kwai\Core\Infrastructure\Dependencies\Dependency;
use League\Container\Container;

function depends(string $name, $classOrFunction)
{
    static $container = null;
    if ($container === null) {
        $container = new Container();
    }

    if ($container->has($name)) {
        return $container->get($name);
    }

    if (is_callable($classOrFunction)) {
        $dependency = $classOrFunction();
    } elseif (class_exists($classOrFunction)) {
        $dependency = new $classOrFunction();
    } else {
        throw new InvalidArgumentException('A class or function must be passed to create the dependency');
    }

    if ($dependency instanceof Dependency) {
        $dependencyInstance = $dependency->create();
        $container->add($name, $dependencyInstance);
        return $dependencyInstance;
    }

    $container->add($name, $dependency);

    return $dependency;
}
