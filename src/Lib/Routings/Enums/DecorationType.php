<?php

namespace Websyspro\NestPhp\Lib\Routings\Enums
{
  enum DecorationType:string
  {
    case Route = "Route";
    case Controller = "Controller";
    case Middleware = "Middleware";
    case RouteParameters = "RouteParameters";
  }
}
