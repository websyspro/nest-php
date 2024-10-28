<?php

namespace Websyspro\NestPhp\Lib\Entitys\Defaults
{
  use Attribute;

  #[Attribute(Attribute::TARGET_PROPERTY)]
  class BeforeDefaultCreate
  {
    public function __construct(
      private string | object $class
    ){}

    public function execute(): mixed {
      return [
        "beforeDefaultCreate" => $this->class
      ];
    }
  }
}
