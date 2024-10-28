<?php

namespace Websyspro\NestPhp\Lib\Entitys\Enums
{
  enum PropertyEntity:string
  {
    case Id = "id";
    case Actived = "actived";
    case ActivedBy = "activedBy";
    case ActivedAt = "activedAt";
    case CreatedBy = "createdBy";
    case CreatedAt = "createdAt";
    case UpdatedBy = "updatedBy";
    case UpdatedAt = "updatedAt";
    case Deleted = "deleted";
    case DeletedBy = "deletedBy";
    case DeletedAt = "deletedAt";
  }
}
