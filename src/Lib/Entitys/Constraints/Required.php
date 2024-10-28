<?php

namespace Websyspro\NestPhp\Lib\Entitys\Constraints
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Required
  {
    public function execute(
    ): array {
      return [
        "required" => true
      ];
    }
  }
}
