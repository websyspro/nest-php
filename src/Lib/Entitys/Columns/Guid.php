<?php

namespace Websyspro\NestPhp\Lib\Entitys\Columns
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Guid
  {
    public function execute(
    ): array {
      return [
        "type" => "varchar(36)"
      ];
    }
  }
}
