<?php

namespace Websyspro\NestPhp\Lib\Dataset\Persisted\Enums
{
  enum PersistedType:string
  {
    case Created = "created";
    case Updated = "updated";
    case Deleted = "deleted";
  }
}
