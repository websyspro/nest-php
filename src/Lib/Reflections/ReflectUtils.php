<?php

namespace Websyspro\NestPhp\Lib\Reflections
{
  use ReflectionAttribute;
  use ReflectionClass;
  use ReflectionMethod;
  use ReflectionParameter;
  use Websyspro\NestPhp\Lib\Commons\Utils;

  class ReflectUtils
  {
    public static function getReflectClass(
      string | object $class
    ): ReflectionClass {
      return new ReflectionClass(
        $class
      );
    }

    public static function getMethdos(
      string | object $class
    ): array {
      return get_class_methods(
        $class
      );
    }

    public static function getClassAttribte(
      ReflectionAttribute $reflectionAttribute
    ): ClassAttributes {
      return new ClassAttributes(
        $reflectionAttribute->getName(),
        $reflectionAttribute->getArguments()
      );
    }

    public static function getClassInstance(
      string | object $objectOrClass
    ): ClassInstance {
      return new ClassInstance(
        $objectOrClass
      );
    }

    public static function getReflectMethod(
      string | object $objectOrClass,
      string $method
    ): ReflectionMethod {
      return new ReflectionMethod(
        $objectOrClass, $method
      );
    }

    public static function isValidParameter(
      ReflectionParameter $reflectionAttribute
    ): bool {
      return Utils::IsValidArray( $reflectionAttribute->getAttributes() )
          && Utils::IsEmptyArray( $reflectionAttribute->getAttributes() ) === false;
    }
  }
}
