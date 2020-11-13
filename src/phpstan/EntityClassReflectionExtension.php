<?php declare(strict_types = 1);

namespace App\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Analyser\OutOfClassScope;
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
     * @param  ClassReflection $classReflection
     * @param  string          $methodName
     * @return bool
     */
    public function hasMethod(
        ClassReflection $classReflection,
        string $methodName
    ): bool {
        if ($classReflection->getName() === 'Kwai\Core\Domain\Entity') {
            return $this->findMethod(
                $this->getT($classReflection),
                $methodName
            ) != null;
        }
        return false;
    }

    /**
     * Returns the method from the type T.
     * @param  ClassReflection  $classReflection
     * @param  string           $methodName
     * @return MethodReflection
     */
    public function getMethod(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        return $this->findMethod(
            $this->getT($classReflection),
            $methodName
        );
    }

    /**
     * T is the domain class. Return the ObjectType of T.
     * @param  ClassReflection $classReflection
     * @return ObjectType
     */
    private function getT(ClassReflection $classReflection): ObjectType
    {
        return $classReflection
            ->getActiveTemplateTypeMap()
            ->getType('T');
    }

    /**
     * Find the method on the ObjectType of T.
     * @param  ObjectType $type
     * @param  string     $method
     */
    private function findMethod(
        ObjectType $type,
        string $method
    ): ?MethodReflection {
        if (!$type->hasMethod($method)->yes()) {
            return null;
        }
        return $type->getMethod($method, new OutOfClassScope());
    }
}
