<?php

namespace Websyspro\NestPhp\Lib\Entitys\Enums
{
  enum EntityType: string
  {
    case Date = "date";
    case Time = "time";
    case Datetime = "datetime";
    case Decimal = "decimal";
    case Varchar = "varchar";
    case BigInt = "bigint";
    case Tinyint = "tinyint";
  }
}
