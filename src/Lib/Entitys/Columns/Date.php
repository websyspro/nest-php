<?php

namespace Websyspro\NestPhp\Lib\Entitys\Columns
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Date
  {
    public function execute(
    ): array {
      return [
        "type" => "date"
      ];
    }
  }
}
