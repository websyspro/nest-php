<?php

namespace Websyspro\NestPhp\Lib\Routings
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  use Websyspro\NestPhp\Lib\Reflections\ClassLoader;
  use Websyspro\NestPhp\Lib\Reflections\ClassMethods;
  use Websyspro\NestPhp\Lib\Routings\Decorations\Middlewares\Controller;
  use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;

  class ControllerDetails
  {
    public string $name;
    public array $middlewaresArr = [];
    public array $methodsArr = [];
    public array $properties = [];

    public function __construct(
      private string $controller
    ){
      $this->setController();
      $this->setMiddleares();
      $this->setControllerMethods();
    }

    private function getClassLoader(
    ): ClassLoader {
      return new ClassLoader(
        $this->controller
      );
    }

    private function setController(
    ): void {
      [ $controller ] = Utils::filter(
        $this->getClassLoader()->middlewares,
        fn( mixed $middlewares ) => (
          $middlewares->decorationType === DecorationType::Controller
        )
      );

      if ( $controller instanceof Controller ){
        $this->name = $controller->name;
      }
    }

    public function getController(
    ): string {
      return $this->name;
    }

    private function setMiddleares(
    ): void {
      $this->middlewaresArr = Utils::filter(
        $this->getClassLoader()->middlewares,
        fn( mixed $middlewares ) => (
          $middlewares->decorationType === DecorationType::Middleware
        )
      );
    }

    public function getMiddleares(
    ): array {
      return $this->middlewaresArr;
    }

    private function setControllerMethods(
    ): void {
      $this->methodsArr = Utils::mapper(
        Utils::filterKey(
          $this->getClassLoader()->methods,
          fn( string $key ) => $key !== "__construct__"
        ),
        fn( ClassMethods $classMethods, string $key ) => (
          new ControllerMethodsDetails( $classMethods )
        )
      );
    }
  }
}
