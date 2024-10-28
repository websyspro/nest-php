<?php

namespace Websyspro\NestPhp\Lib\Entitys\Enums
{
  enum PropertyType:string
  {
    case Type = "type";
    case Size = "size";
    case List = "list";
    case Decs = "decs";
    case AutoInc = "autoinc";
    case Required = "required";
    case PrimaryKey = "primarykey";
    case Foreign = "foreign";
  }
}
