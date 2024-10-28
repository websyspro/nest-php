<?php

namespace Websyspro\NestPhp\Lib\Entitys\Consts
{
  use Websyspro\NestPhp\Lib\Entitys\Enums\PropertyEntity;
  
  class PropertyOrder
  {
    public static array $initial = [
      PropertyEntity::Id->value
    ];
    public static array $finaly = [
      PropertyEntity::Actived->value,
      PropertyEntity::ActivedBy->value,
      PropertyEntity::ActivedAt->value,
      PropertyEntity::CreatedBy->value,
      PropertyEntity::CreatedAt->value,
      PropertyEntity::UpdatedBy->value,
      PropertyEntity::UpdatedAt->value,
      PropertyEntity::Deleted->value,
      PropertyEntity::DeletedBy->value,
      PropertyEntity::DeletedAt->value
    ];
  }
}
