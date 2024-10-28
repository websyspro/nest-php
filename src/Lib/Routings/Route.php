<?php

namespace Websyspro\NestPhp\Lib\Routings
{
use Websyspro\NestPhp\Lib\Routings\Commons\RoutingsUtils;
  class Route
  {
    public string $api;
    public string $version;
    public string $controller;
    public string $method;
    public array $paths = [];
    public int $pathsCount = 0;
    public bool $isValid = false;

    public function __construct(
      private string $route
    ){
      $this->setStructureController();
      $this->setStrucureMethod();
      $this->setStructurePaths();
      $this->setStructureValid();
    }

    private function splitRoute(): array {
      return explode(
        "/", $this->route
      );
    }

    private function hasController(): bool {
      return sizeof(
        $this->splitRoute()
      ) >= 3 ? true : false;
    }

    private function hasRouteItems(): bool {
      return sizeof(
        $this->splitRoute()
      ) >= 3 ? true : false;
    }

    private function setStructureController(): void {
      if ( $this->hasController() === true ) {
        [ $this->api, $this->version, $this->controller ] = $this->splitRoute();
      }
    }

    private function setStrucureMethod(): void {
      $this->method = RoutingsUtils::requestMethod();
    }

    private function setStructurePaths(): void {
      if ( $this->hasRouteItems() === true ) {
        $this->paths = array_slice(
          $this->splitRoute(), 3
        );

        $this->pathsCount = sizeof(
          $this->paths
        );
      }
    }

    private function setStructureValid(): void {
      $this->isValid = $this->hasController() === true
                    && $this->hasRouteItems() === true;
    }
  }
}
