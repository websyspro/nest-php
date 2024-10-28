<?php

namespace Websyspro\NestPhp\Lib\Entitys\Indexes
{
  use Attribute;
  
  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Index
  {
    public function __construct(
      private readonly int $order = 1
    ){}

    public function execute(
    ): array {
      return [
        "index" => $this->order
      ];
    }
  }
}
