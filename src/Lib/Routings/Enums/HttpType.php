<?php

namespace Websyspro\NestPhp\Lib\Routings\Enums
{
  enum HttpType:string
  {
    case Post = "POST";
    case Get = "GET";
    case Put = "PUT";
    case Delete = "DELETE";
    case Options = "OPTIONS";
  }
}
