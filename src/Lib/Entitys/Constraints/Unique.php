<?php

namespace Websyspro\NestPhp\Lib\Entitys\Constraints
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Unique
  {
    public function __construct(
      private readonly int $order = 1
    ){}

    public function execute(
    ): array {
      return [
        "unique" => $this->order
      ];
    }
  }
}
