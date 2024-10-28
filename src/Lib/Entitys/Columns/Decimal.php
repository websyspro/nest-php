<?php

namespace Websyspro\NestPhp\Lib\Entitys\Columns
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Decimal
  {
    public function execute(
    ): array {
      return [
        "type" => "decimal(10,4)"
      ];
    }
  }
}
