<?php declare(strict_types = 1);

namespace App\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Type\ObjectType;

class EntityClassReflectionExtension implements MethodsClassReflectionExtension
{
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

    public function getMethod(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        return $this->findMethod(
            $this->getT($classReflection),
            $methodName
        );
    }

    private function getT(ClassReflection $classReflection): ObjectType
    {
        return $classReflection
            ->getActiveTemplateTypeMap()
            ->getType('T');
    }

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
