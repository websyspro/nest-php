<?php

namespace Websyspro\NestPhp\Lib\Entitys\Columns
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class Varchar
  {
    public function __construct(
      private readonly int $size
    ){}

    public function execute(
    ): array {
      return [
        "type" => "varchar({$this->size})"
      ];
    }
  }
}
