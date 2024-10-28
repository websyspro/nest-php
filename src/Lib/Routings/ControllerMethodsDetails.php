<?php

namespace Websyspro\NestPhp\Lib\Routings
{

    use Websyspro\NestPhp\Lib\Commons\Utils;
    use Websyspro\NestPhp\Lib\Reflections\ClassMethods;
    use Websyspro\NestPhp\Lib\Routings\Enums\DecorationType;

  class ControllerMethodsDetails
  {
    public string $base;
    public string $route;
    public string $routeMethod;
    public array $paths;
    public int $pathsCount;
    public bool $pathsRelative;
    public string $method;
    public array $middlewaresArr = [];
    public array $properties = [];

    public function __construct(
      ClassMethods $classMethods
    ){
      $this->setBase( $classMethods );
      $this->setRoute( $classMethods );
      $this->setMethod( $classMethods );
      $this->setProperties( $classMethods );
      $this->setMiddlewares( $classMethods );
    }

    private function getRoute(
      ClassMethods $classMethods
    ): mixed {
      $middleware = Utils::filter(
        $classMethods->getMiddlewares(),
        fn( mixed $middlewares ) => (
          $middlewares->decorationType === DecorationType::Route
        )
      );

      if ( Utils::isEmptyArray( $middleware ) === false ) {
        return Utils::shitArray(
          $middleware
        );
      }

      return null;
    }

    private function setBase(
      ClassMethods $classMethods
    ): void {
      $this->base = $classMethods->objectOrClass;
    }

    private function setRoute(
      ClassMethods $classMethods
    ): void {
      $route = $this->getRoute(
        $classMethods
      );

      if ( $classMethods->method !== "__construct" ) {
        $this->route = $route->route;
        $this->routeMethod = $route->httpType->value;
        $this->pathsRelative = preg_match(
          "/:/",
          $route->route
        );
        $this->paths = $route->route === ""
          ? [] : explode(
          "/",
          preg_replace(
            "/(^\/)|(\/$)/",
            "",
            $route->route
          )
        );
        $this->pathsCount = sizeof(
          $this->paths
        );
      } else {
        $this->route = "";
        $this->routeMethod = "";
        $this->pathsRelative = false;
        $this->pathsCount = 0;
      }
    }

    private function setMethod(
      ClassMethods $classMethods
    ): void {
      $this->method = $classMethods->method;
    }

    private function setProperties(
      ClassMethods $classMethods
    ): void {
      $this->properties = $classMethods->properties;
    }

    private function setMiddlewares(
      ClassMethods $classMethods
    ): void {
      $this->middlewaresArr = Utils::filter(
        $classMethods->getMiddlewares(),
        fn( mixed $middlewares ) => (
          $middlewares->decorationType === DecorationType::Middleware
        )
      );
    }

    public function setExecute(
    ): array {
      return [];
    }
  }
}
