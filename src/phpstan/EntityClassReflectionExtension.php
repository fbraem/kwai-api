<?php declare(strict_types = 1);

namespace App\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Broker\Broker;

class EntityClassReflectionExtension implements MethodsClassReflectionExtension
{
    private $broker;

    public function setBroker(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if ($classReflection->getName() === 'Kwai\Core\Domain\Entity') {
            $domain = $classReflection->getNativeProperty('domain');
            if ($domain) {
                $type = $domain->getReadableType();
                echo var_dump($type);
                exit;
                if ($type->hasMethod($methodName)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $property = $classReflection->getNativeProperty('domain');
        return $property->getReadableType()->getMethod($methodName, new OutOfClassScope());
        // return new EntityMethodReflection($methodName, $classReflection);
    }
}
