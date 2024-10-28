<?php

namespace Websyspro\NestPhp\Lib\Entitys\Columns
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Numeric
  {
    public function execute(
    ): array {
      return [
        "type" => "bigint"
      ];
    }
  }
}
