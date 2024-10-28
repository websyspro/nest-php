<?php

namespace Websyspro\NestPhp\Lib\Entitys\Abstract
{
  use Websyspro\NestPhp\Lib\Commons\Utils;
  
  class EntityProperty
  {
    public function __construct(
      public string $name,
      public string $type,
      EntityAutoIncList $autoincs,
      EntityRequiredList $requireds,
      EntityPrimaryKeyList $primarykeys,
    ){
      $this->setPropertyType(
        autoincs: $autoincs,
        requireds: $requireds,
        primarykeys: $primarykeys
      );
    }

    private function setPropertyType(
      EntityAutoIncList $autoincs,
      EntityRequiredList $requireds,
      EntityPrimaryKeyList $primarykeys,
    ): void {
      $this->type = trim( Utils::Join(
        array_merge([ $this->type ], [
          $requireds->isRequired($this->name) ? "not null" : "null",
          $primarykeys->isPrimaryKey($this->name) ? "primary key" : "",
          $autoincs->isAutoInc($this->name) ? "auto_increment" : ""
        ]), " "
      ));
    }
  }
}
