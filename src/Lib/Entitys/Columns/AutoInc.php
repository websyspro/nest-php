<?php

namespace Websyspro\NestPhp\Lib\Entitys\Columns
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class AutoInc
  {
    public function execute(
    ): array {
      return [
        "autoinc" => true,
      ];
    }
  }
}
