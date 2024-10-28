<?php

namespace Websyspro\NestPhp\Lib\Dataset\Sqls\Enums
{
  enum FindType:string
  {
    case FindAnd = "and";
    case FindOr = "or";
  }
}