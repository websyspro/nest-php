<?php

namespace Websyspro\NestPhp\Lib\Reflections
{
  use ReflectionAttribute;
  use ReflectionParameter;
  use Websyspro\NestPhp\Lib\Commons\Utils;

  class ClassMethods
  {
    public array $middlewares = [];
    public mixed $properties;

    public function __construct(
      public string | object $objectOrClass,
      public string $method
    ){
      $this->setMiddlewares();
      $this->setProperties();
    }

    private function setMiddlewares(
    ): void {
      Utils::mapper( ReflectUtils::getReflectMethod(
        $this->objectOrClass, $this->method
      )->getAttributes(), fn( ReflectionAttribute $reflectionAttribute ) => (
        $this->middlewares[] = ReflectUtils::getClassAttribte( $reflectionAttribute )->new()
      ));
    }

    public function getMiddlewares(
    ): array {
      return $this->middlewares;
    }

    private function setProperties(
    ): void {
      $this->properties = Utils::mapper(
        ReflectUtils::getReflectMethod( $this->objectOrClass, $this->method )->getParameters(),
        function( ReflectionParameter $reflectionParameter ) {
          return Utils::ShitArray(
            empty( $reflectionParameter->getAttributes() ) === false
              ? Utils::mapper(
                $reflectionParameter->getAttributes(),
                fn( ReflectionAttribute $reflectionAttribute ) => (
                  ReflectUtils::getClassAttribte(
                    $reflectionAttribute
                  )->new())
                )
              : [ $reflectionParameter->getType()->getName() ]
            );
        });
    }
  }
}
