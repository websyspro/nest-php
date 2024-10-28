<?php

namespace Websyspro\NestPhp\Lib\Entitys\Constraints
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class PrimaryKey
  {
    public function execute(
    ): array {
      return [
        "primarykey" => "s"
      ];
    }
  }
}
