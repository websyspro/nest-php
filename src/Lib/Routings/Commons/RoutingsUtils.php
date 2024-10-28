<?php

namespace Websyspro\NestPhp\Lib\Routings\Commons
{
  use Websyspro\NestPhp\Lib\Routings\Route;

  class RoutingsUtils
  {
    private static $route;

    public static function hasRequestUri(): bool {
      return isset(
        $_SERVER[ "REQUEST_URI" ]
      ) ? true : false;
    }

    public static function parseRequestUri(
      string $requestUri
    ): Route {
      [ $requestUri ] = explode(
        "?",
        $requestUri
      );

      return isset(static::$route)
        ? static::$route : new Route(
          preg_replace(
            "/(^\/)|(\/$)/",
            "",
            $requestUri
          )
      );
    }

    public static function getRequestUri(): string {
      [ "REQUEST_URI" => $requestUri ] = $_SERVER;
      return $requestUri;
    }

    public static function requestUri(
    ): Route | null {
      if ( RoutingsUtils::hasRequestUri() === false ){
        return null;
      }

      return RoutingsUtils::parseRequestUri(
        static::getRequestUri()
      );
    }

    public static function requestMethod(
    ): string {
      [ "REQUEST_METHOD" => $requestMethod ] = $_SERVER;
      return $requestMethod;
    }
  }
}
