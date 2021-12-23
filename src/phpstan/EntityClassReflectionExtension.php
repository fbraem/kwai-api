<?php declare(strict_types = 1);

namespace App\PHPStan;

use Kwai\Core\Domain\Entity;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Dummy\DummyMethodReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Type\ObjectType;

/**
 * Extension to check a method call on an Entity. A method call on Entity
 * is forwarded to the real domain class using magic method __call. This
 * extension will help PHPStan to find the method in the domain class.
 */
class EntityClassReflectionExtension implements MethodsClassReflectionExtension
{
    /**
     * Returns true when the class T (which is a domain class) has a method
     * with the given name.
     *
     * @param ClassReflection $classReflection
     * @param string          $methodName
     * @return bool
     */
    public function hasMethod(
        ClassReflection $classReflection,
        string $methodName
    ): bool {
        if ($classReflection->getName() !== Entity::class) {
            return false;
        }

        $templateMap = $classReflection->getActiveTemplateTypeMap();
        $templateType = $templateMap->getType('T');
        if (! $templateType instanceof ObjectType) {
            return false;
        }

        return $templateType->hasMethod($methodName)->yes();
    }

    /**
     * Returns the method from the type T.
     *
     * @param ClassReflection $classReflection
     * @param string          $methodName
     * @return MethodReflection
     * @throws MissingMethodFromReflectionException
     */
    public function getMethod(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        $templateMap = $classReflection->getActiveTemplateTypeMap();
        $templateType = $templateMap->getType('T');
        if ($templateType instanceof ObjectType) {
            $reflection = $templateType->getClassReflection();
            if ($reflection !== null) {
                return $reflection->getMethod($methodName, new OutOfClassScope());
            }
        }
        return new DummyMethodReflection($methodName);
    }
}
